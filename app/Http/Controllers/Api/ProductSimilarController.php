<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductRecommendationResource;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class ProductSimilarController extends Controller
{
    public function __construct(protected RecommendationService $recommendations) {}

    public function __invoke(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        if (! $product->is_active) {
            abort(404);
        }

        $limit = min(max((int) $request->query('limit', 8), 1), 24);
        $items = $this->recommendations->getSimilarProducts($product, $limit, $request->user());

        return ProductRecommendationResource::collection($items)->response();
    }
}
