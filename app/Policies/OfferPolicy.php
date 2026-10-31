<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('offers.view');
    }

    public function view(User $user, Offer $offer): bool
    {
        return $user->hasPermission('offers.view') && $this->manages($user, $offer);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('offers.create')
            && $user->accessibleBrandIds() !== [];
    }

    public function update(User $user, Offer $offer): bool
    {
        return $user->hasPermission('offers.edit') && $this->manages($user, $offer);
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $user->hasPermission('offers.delete') && $this->manages($user, $offer);
    }

    /**
     * Group-wide offers (brand_id NULL) stay admin territory; brand
     * offers follow the user's assigned brands.
     */
    private function manages(User $user, Offer $offer): bool
    {
        if ($offer->isGroupWide()) {
            return $user->isAdmin();
        }

        return $user->canAccessBrand($offer->brand_id);
    }
}
