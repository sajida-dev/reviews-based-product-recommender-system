<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $now = Carbon::now();

        return [
            'kpis' => [
                'total_users' => User::query()->count(),
                'active_users_week' => User::query()
                    ->where('updated_at', '>=', $now->copy()->subWeek())
                    ->count(),
                'total_products' => Product::query()->count(),
                'total_reviews' => Review::query()->count(),
                'avg_rating' => round((float) (Review::query()->where('is_approved', true)->avg('rating') ?? 0), 2),
                'top_category' => Category::query()
                    ->withCount('products')
                    ->orderByDesc('products_count')
                    ->first()?->name,
            ],
            'reviews_per_day' => $this->reviewsPerDay(14),
            'sentiment_distribution' => $this->sentimentDistribution(),
            'top_aspects' => $this->topAspects(8),
            'top_products' => $this->topProducts(8),
            'recent_reviews' => Review::query()
                ->with(['user:id,name,avatar', 'product:id,name,slug'])
                ->latest()
                ->limit(12)
                ->get()
                ->map(fn (Review $r) => [
                    'id' => $r->id,
                    'rating' => $r->rating,
                    'excerpt' => str($r->review ?? '')->limit(120)->value(),
                    'is_approved' => $r->is_approved,
                    'user' => $r->user ? [
                        'id' => $r->user->id,
                        'name' => $r->user->name,
                        'avatar_url' => $r->user->avatar_url,
                    ] : null,
                    'product' => $r->product?->only(['id', 'name', 'slug']),
                    'created_at' => $r->created_at?->toIso8601String(),
                ]),
            'recent_users' => User::query()
                ->latest()
                ->limit(8)
                ->get(['id', 'name', 'email', 'created_at']),
            'system' => [
                'queue_connection' => config('queue.default'),
                'failed_jobs' => DB::table('failed_jobs')->count(),
                'embedding_service_configured' => (bool) config('recommendation.embedding_service_url'),
                'qdrant_enabled' => (bool) config('recommendation.enable_qdrant'),
            ],
        ];
    }

    /**
     * @return list<array{date: string, count: int}>
     */
    private function reviewsPerDay(int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();
        $dateExpr = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at)"
            : 'DATE(created_at)';

        $rows = Review::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("{$dateExpr} as d, COUNT(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return $rows->map(fn ($r) => [
            'date' => (string) $r->d,
            'count' => (int) $r->c,
        ])->values()->all();
    }

    /**
     * @return list<array{sentiment: string, count: int}>
     */
    private function sentimentDistribution(): array
    {
        if (! DB::getSchemaBuilder()->hasTable('aspect_sentiments')) {
            return [];
        }

        return DB::table('aspect_sentiments')
            ->selectRaw('sentiment, COUNT(*) as c')
            ->groupBy('sentiment')
            ->get()
            ->map(fn ($r) => [
                'sentiment' => (string) $r->sentiment,
                'count' => (int) $r->c,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{aspect: string, count: int}>
     */
    private function topAspects(int $limit): array
    {
        if (! DB::getSchemaBuilder()->hasTable('aspect_sentiments')) {
            return [];
        }

        return DB::table('aspect_sentiments')
            ->selectRaw('aspect, COUNT(*) as c')
            ->groupBy('aspect')
            ->orderByDesc('c')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'aspect' => (string) $r->aspect,
                'count' => (int) $r->c,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{id: int, name: string, slug: string, review_count: int, avg_rating: float}>
     */
    private function topProducts(int $limit): array
    {
        return Product::query()
            ->withCount(['reviews as approved_review_count' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->having('approved_review_count', '>', 0)
            ->orderByDesc('approved_review_count')
            ->limit($limit)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'review_count' => (int) $p->approved_review_count,
                'avg_rating' => round((float) ($p->avg_rating ?? 0), 2),
            ])
            ->values()
            ->all();
    }
}
