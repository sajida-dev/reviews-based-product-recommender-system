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
        return Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Get all wishlist items for user.
     */
    public function getAll(int $userId)
    {
        return Wishlist::with('product')
            ->where('user_id', $userId)
            ->get();
    }
}
