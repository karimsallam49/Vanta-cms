<?php

namespace App\Http\Requests;

use App\Enums\TestRideStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestRideUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $request = $this->route('testRide');

        return $request && $this->user()?->can('update', $request);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(TestRideStatus::class)],
            'internal_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
