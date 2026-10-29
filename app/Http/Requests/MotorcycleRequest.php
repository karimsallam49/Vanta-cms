<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MotorcycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The chosen brand must be one the user manages — hard guarantee
        // on top of the policy check in the controller.
        return $this->user()?->canAccessBrand((int) $this->input('brand_id')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }

    public function rules(): array
    {
        $motorcycle = $this->route('motorcycle');
        $brandIds = $this->user()->accessibleBrandIds();

        return [
            'brand_id' => ['required', 'integer', Rule::in($brandIds)],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', Rule::unique('motorcycles', 'slug')->ignore($motorcycle)],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'monthly_from' => ['required', 'integer', 'min:0', 'max:100000'],
            'engine_cc' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'power_hp' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'weight_kg' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'top_speed_kmh' => ['nullable', 'integer', 'min:0', 'max:500'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'url', 'max:500'],
            'colors' => ['nullable', 'array', 'max:8'],
            'colors.*.name' => ['required_with:colors', 'string', 'max:60'],
            'colors.*.hex' => ['required_with:colors', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
