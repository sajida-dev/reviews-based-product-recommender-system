<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'is_active',
        'attributes',
        'views'
    ];

    protected $casts = [
        'attributes' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];
    protected $appends = [
        'effective_price',
        'discount_amount',
        'discount_percentage',
        'is_in_stock',
        'is_sellable',
    ];

    // ------------------- RELATIONSHIPS -------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function orders()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the product's embedding (vector representation)
     */
    public function embedding(): HasMany
    {
        return $this->hasMany(Embedding::class);
    }

    /**
     * Get the latest embedding
     */
    public function latestEmbedding()
    {
        return $this->hasOne(Embedding::class)->latest();
    }

    /**
     * Get all aspect sentiments from reviews
     */
    public function aspectSentiments(): HasMany
    {
        return $this->hasMany(AspectSentiment::class);
    }

    /**
     * Get recommendation logs for this product as the source
     */
    public function recommendationsAsSource(): HasMany
    {
        return $this->hasMany(RecommendationLog::class, 'product_id');
    }

    /**
     * Get recommendation logs for this product as the recommendation
     */
    public function recommendationsAsTarget(): HasMany
    {
        return $this->hasMany(RecommendationLog::class, 'recommended_product_id');
    }


    public function isLowStock($threshold = 5): bool
    {
        return $this->stock <= $threshold;
    }
    /* Scopes */
    public function scopeSearch($q, $term)
    {
        $q->where('name', 'like', "%{$term}%");
    }

    public function scopeCategory($q, $categoryId)
    {
        $q->where('category_id', $categoryId);
    }

   /* ------------------- PRICING ACCESSORS ------------------- */

    /**
     * Final price shown to users & admin
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->hasValidDiscount()
            ? (float) $this->discount_price
            : (float) $this->price;
    }

    /**
     * Whether discount is valid
     */
    public function hasValidDiscount(): bool
    {
        return
            $this->discount_price !== null &&
            $this->discount_price > 0 &&
            $this->discount_price < $this->price;
    }

    /**
     * Discount amount (absolute)
     */
    public function getDiscountAmountAttribute(): float
    {
        return $this->hasValidDiscount()
            ? (float) ($this->price - $this->discount_price)
            : 0.0;
    }

    /**
     * Discount percentage
     */
    public function getDiscountPercentageAttribute(): int
    {
        return $this->hasValidDiscount()
            ? (int) round(($this->discount_amount / $this->price) * 100)
            : 0;
    }

    /* ------------------- STOCK & AVAILABILITY ------------------- */

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getIsSellableAttribute(): bool
    {
        return $this->is_active && $this->is_in_stock;
    }


    // ------------------- HELPER METHODS -------------------


    public function rating(): float
    {
        return round((float) ($this->reviews()->avg('rating') ?? 0), 2);
    }


    public function reduceStock(int $qty): bool
    {
        if ($qty <= 0) {
            return false;
        }

        return DB::transaction(function () use ($qty) {
            $affected = self::where('id', $this->id)
                ->where('stock', '>=', $qty)
                ->decrement('stock', $qty);

            return $affected > 0;
        });
    }
}
