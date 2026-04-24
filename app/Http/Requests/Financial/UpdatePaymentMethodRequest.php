<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && in_array((string) ($user->role?->value ?? ''), [
            'super_admin',
            'school_admin',
            'principal',
            'vice_principal',
            'accountant',
        ], true);
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }
}