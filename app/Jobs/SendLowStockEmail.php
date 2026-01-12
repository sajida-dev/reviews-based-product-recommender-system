<?php

namespace App\Jobs;

use App\Mail\LowStockAlert;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowStockEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;
    public $tries = 3;          // retry 3 times on failure
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = 'admin@example.com';

        Mail::to($adminEmail)->queue(new LowStockAlert([
            'productName' => $this->product->name,
            'productStock' => $this->product->stock,
            'productUrl' => url("/admin/products/{$this->product->id}"),
            'productImage' => $this->product->images->first()?->url ?? '/img/default.png',
        ]));
    }
}
