<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGatewaySettingsRequest extends FormRequest
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
            'is_active' => ['nullable', 'boolean'],
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'merchant_email' => ['nullable', 'email', 'max:255'],
            'secret_hash' => ['nullable', 'string', 'max:255'],
            'encryption_key' => ['nullable', 'string', 'max:255'],
            'base_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
