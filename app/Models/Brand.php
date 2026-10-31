<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Brand extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'segment',
        'origin',
        'origin_ar',
        'tagline',
        'tagline_ar',
        'accent',
    ];

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_brand')->withTimestamps();
    }

    public function testRideRequests(): HasManyThrough
    {
        return $this->hasManyThrough(TestRideRequest::class, Motorcycle::class);
    }

    public function activityTitle(): string
    {
        return $this->name;
    }
}
