<?php

namespace App\Models;

use App\Enums\OfferScope;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use LogsActivity;

    protected $fillable = [
        'brand_id',
        'scope',
        'motorcycle_id',
        'title',
        'description',
        'badge',
        'discount_percent',
        'expires_on',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'scope' => OfferScope::class,
            'discount_percent' => 'integer',
            'expires_on' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    /**
     * Group-level offers (brand_id NULL) cover group-wide and test-ride
     * scopes — both are admin territory for permissions purposes.
     */
    public function isGroupWide(): bool
    {
        return $this->brand_id === null;
    }

    /** What the offer is attached to, for list display and the API. */
    public function targetName(): string
    {
        return match ($this->scope) {
            OfferScope::Group => 'Whole group',
            OfferScope::Brand => $this->brand?->name ?? '—',
            OfferScope::Motorcycle => $this->motorcycle?->name ?? '—',
            OfferScope::TestRide => 'Test rides',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('expires_on')->orWhere('expires_on', '>=', today()));
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

    public function activityTitle(): string
    {
        return $this->title;
    }
}
