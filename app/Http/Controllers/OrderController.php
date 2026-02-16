<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\PaymentRequest;
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

            // Redirect to payment page instead of order confirmation
            return redirect()
                ->route('payment.show', $order->id)
                ->with('success', 'Please complete payment to confirm your order.');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function showPayment(\Illuminate\Http\Request $request, int $orderId)
    {
        $user = $request->user();
        $order = Order::with('items.product.primaryImage')
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->firstOrFail();

        // Don't allow payment if already paid
        if ($order->payment_status === 'completed') {
            return redirect()
                ->route('orders.show', $order->id)
                ->with('info', 'This order has already been paid.');
        }

        return Inertia::render('Payment/Index', [
            'order' => $order,
        ]);
    }

    public function processPayment(PaymentRequest $request)
    {
        try {
            $user = $request->user();
            $order = $this->service->processPayment(
                $user->id,
                $request->order_id,
                $request->payment_method,
                $request->only(['card_number', 'card_holder_name', 'expiry_month', 'expiry_year', 'cvv'])
            );

            return redirect()
                ->route('payment.success', $order->id)
                ->with('success', 'Payment processed successfully!');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function paymentSuccess(\Illuminate\Http\Request $request, int $orderId)
    {
        $user = $request->user();
        $order = Order::with('items.product')
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->firstOrFail();

        return Inertia::render('Payment/Success', [
            'order' => $order,
        ]);
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
