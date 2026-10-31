<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /** Sections whose content is scoped by assigned brands. */
    private const BRAND_SCOPED = ['brands', 'motorcycles', 'offers', 'test-rides'];

    public function authorize(): bool
    {
        $action = $this->route('user') ? 'edit' : 'create';

        return $this->user()?->hasPermission("staff.{$action}") ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user)],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            // Password required on create, optional on edit (blank = keep current).
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
            'brands' => ['nullable', 'array'],
            'brands.*' => ['integer', 'exists:brands,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $role = Role::find($this->input('role_id'));

            // Brands are only required when the chosen role can actually
            // reach brand-scoped sections — a reports-only viewer isn't
            // forced into a meaningless assignment.
            $needsBrands = $role && ! $role->is_admin && collect($role->permissions ?? [])
                ->contains(fn ($p) => in_array(Str::before($p, '.'), self::BRAND_SCOPED, true));

            if ($needsBrands && ! $this->filled('brands')) {
                $validator->errors()->add('brands', 'Assign at least one brand — this role manages brand-scoped sections.');
            }
        });
    }
}
