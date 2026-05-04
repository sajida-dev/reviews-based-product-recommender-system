<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationLog extends Model
{
    protected $table = 'recommendation_logs';

    protected $fillable = [
        'user_id',
        'product_id',
        'recommended_product_id',
        'recommendation_type',
        'score',
        'rank',
        'was_clicked',
        'was_purchased',
    ];

    protected $casts = [
        'score' => 'float',
        'rank' => 'integer',
        'was_clicked' => 'boolean',
        'was_purchased' => 'boolean',
    ];

    /**
     * Get the user this recommendation is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was recommended
     */
    public function recommendedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'recommended_product_id');
    }

    /**
     * Get the reference product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope by recommendation type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('recommendation_type', $type);
    }

    /**
     * Scope to get clicked recommendations
     */
    public function scopeClicked($query)
    {
        return $query->where('was_clicked', true);
    }

    /**
     * Scope to get purchased recommendations
     */
    public function scopePurchased($query)
    {
        return $query->where('was_purchased', true);
    }

    /**
     * Calculate click-through rate
     */
    public static function getClickThroughRate(string $type = null): float
    {
        // $query = self::class;
        $query = self::query();

        if ($type) {
            $query = $query->byType($type);
        }

        $total = $query->count();
        $clicked = $query->clicked()->count();

        return $total > 0 ? ($clicked / $total) * 100 : 0;
    }

    /**
     * Calculate conversion rate (purchases from recommendations)
     */
    public static function getConversionRate(string $type = null): float
    {
        // $query = self::class;
        $query = self::query();

        if ($type) {
            $query = $query->byType($type);
        }

        $clicked = $query->clicked()->count();
        $purchased = $query->purchased()->count();

        return $clicked > 0 ? ($purchased / $clicked) * 100 : 0;
    }
}
