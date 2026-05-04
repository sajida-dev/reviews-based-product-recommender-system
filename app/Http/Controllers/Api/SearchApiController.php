<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSearchResource;
use App\Models\Product;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    public function __construct(protected SearchService $searchService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['message' => 'Query parameter q is required.'], 422);
        }

        $limit = min(max((int) $request->query('limit', 24), 1), 50);

        $query = Product::query()
            ->where('is_active', true)
            ->with(['category:id,name', 'images']);

        $this->searchService->applyProductSearch($query, $q);

        $items = $query->latest()->limit($limit)->get();

        return ProductSearchResource::collection($items)->response();
    }
}
