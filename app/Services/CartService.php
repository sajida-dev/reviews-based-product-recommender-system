<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartService
{
    public function getAll(int $userId)
    {
        return CartItem::with(['product.primaryImage'])
            ->whereHas('cart', fn($q) => $q->where('user_id', $userId))
            ->get()
            ->map(function (CartItem $item) {
                $product = $item->product;
                return [
                    'id' => $item->id,
                    'name' => $product->name ?? 'Unknown',
                    'price' => $product->price ?? 0,
                    'quantity' => $item->quantity,
                    'image' => $product->primaryImage?->url,
                ];
            });
    }

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
    public function updateItemQuantity(int $userId, int $itemId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $itemId, $quantity) {
            $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $userId))
                ->findOrFail($itemId);

            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            if ($product->stock < $quantity) {
                throw new \RuntimeException("Insufficient stock for {$product->name}");
            }

            $item->quantity = $quantity;
            $item->save();

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
