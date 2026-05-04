<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean'
    ];
    protected $appends = [
        'url',
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
    /**
     * Accessor to get the full URL of the image
     */
    public function getUrlAttribute(): string
    {
        return self::resolvePublicUrl($this->image_path);
    }

    /**
     * External URLs (https://…) must be used as-is. Relative paths use the public disk.
     */
    public static function resolvePublicUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return asset('img/default.png');
        }

        $path = trim($path);

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '//')) {
            return 'https:'.$path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return Storage::disk('public')->url($path);
    }
}
