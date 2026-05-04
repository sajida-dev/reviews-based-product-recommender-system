<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
        'raw_text',
        'verified_purchase',
        'is_approved',
        'spam_flagged',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'spam_flagged' => 'boolean',
    ];
    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the aspect sentiments from this review
     */
    public function aspectSentiments(): HasMany
    {
        return $this->hasMany(AspectSentiment::class);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ?? 'https://i.pravatar.cc/40?u=' . $this->id;
    }
}
