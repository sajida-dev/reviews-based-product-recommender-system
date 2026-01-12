<?php

namespace App\Listeners;

use App\Events\ProductStockUpdated;
use App\Jobs\SendLowStockEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowStockNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductStockUpdated $event): void
    {
        $product = $event->product;

        $lowStockThreshold = config('inventory.low_stock_threshold', 5);

        if ($product->stock <= $lowStockThreshold) {
            SendLowStockEmail::dispatch($product);
        }
    }
}
