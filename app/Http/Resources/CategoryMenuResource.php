<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryMenuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'slug'     => $this->slug,
            'image'    => $this->image_url,
            'children' => CategoryMenuResource::collection($this->children),
            'products_count' => $this->products_count ?? 0,
        ];
    }
}
