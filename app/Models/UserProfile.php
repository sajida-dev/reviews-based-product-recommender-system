<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'interests_vector',
        'preferred_categories',
        'last_interest_update',
        'model_version',
        'preference_score',
    ];

    protected $casts = [
        'interests_vector' => 'array',
        'preferred_categories' => 'array',
        'preference_score' => 'float',
        'last_interest_update' => 'datetime',
    ];

    /**
     * Get the user this profile belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if profile needs update (older than 7 days)
     */
    public function needsUpdate(): bool
    {
        return $this->last_interest_update === null ||
            $this->last_interest_update->diffInDays(now()) > 7;
    }

    /**
     * Get the top preferred categories
     */
    public function getTopCategoriesAttribute(): array
    {
        return array_slice($this->preferred_categories ?? [], 0, 3);
    }
}
