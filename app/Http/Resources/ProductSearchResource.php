<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductSearchResource extends JsonResource
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
            'brand' => is_array($p->attributes) ? ($p->attributes['brand'] ?? null) : null,
            'main_image' => $p->images->first()?->url ?? asset('img/default.png'),
            'effective_price' => (float) $p->effective_price,
            'in_stock' => $p->stock > 0,
        ];
    }
}
