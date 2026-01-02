<?php

namespace App\Services;

use App\Models\Product;
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
    public function publicList(array $filters = [])
    {
        return Product::query()
            ->where('is_active', true)
            ->with(['category:id,name', 'images'])
            ->when(
                $filters['search'] ?? null,
                fn($q, $v) => $q->where('name', 'like', "%$v%")
            )
            ->when(
                $filters['category_id'] ?? null,
                fn($q, $v) => $q->where('category_id', $v)
            )
            ->latest()
            ->paginate(12)
            ->through(fn($product) => $this->transformForPublic($product));
    }

    /** Admin listing */
    public function adminList(array $filters = [])
    {
        return Product::query()
            ->with('category:id,name')
            ->latest()
            ->paginate(20)
            ->through(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'slug' => $product->slug,
                'stock' => $product->stock,
                'is_active' => $product->is_active,
                'description' => $product->description,
                'category' => $product->category?->name,
                'discount_price' => 'Rs ' . $product->discount_price,
                'effective_price' => 'Rs ' . $product->effective_price,
                'discount_percentage' => $product->discount_percentage . '%',
                'primary_image' => optional(
                    $product->images->firstWhere('is_primary', true)
                ) ? Storage::url($product->images->firstWhere('is_primary', true)->image_path) : null,
                'attributes' => $product->attributes ?? [],
            ]);
    }
    public function getLatestProducts($limit = 6)
    {
        return Product::with('category', 'images')
            ->take($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? '',
                    'price' => $product->price,
                    'discountedPrice' => $product->discount_amount,
                    'discountPercentage' => $product->discount_percentage ?? 0,
                    'mainImage' => $product->images->first()->url ?? '/img/default.png',
                    'images' => $product->images->pluck('url')->toArray(),
                    'inStock' => $product->stock > 0,
                ];
            });
    }

    /* =========================
       SINGLE PRODUCT
    ========================== */

    public function findBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'images',
                'reviews.user'
            ])
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

    private function transformForPublic(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->price,
            'effective_price' => (float) $product->effective_price,
            'discount_percentage' => $product->discount_percentage,
            'category' => $product->category?->name,
            'image' => $product->images->first()?->url,
            'attributes' => $product->attributes ?? [],
            'is_sellable' => $product->stock > 0,
        ];
    }
}
