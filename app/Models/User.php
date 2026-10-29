<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
            'role' => UserRole::class,
        ];
    }

    /* ---------- Relations ---------- */

    /**
     * Brands this user is allowed to manage (data-entry role only).
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'user_brand')->withTimestamps();
    }

    /* ---------- Role helpers ---------- */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
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
}
