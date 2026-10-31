<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MotorcycleImage extends Model
{
    use LogsActivity;

    protected $fillable = [
        'motorcycle_id',
        'path',
        'color_name',
        'color_hex',
        'sort_order',
    ];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    protected function url(): Attribute
    {
        // External URLs (seeded demo photos, hotlinks) pass through untouched;
        // uploaded files resolve through the public disk.
        return Attribute::get(fn () => str_starts_with($this->path, 'http')
            ? $this->path
            : Storage::disk('public')->url($this->path));
    }

    public function activityTitle(): string
    {
        return $this->motorcycle?->name.' — photo';
    }
}
