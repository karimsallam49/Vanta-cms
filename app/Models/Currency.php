<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function activityTitle(): string
    {
        return $this->code;
    }
}
