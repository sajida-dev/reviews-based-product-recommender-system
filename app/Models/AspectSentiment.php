<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AspectSentiment extends Model
{
    protected $fillable = [
        'review_id',
        'product_id',
        'aspect',
        'sentiment',
        'confidence',
        'mention_text',
        'is_emphasized',
    ];

    protected $casts = [
        'confidence' => 'float',
        'is_emphasized' => 'boolean',
    ];

    /**
     * Get the review that owns this aspect sentiment
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the product this aspect is about
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get positive sentiments
     */
    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    /**
     * Scope to get negative sentiments
     */
    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    /**
     * Scope by aspect name
     */
    public function scopeAspect($query, string $aspect)
    {
        return $query->where('aspect', $aspect);
    }

    /**
     * Scope by high confidence
     */
    public function scopeHighConfidence($query, float $threshold = 0.7)
    {
        return $query->where('confidence', '>=', $threshold);
    }
}
