<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Embedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    /**
     * Generate embedding for a product via embedding microservice and store it
     * Returns embedding array or null on failure
     */
    public function generateProductEmbedding(Product $product): ?array
    {
        $text = $this->prepareProductText($product);

        try {
            $url = config('recommendation.embedding_service_url') . '/embed';
            $response = Http::timeout(30)->post($url, ['text' => $text]);

            if ($response->successful()) {
                $embedding = $response->json('embedding');

                if (is_array($embedding) && count($embedding) > 0) {
                    $modelVersion = config('recommendation.model_version');

                    $record = Embedding::updateOrCreate(
                        ['product_id' => $product->id, 'model_version' => $modelVersion],
                        [
                            'embedding' => $embedding,
                            'dimension' => count($embedding),
                            'metadata' => [
                                'product_name' => $product->name,
                                'category_id' => $product->category_id,
                            ]
                        ]
                    );

                    // Optionally push to Qdrant in background
                    if (config('recommendation.enable_qdrant')) {
                        try {
                            $client = app('qdrant.client');
                            $client->upsertPoints(
                                collection: config('recommendation.qdrant_collection'),
                                points: [[
                                    'id' => $product->id,
                                    'vector' => $embedding,
                                    'payload' => [
                                        'product_id' => $product->id,
                                        'name' => $product->name,
                                        'category_id' => $product->category_id,
                                    ],
                                ]]
                            );
                        } catch (\Exception $e) {
                            Log::error('Qdrant upsert failed: ' . $e->getMessage());
                        }
                    }

                    return $embedding;
                }
            }

            Log::warning('Embedding service returned non-successful response for product ' . $product->id);
        } catch (\Exception $e) {
            Log::error('Embedding service error: ' . $e->getMessage());
        }

        return null;
    }

    private function prepareProductText(Product $product): string
    {
        $parts = [
            $product->name,
            $product->category?->name ?? '',
            $product->description ?? '',
        ];

        if (!empty($product->attributes) && is_array($product->attributes)) {
            $parts[] = implode(' ', array_values($product->attributes));
        }

        $topReviews = $product->reviews()->where('is_approved', true)->orderByDesc('rating')->take(3)->pluck('review')->toArray();
        if (!empty($topReviews)) {
            $parts[] = implode(' ', $topReviews);
        }

        return trim(implode(' ', array_filter($parts)));
    }
}
