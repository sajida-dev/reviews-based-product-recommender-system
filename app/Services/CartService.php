<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            Log::info("Attempting to add product to cart", [
                'user_id'          => $userId,
                'product_id'       => $productId,
                'product_stock'    => $product->stock,
                'requested_qty'    => $quantity,
            ]);

            if ($product->stock < $quantity) {
                Log::warning("Insufficient stock for product", [
                    'product_id' => $productId,
                    'stock'      => $product->stock,
                    'requested'  => $quantity,
                ]);
                throw new RuntimeException("Insufficient stock for {$product->name}");
            }

            $cart = Cart::firstOrCreate(['user_id' => $userId]);
            Log::info("Cart retrieved/created", ['cart_id' => $cart->id]);

            $cartItem = CartItem::withTrashed()
                ->where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                if ($cartItem->trashed()) {
                    $cartItem->restore();
                }
                $cartItem->quantity = $quantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }
            Log::info("CartItem added or updated", [
                'cart_item_id' => $cartItem->id,
                'cart_id'      => $cart->id,
                'product_id'   => $productId,
                'quantity'     => $quantity,
                'was_trashed'  => $cartItem->trashed(),
            ]);

            return $cartItem;
        });
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param int $userId
     * @param int $itemId
     * @param int $quantity
     * @return CartItem
     * @throws RuntimeException
     */
    public function updateItemQuantity(int $userId, int $itemId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $itemId, $quantity) {

            // Fetch the CartItem belonging to the user's cart
            $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $userId))
                ->findOrFail($itemId);

            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            Log::info("Updating cart item quantity", [
                'cart_item_id'  => $item->id,
                'user_id'       => $userId,
                'product_id'    => $product->id,
                'old_quantity'  => $item->quantity,
                'new_quantity'  => $quantity,
                'stock_available' => $product->stock,
            ]);

            // Stock validation
            if ($product->stock < $quantity) {
                Log::warning("Insufficient stock to update quantity", [
                    'product_id' => $product->id,
                    'stock'      => $product->stock,
                    'requested'  => $quantity,
                ]);
                throw new RuntimeException("Insufficient stock for {$product->name}");
            }

            $item->quantity = $quantity;
            $item->save();

            Log::info("Cart item quantity updated successfully", [
                'cart_item_id' => $item->id,
                'quantity'     => $item->quantity,
            ]);

            return $item;
        });
    }

    /**
     * Remove a product from the user's cart.
     *
     * @param int $userId
     * @param int $itemId
     * @return bool
     */
    public function removeItem(int $userId, int $itemId): bool
    {
        $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $userId))
            ->find($itemId);

        if (!$item) return false;

        $deleted = $item->delete();

        Log::info("Removed product from cart", [
            'user_id'    => $userId,
            'cart_item_id' => $itemId,
            'deleted'    => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Clear all items from the user's cart.
     *
     * @param int $userId
     * @return bool
     */
    public function clear(int $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            Log::info("Clear cart: no cart found for user", ['user_id' => $userId]);
            return false;
        }

        $deleted = $cart->items()->delete() > 0;

        Log::info("Cleared cart items", [
            'user_id' => $userId,
            'cart_id' => $cart->id,
            'deleted_count' => $deleted,
        ]);

        return $deleted;
    }
}
