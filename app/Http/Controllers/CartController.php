<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Services\CartService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}

    public function index(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->first() ?? new Cart();
        Log::info('Rendering cart page', [
            'user_id' => $request->user()->id,
            'cart_id' => $cart->id ?? null,
        ]);
        return Inertia::render('Cart/Index', ['cart' => $cart]);
    }

    public function store(CartRequest $request)
    {
        try {
            $item = $this->service->addItem(
                $request->user()->id,
                $request->product_id,
                $request->quantity
            );
            Log::info('Product added to cart successfully', [
                'user_id'    => $request->user()->id,
                'cart_item_id' => $item->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
            return back()->with(['cart_item' => $item, 'success' => 'Product added to cart successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to add product to cart', [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
    public function updateQty(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        try {
            $this->service->updateItemQuantity($request->user()->id, $itemId, $request->quantity);
            Log::info('Cart item quantity updated', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'new_quantity' => $request->quantity,
            ]);

            return back()->with('success', 'Cart updated successfully');
        } catch (\Throwable $e) {
            report($e);
            Log::error('Failed to update cart item quantity', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'requested_quantity' => $request->quantity,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors($e->getMessage());
        }
    }


    public function destroy(Request $request, int $itemId)
    {
        try {
            $deleted = $this->service->removeItem(
                $request->user()->id,
                $itemId
            );

            Log::info('Product removed from cart', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'deleted' => $deleted,
            ]);

            $message = $deleted ? 'Product removed from cart.' : 'Product not found in cart.';
            return back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Failed to remove product from cart', [
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }
}
