<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(protected ProductImageService $imageService) {}

    /* =========================
       LISTING
    ========================== */

    /** Public listing */
    public function publicList(array $filters = [], ?int $userId = null)
    {
        $wishlistProductIds = collect();

        if ($userId) {
            $wishlistProductIds = Wishlist::where('user_id', $userId)
                ->pluck('product_id');
        }

        return Product::query()
            ->where('is_active', true)
            ->with(['category:id,name', 'images'])
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('category_id', $v))
            ->latest()
            ->paginate(12)
            ->through(fn(Product $product) => $this->transformForPublic($product, $wishlistProductIds));
    }

    /** Admin listing */
    public function adminList(array $filters = [])
    {
        return Product::query()
            ->with('category:id,name')
            ->latest()
            ->paginate(20)
            ->through(fn($product) => $this->transformForAdmin($product));
    }

    /** Latest products for homepage */
    public function getLatestProducts(int $limit = 6, ?int $userId = null)
    {
        $wishlistProductIds = collect();

        if ($userId) {
            $wishlistProductIds = Wishlist::where('user_id', $userId)
                ->pluck('product_id');
        }

        return Product::with(['category', 'images'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn(Product $product) => $this->transformForPublic($product, $wishlistProductIds));
    }

    /** Home page ad products */
    public function getHomeAdProducts(int $limit = 2)
    {
        return Product::where('is_active', true)
            ->with('images')
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'title' => $product->name,
                'slug' => $product->slug,
                'image' => $product->images->first()?->url ?? '/img/default.png',
                'offer' => $product->discount_percentage ?? 0,
                'button' => 'SHOP NOW',
            ]);
    }

    /* =========================
       SINGLE PRODUCT
    ========================== */

    public function findBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'images', 'reviews.user'])
            ->firstOrFail();
    }

    public function findById(int $id): Product
    {
        return Product::with(['category', 'images', 'reviews.user'])
            ->findOrFail($id);
    }

    /* =========================
       CRUD
    ========================== */

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']);
            $images = $data['images'] ?? [];
            $primary = $data['primary_image'] ?? null;
            unset($data['images'], $data['primary_image']);

            $product = Product::create($data);

            if ($images) {
                $this->imageService->store($product, $images, $primary);
            }

            return $product;
        });
    }

    public function update(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::findOrFail($id);
            $images = $data['images'] ?? null;
            $primary = $data['primary_image'] ?? null;
            unset($data['images'], $data['primary_image']);

            $product->update($data);

            if (is_array($images)) {
                $this->imageService->replace($product, $images, $primary);
            }

            return $product;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->images()->delete();
            $product->delete();
        });
    }

    /* =========================
       HELPERS
    ========================== */

    private function transformForPublic(Product $product, $wishlistProductIds): array
    {
        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'category' => $product->category->name ?? '',
            'price' => (float) $product->price,
            'discountedPrice' => (float) $product->discount_price,
            'discountPercentage' => $product->discount_percentage ?? 0,
            'mainImage' => $product->images->first()?->url ?? '/img/default.png',
            'images' => $product->images->pluck('url')->toArray(),
            'inStock' => $product->stock > 0,
            'attributes' => $product->attributes ?? [],
            'isWishlisted' => $wishlistProductIds->contains($product->id),
        ];
    }

    private function transformForAdmin(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'effective_price' => $product->effective_price,
            'stock' => $product->stock,
            'is_active' => $product->is_active,
            'description' => $product->description,
            'category' => $product->category?->name,
            'primary_image' => $product->primaryImage?->url,
            'images' => $product->images->pluck('url')->toArray(),
            'attributes' => $product->attributes ?? [],
        ];
    }
}
