<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ActivityLog extends Model
{
    // Append-only journal — rows are written, never edited.
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Write one entry. Never throws — a logging hiccup must not break
     * the action the user actually asked for.
     */
    public static function record(string $action, ?Model $subject = null, ?string $description = null, array $properties = []): void
    {
        try {
            static::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'description' => $description ?? $action,
                'properties' => $properties ?: null,
                'ip_address' => app()->runningInConsole() ? null : request()->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // swallow — audit is best-effort
        }
    }

    /** Fields that changed, for the log table's "details" column. */
    public function changedFields(): array
    {
        return array_keys($this->properties['changes'] ?? []);
    }

    public function tone(): string
    {
        return match ($this->action) {
            'deleted' => 'red',
            'created', 'login' => 'lime',
            default => '',
        };
    }
}
