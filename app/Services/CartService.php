<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartService
{
    public function addItem(int $userId, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {

            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($product->stock < $quantity) {
                throw new RuntimeException("Insufficient stock for {$product->name}");
            }

            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            $item = CartItem::withTrashed()->updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $productId],
                ['quantity' => $quantity, 'deleted_at' => null]
            );

            return $item;
        });
    }

    public function removeItem(int $userId, int $productId): bool
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        return $cart->items()->where('product_id', $productId)->delete() > 0;
    }

    public function clear(int $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();
        return $cart ? $cart->items()->delete() > 0 : false;
    }
}
