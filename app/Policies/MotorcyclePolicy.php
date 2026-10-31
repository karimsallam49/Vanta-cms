<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('motorcycles.view');
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasPermission('motorcycles.view')
            && $user->canAccessBrand($motorcycle->brand_id);
    }

    public function create(User $user): bool
    {
        // Needs the permission plus at least one manageable brand —
        // the brand itself is enforced again in the form request.
        return $user->hasPermission('motorcycles.create')
            && $user->accessibleBrandIds() !== [];
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasPermission('motorcycles.edit')
            && $user->canAccessBrand($motorcycle->brand_id);
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasPermission('motorcycles.delete')
            && $user->canAccessBrand($motorcycle->brand_id);
    }
}
