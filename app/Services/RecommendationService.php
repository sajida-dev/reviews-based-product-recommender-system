<?php

namespace App\Services;

use App\Jobs\GenerateProductEmbedding;
use App\Models\Category;
use App\Models\Embedding;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Support\VectorMath;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    public function __construct(protected EmbeddingService $embeddingService) {}

    public function getSimilarProducts(Product $product, int $limit = 5, ?User $user = null): Collection
    {
        $cacheKey = 'similar_products:'.$product->id.':'.$limit;

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($product, $limit, $user) {
            $modelVersion = (string) config('recommendation.model_version');

            $embedding = Embedding::query()
                ->where('product_id', $product->id)
                ->where('model_version', $modelVersion)
                ->first();

            if (! $embedding) {
                GenerateProductEmbedding::dispatch($product);

                $items = $this->ensureMinSimilarProducts(
                    $product,
                    $this->getFallbackSimilarProducts($product, $limit),
                    $limit,
                );

                return $this->withPresentationMeta(
                    $items,
                    $user,
                    sourceProduct: $product,
                );
            }

            $vector = $embedding->embedding;
            if (! is_array($vector) || $vector === []) {
                $items = $this->ensureMinSimilarProducts(
                    $product,
                    $this->getFallbackSimilarProducts($product, $limit),
                    $limit,
                );

                return $this->withPresentationMeta(
                    $items,
                    $user,
                    sourceProduct: $product,
                );
            }

            if (config('recommendation.enable_qdrant')) {
                try {
                    /** @var \App\Services\QdrantHttpClient $client */
                    $client = app('qdrant.client');
                    $res = $client->search(
                        (string) config('recommendation.qdrant_collection'),
                        $vector,
                        $limit + 5,
                    );
                    $rows = $res['result'] ?? [];
                    $scoresById = [];
                    $ids = [];
                    foreach ($rows as $row) {
                        $pid = isset($row['id']) ? (int) $row['id'] : null;
                        if ($pid === null || $pid === (int) $product->id) {
                            continue;
                        }
                        $ids[] = $pid;
                        $scoresById[$pid] = (float) ($row['score'] ?? 0);
                        if (count($ids) >= $limit) {
                            break;
                        }
                    }

                    if ($ids !== []) {
                        $products = $this->orderByIdList(
                            Product::query()
                                ->whereIn('id', $ids)
                                ->where('is_active', true)
                                ->with(['category', 'images', 'reviews'])
                                ->get(),
                            $ids,
                        );

                        return $this->withPresentationMeta($products, $user, $scoresById, $product);
                    }
                } catch (\Throwable $e) {
                    Log::error('Qdrant search failed: '.$e->getMessage());
                }
            }

            $products = $this->similarByStoredEmbeddings($product, $vector, $limit);

            if ($products->isEmpty()) {
                $products = $this->getFallbackSimilarProducts($product, $limit);
            }

            $products = $this->ensureMinSimilarProducts($product, $products, $limit);

            return $this->withPresentationMeta($products, $user, sourceProduct: $product);
        });
    }

    /**
     * Guest / cold-start feed: popular & trending in preferred categories (e.g. electronics).
     *
     * @param  list<int>  $excludeProductIds
     */
    public function getColdStartRecommendationsForGuest(int $limit = 10, array $excludeProductIds = []): Collection
    {
        $exclude = array_values(array_unique(array_map('intval', $excludeProductIds)));
        $prefer = $this->preferredColdStartCategoryId();
        $products = $this->queryColdStartProducts($limit, $exclude, $prefer);

        return $this->withPresentationMeta(
            $products,
            null,
            [],
            null,
            false,
            'trending',
        );
    }

    public function getPersonalizedRecommendations(User $user, int $limit = 10): Collection
    {
        $modelVersion = (string) config('recommendation.model_version');
        $profile = $user->profile;
        $vector = $profile?->interests_vector;

        if (is_array($vector) && $vector !== [] && config('recommendation.enable_qdrant')) {
            try {
                /** @var \App\Services\QdrantHttpClient $client */
                $client = app('qdrant.client');
                $res = $client->search(
                    (string) config('recommendation.qdrant_collection'),
                    $vector,
                    $limit + 15,
                );
                $rows = $res['result'] ?? [];
                $exclude = $this->productIdsToExcludeForUser($user);
                $ids = [];
                $scoresById = [];
                foreach ($rows as $row) {
                    $pid = isset($row['id']) ? (int) $row['id'] : null;
                    if ($pid === null || in_array($pid, $exclude, true)) {
                        continue;
                    }
                    $ids[] = $pid;
                    $scoresById[$pid] = (float) ($row['score'] ?? 0);
                    if (count($ids) >= $limit) {
                        break;
                    }
                }

                if ($ids !== []) {
                    $liked = $this->referenceProductForUser($user);

                    return $this->withPresentationMeta(
                        $this->orderByIdList(
                            Product::query()
                                ->whereIn('id', $ids)
                                ->where('is_active', true)
                                ->with(['category', 'images', 'reviews'])
                                ->get(),
                            $ids,
                        ),
                        $user,
                        $scoresById,
                        $liked,
                        recommendedForYou: true,
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Qdrant personalized search failed: '.$e->getMessage());
            }
        }

        if (is_array($vector) && $vector !== []) {
            $exclude = $this->productIdsToExcludeForUser($user);
            $products = $this->personalizedByStoredEmbeddings($user, $vector, $limit, $exclude, $modelVersion);
            if ($products->isNotEmpty()) {
                $liked = $this->referenceProductForUser($user);

                return $this->withPresentationMeta(
                    $products,
                    $user,
                    sourceProduct: $liked,
                    recommendedForYou: true,
                );
            }
        }

        $exclude = $this->productIdsToExcludeForUser($user);
        $prefer = $this->preferredColdStartCategoryId();
        $products = $this->queryColdStartProducts($limit, $exclude, $prefer);

        return $this->withPresentationMeta(
            $products,
            $user,
            [],
            null,
            true,
            'for_you',
        );
    }

    /**
     * @param  list<int>  $idOrder
     */
    private function orderByIdList(Collection $products, array $idOrder): Collection
    {
        $pos = array_flip($idOrder);

        return $products->sortBy(fn (Product $p) => $pos[$p->id] ?? PHP_INT_MAX)->values();
    }

    /**
     * @param  array<int, float>  $scoresById
     */
    private function withPresentationMeta(
        Collection $products,
        ?User $user,
        array $scoresById = [],
        ?Product $sourceProduct = null,
        bool $recommendedForYou = false,
        ?string $badgeOverride = null,
    ): Collection {
        $userAspects = $user ? $this->userTopAspects($user) : [];

        return $products->map(function (Product $p) use ($scoresById, $sourceProduct, $recommendedForYou, $userAspects, $badgeOverride) {
            $p->setAttribute('top_aspects', $this->topAspectsForProduct((int) $p->id));
            if ($scoresById !== []) {
                $p->setAttribute('similarity_score', $scoresById[$p->id] ?? null);
            }
            if ($sourceProduct) {
                $p->setAttribute('because_you_liked', [
                    'id' => $sourceProduct->id,
                    'name' => $sourceProduct->name,
                    'slug' => $sourceProduct->slug,
                ]);
            }
            $p->setAttribute('matching_aspects', $this->matchingAspects($p, $userAspects));
            $p->setAttribute('recommended_for_you', $recommendedForYou);

            if ($badgeOverride !== null) {
                $badge = $badgeOverride;
            } elseif ($recommendedForYou) {
                $badge = 'for_you';
            } elseif ($p->getAttribute('similarity_score') !== null
                || ($scoresById !== [] && array_key_exists($p->id, $scoresById))) {
                $badge = 'similar';
            } else {
                $badge = 'trending';
            }
            $p->setAttribute('recommendation_badge', $badge);

            return $p;
        });
    }

    /**
     * @return list<array{aspect: string, sentiment: string, label: string}>
     */
    private function topAspectsForProduct(int $productId, int $limit = 2): array
    {
        $rows = \App\Models\AspectSentiment::query()
            ->where('product_id', $productId)
            ->selectRaw('aspect, sentiment, AVG(confidence) as conf')
            ->groupBy('aspect', 'sentiment')
            ->orderByDesc('conf')
            ->limit($limit)
            ->get();

        return $rows->map(function ($r) {
            $emoji = match ($r->sentiment) {
                'positive' => '👍',
                'negative' => '👎',
                default => '➖',
            };

            return [
                'aspect' => (string) $r->aspect,
                'sentiment' => (string) $r->sentiment,
                'label' => ucfirst((string) $r->aspect).' '.$emoji,
            ];
        })->values()->all();
    }

    /**
     * @return list<string>
     */
    private function userTopAspects(User $user, int $limit = 8): array
    {
        return \App\Models\AspectSentiment::query()
            ->whereHas('review', fn ($q) => $q->where('user_id', $user->id))
            ->selectRaw('aspect, MAX(confidence) as c')
            ->groupBy('aspect')
            ->orderByDesc('c')
            ->limit($limit)
            ->pluck('aspect')
            ->map(fn ($a) => strtolower((string) $a))
            ->all();
    }

    /**
     * @param  list<string>  $userAspectsLower
     * @return list<string>
     */
    private function matchingAspects(Product $product, array $userAspectsLower): array
    {
        if ($userAspectsLower === []) {
            return [];
        }
        $productAspects = \App\Models\AspectSentiment::query()
            ->where('product_id', $product->id)
            ->selectRaw('aspect, MAX(confidence) as c')
            ->groupBy('aspect')
            ->orderByDesc('c')
            ->limit(12)
            ->pluck('aspect')
            ->map(fn ($a) => strtolower((string) $a))
            ->all();

        return array_values(array_intersect($productAspects, $userAspectsLower));
    }

    private function referenceProductForUser(User $user): ?Product
    {
        $review = $user->reviews()->latest()->with('product')->first();

        return $review?->product;
    }

    /**
     * @return list<int>
     */
    private function productIdsToExcludeForUser(User $user): array
    {
        $fromReviews = $user->reviews()->pluck('product_id')->all();
        $fromOrders = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->pluck('product_id')
            ->all();

        return array_values(array_unique(array_merge($fromReviews, $fromOrders)));
    }

    private function similarByStoredEmbeddings(Product $product, array $vector, int $limit): Collection
    {
        $modelVersion = (string) config('recommendation.model_version');
        $max = (int) config('recommendation.local_similarity_max_candidates', 500);

        $query = Embedding::query()
            ->where('model_version', $modelVersion)
            ->where('product_id', '!=', $product->id)
            ->whereHas('product', function ($q) use ($product) {
                $q->where('is_active', true)
                    ->when($product->category_id, fn ($q2) => $q2->where('category_id', $product->category_id));
            })
            ->with(['product.category', 'product.images', 'product.reviews']);

        $candidates = $query->limit($max)->get();

        if ($candidates->isEmpty()) {
            $candidates = Embedding::query()
                ->where('model_version', $modelVersion)
                ->where('product_id', '!=', $product->id)
                ->whereHas('product', fn ($q) => $q->where('is_active', true))
                ->with(['product.category', 'product.images', 'product.reviews'])
                ->limit($max)
                ->get();
        }

        $scored = [];
        foreach ($candidates as $row) {
            $emb = $row->embedding;
            if (! is_array($emb) || $emb === []) {
                continue;
            }
            $scored[] = [
                'score' => VectorMath::cosineSimilarity($vector, $emb),
                'product' => $row->product,
            ];
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $out = collect();
        $scoresById = [];
        foreach (array_slice($scored, 0, $limit) as $item) {
            if ($item['product'] instanceof Product) {
                $scoresById[$item['product']->id] = $item['score'];
                $item['product']->setAttribute('similarity_score', $item['score']);
                $out->push($item['product']);
            }
        }

        return $out;
    }

    /**
     * @param  list<int>  $exclude
     */
    private function personalizedByStoredEmbeddings(User $user, array $vector, int $limit, array $exclude, string $modelVersion): Collection
    {
        $max = (int) config('recommendation.local_similarity_max_candidates', 500);

        $candidates = Embedding::query()
            ->where('model_version', $modelVersion)
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->with(['product.category', 'product.images', 'product.reviews'])
            ->limit($max)
            ->get();

        $scored = [];
        foreach ($candidates as $row) {
            $pid = (int) $row->product_id;
            if (in_array($pid, $exclude, true)) {
                continue;
            }
            $emb = $row->embedding;
            if (! is_array($emb) || $emb === []) {
                continue;
            }
            $scored[] = [
                'score' => VectorMath::cosineSimilarity($vector, $emb),
                'product' => $row->product,
            ];
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $out = collect();
        foreach (array_slice($scored, 0, $limit) as $item) {
            if ($item['product'] instanceof Product) {
                $item['product']->setAttribute('similarity_score', $item['score']);
                $out->push($item['product']);
            }
        }

        return $out;
    }

    private function getFallbackSimilarProducts(Product $product, int $limit): Collection
    {
        $fromCategory = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['category', 'images', 'reviews'])
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        if ($fromCategory->count() >= $limit) {
            return $fromCategory;
        }

        $exclude = array_merge([$product->id], $fromCategory->pluck('id')->all());
        $need = $limit - $fromCategory->count();
        $extra = $this->queryColdStartProducts($need, $exclude, $product->category_id);

        return $fromCategory->concat($extra)->unique('id')->take($limit)->values();
    }

    /**
     * Fill up to $limit when the catalog is sparse (single product, empty category, etc.).
     */
    private function ensureMinSimilarProducts(Product $current, Collection $products, int $limit): Collection
    {
        if ($products->count() >= $limit) {
            return $products->take($limit)->values();
        }

        $exclude = array_merge([$current->id], $products->pluck('id')->all());
        $need = $limit - $products->count();
        $extra = $this->queryColdStartProducts($need, $exclude, $current->category_id);

        return $products->concat($extra)->unique('id')->take($limit)->values();
    }

    private function preferredColdStartCategoryId(): ?int
    {
        $slugs = config('recommendation.cold_start_category_slugs', []);
        if ($slugs === [] || ! is_array($slugs)) {
            return null;
        }

        return Category::query()->whereIn('slug', $slugs)->value('id');
    }

    /**
     * @param  list<int>  $excludeIds
     */
    private function queryColdStartProducts(int $limit, array $excludeIds, ?int $preferCategoryId = null): Collection
    {
        $excludeIds = array_values(array_filter($excludeIds));

        return Product::query()
            ->where('is_active', true)
            ->when($excludeIds !== [], fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->with(['category', 'images', 'reviews'])
            ->withCount('orders')
            ->withAvg(['reviews as avg_rating_approved' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->when($preferCategoryId, function ($q) use ($preferCategoryId) {
                $q->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$preferCategoryId]);
            })
            ->orderByDesc('views')
            ->orderByDesc('orders_count')
            ->orderByDesc('avg_rating_approved')
            ->limit($limit)
            ->get();
    }
}
