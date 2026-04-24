<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        return in_array((string) ($user->role?->value ?? ''), [
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
            'bank_name' => ['required', 'string', 'max:120'],
            'account_name' => ['required', 'string', 'max:180'],
            'account_number' => ['required', 'string', 'max:40'],
            'branch' => ['nullable', 'string', 'max:120'],
            'instruction_note' => ['nullable', 'string', 'max:4000'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}

