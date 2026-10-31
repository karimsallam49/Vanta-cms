<?php

namespace App\Http\Requests;

use App\Support\Permissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $action = $this->route('role') ? 'edit' : 'create';

        return $this->user()?->hasPermission("roles.{$action}") ?? false;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:60'],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('roles', 'slug')->ignore($role)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(Permissions::all())],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }

        $this->merge([
            'is_admin' => $this->boolean('is_admin'),
            // Admin roles carry no matrix — the flag is the whole answer.
            'permissions' => $this->boolean('is_admin') ? [] : array_values($this->input('permissions', [])),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->boolean('is_admin') && empty($this->input('permissions'))) {
                $validator->errors()->add('permissions', 'Grant at least one permission — or mark the role as administrator.');
            }
        });
    }
}
