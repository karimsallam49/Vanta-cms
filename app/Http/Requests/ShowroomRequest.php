<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $action = $this->route('showroom') ? 'edit' : 'create';

        return $this->user()?->hasPermission("showrooms.{$action}") ?? false;
    }

    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:80'],
            'country' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:140'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'hours' => ['nullable', 'string', 'max:120'],
            'open_days' => ['nullable', 'array', 'min:1'],
            'open_days.*' => ['integer', 'between:0,6'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_service_center' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_service_center' => $this->boolean('is_service_center')]);
    }
}
