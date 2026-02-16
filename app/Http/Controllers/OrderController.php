<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\OrderService;
use Inertia\Inertia;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service

    ) {}

    public function showCheckout(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product.primaryImage')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return Inertia::render('Checkout/Index', [
            'user' => $user->only(['name', 'email']),
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        try {
            $user = $request->user();
            $order = $this->service->checkout(
                $user->id,
                [
                    'shipping' => $request->shipping_address,
                    'billing'  => $request->billing_address,
                ]
            );

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Order placed successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function show(int $id)
    {
        $user = request()->user();
        $order = Order::with('items.product')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
