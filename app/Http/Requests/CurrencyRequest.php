<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $action = $this->route('currency') ? 'edit' : 'create';

        return $this->user()?->hasPermission("currencies.{$action}") ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => Str::upper($this->input('code'))]);
        }
    }

    public function rules(): array
    {
        $currency = $this->route('currency');

        return [
            'code' => ['required', 'alpha', 'size:3', Rule::unique('currencies', 'code')->ignore($currency)],
            'name' => ['required', 'string', 'max:60'],
            'symbol' => ['required', 'string', 'max:8'],
        ];
    }
}
