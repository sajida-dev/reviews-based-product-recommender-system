<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateProductEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(public Product $product)
    {
        // Ensure product is a fresh instance
        $this->product = $this->product->fresh();
    }

    public function handle(EmbeddingService $embeddingService): void
    {
        try {
            $embedding = $embeddingService->generateProductEmbedding($this->product);

            if ($embedding) {
                Log::info("Embedding generated for product: {$this->product->id}");
            } else {
                Log::warning("Embedding generation failed for product: {$this->product->id}");
            }
        } catch (\Exception $e) {
            Log::error('GenerateProductEmbedding job failed: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
