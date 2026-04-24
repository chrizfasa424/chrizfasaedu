<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPaymentRequest extends FormRequest
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
            'action' => ['required', 'in:approve,reject,under_review'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:2000'],
        ];
    }
}