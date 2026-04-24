<?php

namespace App\Http\Requests\Financial;

use App\Models\BursarySignature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBursarySignatureRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:160'],
            'title' => ['nullable', 'string', 'max:160'],
            'signature_role' => ['required', Rule::in(BursarySignature::ROLE_OPTIONS)],
            'signature' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
