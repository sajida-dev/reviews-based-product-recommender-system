<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductRecommendationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Product $p */
        $p = $this->resource;

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'name' => $p->name,
            'category' => $p->category?->name,
            'price' => (float) $p->price,
            'discount_price' => $p->discount_price !== null ? (float) $p->discount_price : null,
            'effective_price' => (float) $p->effective_price,
            'discount_percentage' => $p->discount_percentage,
            'main_image' => $p->images->first()?->url ?? '/img/default.png',
            'in_stock' => $p->stock > 0,
            'rating_avg' => round((float) ($p->reviews->where('is_approved', true)->avg('rating') ?? 0), 2),
            'rating_count' => $p->reviews->where('is_approved', true)->count(),
            'top_aspects' => $p->getAttribute('top_aspects') ?? [],
            'similarity_score' => $this->when(
                $p->getAttribute('similarity_score') !== null,
                $p->getAttribute('similarity_score')
            ),
            'because_you_liked' => $this->when(
                $p->getAttribute('because_you_liked') !== null,
                $p->getAttribute('because_you_liked')
            ),
            'matching_aspects' => $p->getAttribute('matching_aspects') ?? [],
            'recommended_for_you' => (bool) $p->getAttribute('recommended_for_you'),
            'recommendation_badge' => $p->getAttribute('recommendation_badge') ?? 'trending',
        ];
    }
}
