<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showroom extends Model
{
    use LogsActivity;

    protected $fillable = [
        'city',
        'country',
        'name',
        'address',
        'phone',
        'hours',
        'open_days',
        'latitude',
        'longitude',
        'is_service_center',
    ];

    protected function casts(): array
    {
        return [
            'is_service_center' => 'boolean',
            'open_days' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function testRideRequests(): HasMany
    {
        return $this->hasMany(TestRideRequest::class);
    }

    /** Models stocked at this location — drives "available here" on the site. */
    public function motorcycles(): BelongsToMany
    {
        return $this->belongsToMany(Motorcycle::class);
    }

    public function activityTitle(): string
    {
        return $this->city.' — '.$this->name;
    }
}
