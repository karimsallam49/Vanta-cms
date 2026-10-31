<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Motorcycle extends Model
{
    use LogsActivity;

    protected $fillable = [
        'brand_id',
        'name',
        'name_ar',
        'slug',
        'price',
        'monthly_from',
        'engine_cc',
        'power_hp',
        'weight_kg',
        'top_speed_kmh',
        'specs',
        'tagline',
        'tagline_ar',
        'description',
        'description_ar',
        'currency_id',
        'image',
        'colors',
        'features',
        'is_active',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'specs' => 'array',
            'features' => 'array',
            'is_active' => 'boolean',
            'quantity' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function showrooms(): BelongsToMany
    {
        return $this->belongsToMany(Showroom::class);
    }

    public function testRideRequests(): HasMany
    {
        return $this->hasMany(TestRideRequest::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->orderBy('sort_order');
    }

    /**
     * First gallery image wins; falls back to the external URL column.
     */
    public function coverUrl(): ?string
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->first()?->url ?? $this->image;
    }

    protected static function booted(): void
    {
        static::deleting(function (Motorcycle $motorcycle) {
            foreach ($motorcycle->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        });
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

    public function activityTitle(): string
    {
        return $this->name;
    }
}
