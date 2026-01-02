<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Helper: safe delete category
    public function safeDelete(): bool
    {
        if ($this->products()->exists() || $this->children()->exists()) {
            return false;
        }
        return $this->delete();
    }

    // App\Models\Category.php
    public function scopeForMegaMenu($query)
    {
        return $query
            ->whereNull('parent_id')
            ->with([
                'children' => function ($q) {
                    $q->withCount('products')
                        ->with('children');
                }
            ]);
    }
    public function scopeNewArrivals($query)
    {
        return $query->whereNull('parent_id')
            ->latest()
            ->limit(8);
    }

    public function scopeTrending($query)
    {
        return $query->whereNull('parent_id')
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(8);
    }

    public function scopePopular($query)
    {
        return $query->whereNull('parent_id')
            ->orderBy('name')
            ->limit(8);
    }
}
