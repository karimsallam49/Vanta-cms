<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Journal create/update/delete on a model. Diff payloads keep the old and
 * new value per field; credentials and timestamps are never recorded.
 */
trait LogsActivity
{
    /** Attributes excluded from update diffs. */
    protected array $activityHidden = ['password', 'remember_token', 'updated_at', 'locale'];

    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            $model->logActivity('created');
        });

        static::updated(function (Model $model) {
            $changes = collect($model->getChanges())
                ->except($model->activityHidden)
                ->mapWithKeys(fn ($new, $field) => [$field => [$model->getOriginal($field), $new]])
                ->all();

            if ($changes !== []) {
                $model->logActivity('updated', ['changes' => $changes]);
            }
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted');
        });
    }

    /** Human label for the log row — override per model when a name exists. */
    public function activityTitle(): string
    {
        return class_basename($this).' #'.$this->getKey();
    }

    protected function logActivity(string $action, array $properties = []): void
    {
        ActivityLog::record(
            $action,
            $this,
            Str::headline(class_basename($this)).' — '.$this->activityTitle(),
            $properties,
        );
    }
}
