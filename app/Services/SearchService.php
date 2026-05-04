<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Constrain a Product query by name, description, category name, or JSON attributes.brand.
     */
    public function applyProductSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        // Split query into tokens and remove short words
        $tokens = collect(preg_split('/\s+/', $term))->map(fn($t) => trim($t))->filter(fn($t) => strlen($t) >= 2)->values();
        if ($tokens->isEmpty()) {
            $tokens = collect([$term]);
        }

        $driver = $query->getConnection()->getDriverName();

        return $query->where(function (Builder $outer) use ($tokens, $driver) {
            // For each token, require that it matches at least one searchable field
            foreach ($tokens as $token) {
                $like = '%' . $token . '%';

                $outer->where(function (Builder $q) use ($like, $driver, $token) {
                    $q->whereRaw('LOWER(name) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(description) LIKE LOWER(?)', [$like])
                        ->orWhereHas('category', fn(Builder $c) => $c->whereRaw('LOWER(name) LIKE LOWER(?)', [$like]));

                    // Search common attribute keys (brand, color, type)
                    if ($driver === 'sqlite') {
                        $q->orWhereRaw("LOWER(COALESCE(json_extract(attributes, '$.brand'), '')) LIKE LOWER(?)", [$like]);
                        $q->orWhereRaw("LOWER(COALESCE(json_extract(attributes, '$.color'), '')) LIKE LOWER(?)", [$like]);
                        $q->orWhereRaw("LOWER(COALESCE(json_extract(attributes, '$.type'), '')) LIKE LOWER(?)", [$like]);
                    } else {
                        $q->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.brand')), '')) LIKE LOWER(?)", [$like]);
                        $q->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.color')), '')) LIKE LOWER(?)", [$like]);
                        $q->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.type')), '')) LIKE LOWER(?)", [$like]);
                    }

                    // Generic attributes search (search all JSON as string)
                    if ($driver === 'sqlite') {
                        $q->orWhereRaw("LOWER(json_extract(attributes, '$')) LIKE LOWER(?)", [$like]);
                    } else {
                        $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(attributes, '$'))) LIKE LOWER(?)", [$like]);
                    }
                });
            }
        });
    }
}
