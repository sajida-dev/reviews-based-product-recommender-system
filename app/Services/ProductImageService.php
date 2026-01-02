<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function store(Product $product, array $files, ?int $primaryIndex): void
    {
        foreach ($files as $index => $file) {
            /** @var UploadedFile $file */
            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $index === $primaryIndex,
            ]);
        }
    }

    public function replace(Product $product, array $files, ?int $primaryIndex): void
    {
        $this->deleteAll($product);
        $this->store($product, $files, $primaryIndex);
    }

    public function deleteAll(Product $product): void
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->images()->delete();
    }
}
