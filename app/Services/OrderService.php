<?php

namespace App\Services;

use App\Events\ProductStockUpdated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class OrderService
{
    /**
     * Checkout cart into an order (atomic & safe)
     */
    public function checkout(int $userId, array $addresses): Order
    {
        return DB::transaction(function () use ($userId, $addresses) {

            $cart = Cart::with('items.product')
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new RuntimeException('Cart is empty');
            }

            $subtotal = 0;
            $total = 0;

            // Validate stock availability (but don't decrement yet - wait for payment)
            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw new RuntimeException(
                        "Insufficient stock for {$product->name}"
                    );
                }

                $price = $product->finalPrice();
                $lineTotal = $price * $item->quantity;

                $subtotal += $product->price * $item->quantity;
                $total += $lineTotal;
            }

            $order = Order::create([
                'user_id'          => $userId,
                'order_number'     => Order::generateNumber(),
                'subtotal'         => $subtotal,
                'discount'         => $subtotal - $total,
                'tax'              => 0,
                'total'            => $total,
                'status'           => 'pending',
                'shipping_address' => $addresses['shipping'],
                'billing_address'  => $addresses['billing'],
            ]);

            // Create order items (stock will be decremented after payment)
            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'price'      => $product->finalPrice(),
                ]);
            }

            // Don't clear cart yet - wait for payment confirmation
            // Cart will be cleared in processPayment after successful payment

            return $order->load('items.product');
        });
    }

    /**
     * Process payment for an order
     */
    public function processPayment(int $userId, int $orderId, string $paymentMethod, array $paymentData = []): Order
    {
        return DB::transaction(function () use ($userId, $orderId, $paymentMethod, $paymentData) {
            $order = Order::with('items.product')
                ->where('user_id', $userId)
                ->where('id', $orderId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->payment_status === 'completed') {
                throw new RuntimeException('Order has already been paid.');
            }

            if ($order->status === 'cancelled') {
                throw new RuntimeException('Cannot process payment for a cancelled order.');
            }

            // Simulate payment processing
            // In production, integrate with payment gateway (Stripe, PayPal, etc.)
            $paymentSuccess = $this->simulatePaymentProcessing($paymentMethod, $paymentData);

            if ($paymentSuccess) {
                // Decrement stock only after successful payment
                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        // Double-check stock availability
                        if ($product->stock < $item->quantity) {
                            throw new RuntimeException(
                                "Insufficient stock for {$product->name}. Stock may have changed."
                            );
                        }
                        $product->decrement('stock', $item->quantity);
                        $product->refresh();
                        ProductStockUpdated::dispatch($product);
                    }
                }

                // Clear cart only after successful payment
                $cart = Cart::where('user_id', $userId)->first();
                if ($cart) {
                    $cart->items()->delete();
                }

                $order->update([
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'completed',
                    'payment_transaction_id' => 'TXN-' . strtoupper(uniqid()),
                    'paid_at' => now(),
                    'status' => 'processing', // Move order to processing after payment
                ]);
            } else {
                $order->update([
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'failed',
                ]);
                throw new RuntimeException('Payment processing failed. Please try again.');
            }

            return $order->load('items.product');
        });
    }

    /**
     * Simulate payment processing
     * In production, replace this with actual payment gateway integration
     */
    protected function simulatePaymentProcessing(string $paymentMethod, array $paymentData): bool
    {
        // Simulate payment processing delay
        usleep(500000); // 0.5 seconds

        // For demo purposes, always succeed except for specific test cases
        // In production, integrate with Stripe, PayPal, etc.
        if (isset($paymentData['card_number']) && $paymentData['card_number'] === '4000000000000002') {
            return false; // Simulate card decline
        }

        return true;
    }
}
