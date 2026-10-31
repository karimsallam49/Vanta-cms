<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, LogsActivity, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ---------- Relations ---------- */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Brands this user is allowed to manage — applies to any non-admin
     * role touching brand-scoped content.
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'user_brand')->withTimestamps();
    }

    /* ---------- Access helpers ---------- */

    public function isAdmin(): bool
    {
        return $this->role?->is_admin ?? false;
    }

    /**
     * RBAC check — "motorcycles.edit". Admin roles bypass every check.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->role?->permissions ?? [], true);
    }

    /**
     * Brand IDs this user may manage. Admins get every brand;
     * data-entry users only their assigned ones.
     */
    public function accessibleBrandIds(): array
    {
        return once(fn () => $this->isAdmin()
            ? Brand::pluck('id')->all()
            : $this->brands()->pluck('brands.id')->all());
    }

    public function canAccessBrand(?int $brandId): bool
    {
        return $brandId !== null && in_array($brandId, $this->accessibleBrandIds(), true);
    }

    public function activityTitle(): string
    {
        return $this->name;
    }
}
