<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MotorcycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        // Section permission first, then the brand-scope guarantee —
        // the chosen brand must be one the user manages.
        $action = $this->route('motorcycle') ? 'edit' : 'create';

        return $user->hasPermission("motorcycles.{$action}")
            && $user->canAccessBrand((int) $this->input('brand_id'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }

        // Unchecked checkboxes never reach the request — normalize them.
        $this->merge(['is_active' => $this->boolean('is_active')]);

        // The form ships the spec table as "LABEL | VALUE" lines and feature
        // tags as a comma list — parse both into the JSON arrays the model stores.
        if ($this->has('specs_text')) {
            $this->merge(['specs' => collect(preg_split('/\r\n|\r|\n/', (string) $this->input('specs_text')))
                ->map(fn ($line) => array_map('trim', explode('|', $line, 2)))
                ->filter(fn ($p) => count($p) === 2 && $p[0] !== '' && $p[1] !== '')
                ->map(fn ($p) => ['label' => mb_strtoupper($p[0]), 'value' => mb_strtoupper($p[1])])
                ->values()->all() ?: null]);
        }
        if ($this->has('features_text')) {
            $this->merge(['features' => collect(preg_split('/[,،]/', (string) $this->input('features_text')))
                ->map(fn ($f) => mb_strtoupper(trim($f)))
                ->filter()->values()->all() ?: null]);
        }
    }

    public function rules(): array
    {
        $motorcycle = $this->route('motorcycle');
        $brandIds = $this->user()->accessibleBrandIds();

        return [
            'brand_id' => ['required', 'integer', Rule::in($brandIds)],
            'name' => ['required', 'string', 'max:120'],
            'name_ar' => ['nullable', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', Rule::unique('motorcycles', 'slug')->ignore($motorcycle)],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'monthly_from' => ['required', 'integer', 'min:0', 'max:100000'],
            'currency_id' => ['required', 'integer', Rule::exists('currencies', 'id')],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'showrooms' => ['nullable', 'array'],
            'showrooms.*' => ['integer', Rule::exists('showrooms', 'id')],
            'engine_cc' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'power_hp' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'weight_kg' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'top_speed_kmh' => ['nullable', 'integer', 'min:0', 'max:500'],
            'specs_text' => ['nullable', 'string', 'max:2000'],
            'specs' => ['nullable', 'array', 'max:12'],
            'specs.*.label' => ['required_with:specs', 'string', 'max:40'],
            'specs.*.value' => ['required_with:specs', 'string', 'max:80'],
            'features_text' => ['nullable', 'string', 'max:500'],
            'features' => ['nullable', 'array', 'max:12'],
            'features.*' => ['string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'tagline_ar' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'url', 'max:500'],
            'colors' => ['nullable', 'array', 'max:8'],
            'colors.*.name' => ['required_with:colors', 'string', 'max:60'],
            'colors.*.hex' => ['required_with:colors', 'regex:/^#[0-9a-fA-F]{6}$/'],
            // Existing gallery images — keyed by image id.
            'images' => ['nullable', 'array'],
            'images.*.color_name' => ['nullable', 'string', 'max:60'],
            'images.*.color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'images.*.sort' => ['nullable', 'integer', 'min:0', 'max:500'],
            'images.*.delete' => ['nullable', 'boolean'],
            // New uploads — nested file + meta per tile.
            'new_images' => ['nullable', 'array', 'max:12'],
            'new_images.*.file' => ['nullable', 'mimes:jpg,jpeg,png,webp,avif', 'max:6144'],
            'new_images.*.color_name' => ['nullable', 'string', 'max:60'],
            'new_images.*.color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_images.*.sort' => ['nullable', 'integer', 'min:0', 'max:500'],
        ];
    }
}
