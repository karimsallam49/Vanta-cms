<?php

namespace App\Http\Requests;

use App\Enums\OfferScope;
use App\Models\Motorcycle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $action = $this->route('offer') ? 'edit' : 'create';
        if (! $user->hasPermission("offers.{$action}")) {
            return false;
        }

        $scope = OfferScope::tryFrom((string) $this->input('scope')) ?? OfferScope::Brand;

        // Group-level scopes (group-wide, test-ride) are admin territory.
        if (in_array($scope, [OfferScope::Group, OfferScope::TestRide], true)) {
            return $user->isAdmin();
        }

        if ($scope === OfferScope::Motorcycle) {
            $bike = Motorcycle::find($this->input('motorcycle_id'));

            return $bike && $user->canAccessBrand($bike->brand_id);
        }

        return $user->canAccessBrand((int) $this->input('brand_id'));
    }

    public function rules(): array
    {
        $brandIds = $this->user()->accessibleBrandIds();

        return [
            'scope' => ['required', Rule::enum(OfferScope::class)],
            'brand_id' => [
                Rule::requiredIf($this->input('scope') === OfferScope::Brand->value),
                'nullable', 'integer', Rule::in($brandIds),
            ],
            'motorcycle_id' => [
                Rule::requiredIf($this->input('scope') === OfferScope::Motorcycle->value),
                'nullable', 'integer', Rule::exists('motorcycles', 'id'),
            ],
            'title' => ['required', 'string', 'max:140'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
            'remove_image' => ['boolean'],
            'badge' => ['nullable', 'string', 'max:40'],
            'discount_percent' => ['nullable', 'integer', 'min:1', 'max:100'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $scope = OfferScope::tryFrom((string) $this->input('scope'));

        $brandId = $this->input('brand_id') ?: null;
        $motorcycleId = $this->input('motorcycle_id') ?: null;

        if ($scope === OfferScope::Motorcycle && $motorcycleId) {
            // The offer's owning brand is always the bike's brand — keeps
            // visibility/permissions consistent for data-entry users.
            $brandId = Motorcycle::find($motorcycleId)?->brand_id;
        } elseif (in_array($scope, [OfferScope::Group, OfferScope::TestRide], true)) {
            $brandId = null;
            $motorcycleId = null;
        } else {
            $motorcycleId = null;
        }

        $this->merge([
            'brand_id' => $brandId,
            'motorcycle_id' => $motorcycleId,
            'discount_percent' => $this->input('discount_percent') ?: null,
            'is_active' => $this->boolean('is_active'),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }
}
