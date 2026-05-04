<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductRecommendationResource;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationApiController extends Controller
{
    public function __construct(protected RecommendationService $recommendations) {}

    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 10), 1), 30);

        $user = $request->user();
        if ($user !== null) {
            $items = $this->recommendations->getPersonalizedRecommendations($user, $limit);
        } else {
            $exclude = array_map('intval', session('guest_viewed_products', []));
            $items = $this->recommendations->getColdStartRecommendationsForGuest($limit, $exclude);
        }

        return ProductRecommendationResource::collection($items)->response();
    }
}
