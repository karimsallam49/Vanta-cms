<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motorcycle extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'price',
        'monthly_from',
        'engine_cc',
        'power_hp',
        'weight_kg',
        'top_speed_kmh',
        'tagline',
        'image',
        'colors',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function testRideRequests(): HasMany
    {
        return $this->hasMany(TestRideRequest::class);
    }

    /**
     * Scope a listing to the brands the user may manage.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereIn('brand_id', $user->accessibleBrandIds());
    }
}
