<?php

namespace App\Models;

use App\Enums\TestRideStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestRideRequest extends Model
{
    protected $fillable = [
        'motorcycle_id',
        'showroom_id',
        'name',
        'email',
        'phone',
        'preferred_date',
        'status',
        'internal_note',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'status' => TestRideStatus::class,
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * Data-entry users only see requests for motorcycles of their brands.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereHas('motorcycle', function (Builder $q) use ($user) {
            $q->whereIn('brand_id', $user->accessibleBrandIds());
        });
    }
}
