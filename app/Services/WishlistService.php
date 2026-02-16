<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WishlistService
{
    /**
     * Add product to wishlist.
     * Restores soft-deleted item if it already exists.
     */
    public function add(int $userId, int $productId): Wishlist
    {
        return DB::transaction(function () use ($userId, $productId) {

            $wishlistItem = Wishlist::withTrashed()
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            Log::info('Wishlist add attempt', [
                'user_id' => $userId,
                'product_id' => $productId,
                'existing_id' => $wishlistItem?->id,
                'trashed' => $wishlistItem?->trashed(),
            ]);

            if ($wishlistItem) {
                if ($wishlistItem->trashed()) {
                    $wishlistItem->restore();

                    Log::info('Wishlist item restored', [
                        'wishlist_item_id' => $wishlistItem->id,
                    ]);
                }

                return $wishlistItem;
            }

            Log::info('Creating new wishlist item', [
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        });
    }

    /**
     * Remove product from wishlist (soft delete).
     */
    public function remove(int $userId, int $productId): bool
    {
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        Log::info('Wishlist remove attempt', [
            'user_id' => $userId,
            'product_id' => $productId,
            'wishlist_item_id' => $wishlistItem?->id,
        ]);

        if (! $wishlistItem) {
            return false;
        }

        $wishlistItem->delete();

        Log::info('Wishlist item removed', [
            'wishlist_item_id' => $wishlistItem->id,
        ]);

        return true;
    }

    /**
     * Get all wishlist items for a user.
     */
    public function getAll(int $userId): Collection
    {
        return Wishlist::with(['product.primaryImage', 'product.category'])
            ->where('user_id', $userId)
            ->get()
            ->filter(fn($item) => $item->product) 
            ->map(function ($item) {
                $product = $item->product;

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => (float) $product->price,
                    'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                    'effective_price' => (float) $product->effective_price,
                    'discount_percentage' => $product->discount_percentage ?? 0,
                    'stock' => $product->stock,
                    'is_in_stock' => $product->is_in_stock,
                    'is_sellable' => $product->is_sellable,
                    'image' => $product->primaryImage?->url ?? '/images/fallback.png',
                    'category' => $product->category?->name ?? '',
                ];
            })
            ->values();
    }
}
