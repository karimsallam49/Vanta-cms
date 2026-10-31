<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class SiteSection extends Model
{
    use LogsActivity;

    protected $fillable = ['key', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    /** Whole map for the public API — { key: payload }. */
    public static function allPayloads(): array
    {
        return static::pluck('payload', 'key')->map(fn ($p) => $p ?? [])->all();
    }

    public function activityTitle(): string
    {
        return $this->key;
    }
}
