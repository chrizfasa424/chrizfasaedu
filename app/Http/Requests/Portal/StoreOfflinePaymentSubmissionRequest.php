<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfflinePaymentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('portal')->user() ?? $this->user();

        return $user && (string) ($user->role?->value ?? '') === 'student';
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_method' => ['required', 'in:bank_transfer,pos'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_reference' => ['required', 'string', 'max:120'],
            'sender_account_name' => ['nullable', 'string', 'max:160'],
            'sender_bank' => ['nullable', 'string', 'max:160'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}