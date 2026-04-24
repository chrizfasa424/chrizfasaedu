<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class RecordCashPaymentRequest extends FormRequest
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
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['nullable', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'mark_approved' => ['nullable', 'boolean'],
        ];
    }
}
