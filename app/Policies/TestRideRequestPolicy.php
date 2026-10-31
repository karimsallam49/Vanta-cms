<?php

namespace App\Policies;

use App\Models\TestRideRequest;
use App\Models\User;

class TestRideRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('test-rides.view');
    }

    public function view(User $user, TestRideRequest $request): bool
    {
        return $user->hasPermission('test-rides.view')
            && $user->canAccessBrand($request->motorcycle->brand_id);
    }

    /**
     * Only status / internal-note updates are allowed — never delete
     * customer data or edit what they submitted.
     */
    public function update(User $user, TestRideRequest $request): bool
    {
        return $user->hasPermission('test-rides.edit')
            && $user->canAccessBrand($request->motorcycle->brand_id);
    }
}
