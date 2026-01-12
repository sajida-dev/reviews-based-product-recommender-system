<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct() {}

    /* 
       LISTING
     */

    /** Public listing */
    public function publicList(array $filters = [], ?int $userId = null)
    {
        return Product::query()
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->latest()
            ->paginate(12)
            ->through(fn(Product $product) => $this->transformForPublic($product));
    }

    /** Admin listing */
    public function adminList(array $filters = [])
    {
        return Product::query()
            ->latest()
            ->paginate(20)
            ->through(fn($product) => $this->transformForAdmin($product));
    }

    /** Latest products for homepage */
    public function getLatestProducts(int $limit = 6, ?int $userId = null)
    {

        return Product::latest()
            ->take($limit)
            ->get()
            ->map(fn(Product $product) => $this->transformForPublic($product));
    }



    /* 
       SINGLE PRODUCT
     */



    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    /* 
       CRUD
     */

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create($data);
            return $product;
        });
    }

    public function update(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::findOrFail($id);
            $product->update($data);
            return $product;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->delete();
        });
    }

    /* 
       HELPERS
     */

    private function transformForPublic(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'stock_quantity' => $product->stock_quantity,
            'inStock' => $product->stock_quantity > 5,
        ];
    }

    private function transformForAdmin(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
            'inStock' => $product->stock_quantity > 5,
        ];
    }
}
