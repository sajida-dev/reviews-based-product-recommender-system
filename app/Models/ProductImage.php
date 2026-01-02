<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    protected static function booted()
    {
        static::creating(function ($image) {
            if ($image->is_primary) {
                static::where('product_id', $image->product_id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
