<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WishlistService
{
    /**
     * Add product to wishlist.
     */
    public function add(int $userId, int $productId): Wishlist
    {
        return DB::transaction(function () use ($userId, $productId) {
            return Wishlist::withTrashed()->updateOrCreate(
                ['user_id' => $userId, 'product_id' => $productId],
                ['deleted_at' => null]
            );
        });
    }

    /**
     * Remove product from wishlist.
     */
    public function remove(int $userId, int $productId): bool
    {
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return true;
        }

        return false;
    }



    /**
     * Get all wishlist items for user.
     */
    public function getAll(int $userId)
    {
        return Wishlist::with(['product.primaryImage'])
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                return [
                    'id' => $item->id,
                    'name' => $product->name ?? 'Unknown',
                    'price' => $product->price ?? 0,
                    'image' => $product->primaryImage?->url ?? '/images/fallback.png',
                ];
            });
    }
}
