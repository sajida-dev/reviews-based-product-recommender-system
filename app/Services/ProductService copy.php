<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected ProductImageService $imageService)
    {
        //
    }



    /** List products with relationships */
    public function list(array $filters = [])
    {
        return Product::query()
            ->with(['category:id,name', 'images:id,product_id,image_path,is_primary'])
            ->when(
                filled($filters['search'] ?? null),
                fn($q) => $q->where('name', 'like', '%' . $filters['search'] . '%')
            )
            ->when(
                filled($filters['category_id'] ?? null),
                fn($q) => $q->where('category_id', $filters['category_id'])
            )
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /** Get single product with category, images, and approved reviews */
    public function findOrFail(int $id)
    {
        return Product::with([
            'category',
            'images',
            'reviews' => function ($query) {
                $query->where('is_approved', true)
                    ->with('user')
                    ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
    }


    /** Create product with transaction & images */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $images = $data['images'] ?? [];
            $primaryIndex = $data['primary_image'] ?? null;

            unset($data['images'], $data['primary_image']);
            $data['slug'] = Str::slug($data['name']);

            $product = Product::create($data);

            if ($images) {
                $this->imageService->store(
                    $product,
                    $images,
                    $primaryIndex
                );
            }

            return $product->load('images', 'category');
        });
    }

    /** Update product safely */
    public function update(int $id, array $data)
    {
        return DB::transaction(
            function () use ($id, $data) {

                $product = Product::lockForUpdate()->findOrFail($id);

                $images = $data['images'] ?? null;
                $primaryIndex = $data['primary_image'] ?? null;

                unset($data['images'], $data['primary_image']);

                $product->update($data);

                if (is_array($images)) {
                    $this->imageService->replace(
                        $product,
                        $images,
                        $primaryIndex
                    );
                }
                return $product->load('images', 'category');
            }
        );
    }

    /** Delete product safely */
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->images()->delete();
            $product->delete();
        });
    }
}
