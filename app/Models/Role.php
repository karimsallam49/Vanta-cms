<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'slug', 'description', 'is_admin', 'permissions'];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Grouped counts for the index card — e.g. "4/6 motorcycles". */
    public function permissionSummary(): array
    {
        $perms = $this->permissions ?? [];
        $out = [];
        foreach (\App\Support\Permissions::sections() as $key => $def) {
            $granted = count(array_intersect(array_keys($def['actions']), array_map(
                fn ($p) => explode('.', $p)[1] ?? '', array_filter($perms, fn ($p) => str_starts_with($p, "{$key}."))
            )));
            if ($granted > 0) {
                $out[$key] = $granted;
            }
        }

        return $out;
    }

    public function activityTitle(): string
    {
        return $this->name;
    }
}
