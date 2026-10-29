<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showroom extends Model
{
    protected $fillable = [
        'city',
        'country',
        'name',
        'address',
        'phone',
        'hours',
        'is_service_center',
    ];

    protected function casts(): array
    {
        return [
            'is_service_center' => 'boolean',
        ];
    }

    public function testRideRequests(): HasMany
    {
        return $this->hasMany(TestRideRequest::class);
    }
}
