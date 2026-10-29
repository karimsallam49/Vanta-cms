<?php

namespace App\Http\Requests;

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

        // NULL brand_id = group-wide offer → admin only.
        if (! $this->filled('brand_id')) {
            return $user->isAdmin();
        }

        return $user->canAccessBrand((int) $this->input('brand_id'));
    }

    public function rules(): array
    {
        $brandIds = $this->user()->accessibleBrandIds();

        return [
            'brand_id' => ['nullable', 'integer', Rule::in($brandIds)],
            'title' => ['required', 'string', 'max:140'],
            'description' => ['required', 'string', 'max:2000'],
            'badge' => ['nullable', 'string', 'max:40'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'brand_id' => $this->input('brand_id') ?: null,
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
