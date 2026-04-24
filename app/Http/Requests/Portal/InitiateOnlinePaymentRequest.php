<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class InitiateOnlinePaymentRequest extends FormRequest
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
            'payment_method' => ['required', 'in:paystack,flutterwave'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }
}