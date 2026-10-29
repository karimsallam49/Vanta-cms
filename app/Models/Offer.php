<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = [
        'brand_id',
        'title',
        'description',
        'badge',
        'expires_on',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_on' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function isGroupWide(): bool
    {
        return $this->brand_id === null;
    }

    /**
     * Data-entry users see offers for their brands plus group-wide ones.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->whereIn('brand_id', $user->accessibleBrandIds())
              ->orWhereNull('brand_id');
        });
    }
}
