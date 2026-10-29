<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $user->canAccessBrand($motorcycle->brand_id);
    }

    public function create(User $user): bool
    {
        // Needs at least one manageable brand — checked again in the form request.
        return $user->accessibleBrandIds() !== [];
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->canAccessBrand($motorcycle->brand_id);
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->canAccessBrand($motorcycle->brand_id);
    }
}
