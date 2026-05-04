<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Embedding extends Model
{
    protected $fillable = [
        'product_id',
        'embedding',
        'model_version',
        'dimension',
        'qdrant_id',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the product that owns this embedding
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
