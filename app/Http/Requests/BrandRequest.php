<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }

    public function rules(): array
    {
        $brand = $this->route('brand');

        return [
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['required', 'string', 'max:90', Rule::unique('brands', 'slug')->ignore($brand)],
            'segment' => ['required', Rule::in(['urban', 'adventure', 'performance', 'premium'])],
            'origin' => ['nullable', 'string', 'max:80'],
            'tagline' => ['nullable', 'string', 'max:140'],
            'accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
