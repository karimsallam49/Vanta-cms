<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Offer $offer): bool
    {
        return $this->manages($user, $offer);
    }

    public function create(User $user): bool
    {
        return $user->accessibleBrandIds() !== [];
    }

    public function update(User $user, Offer $offer): bool
    {
        return $this->manages($user, $offer);
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $this->manages($user, $offer);
    }

    /**
     * Group-wide offers (brand_id NULL) are admin-only; brand offers
     * follow the user's assigned brands.
     */
    private function manages(User $user, Offer $offer): bool
    {
        if ($offer->isGroupWide()) {
            return $user->isAdmin();
        }

        return $user->canAccessBrand($offer->brand_id);
    }
}
