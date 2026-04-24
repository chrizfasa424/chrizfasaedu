<?php

namespace App\Http\Requests\Portal;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStudentAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (string) ($user->role?->value ?? $user->role ?? '') === UserRole::STUDENT->value;
    }

    public function rules(): array
    {
        return [
            'submission_text' => ['nullable', 'string', 'max:8000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $text = trim((string) $this->input('submission_text', ''));

            if ($text === '' && !$this->hasFile('attachment')) {
                $validator->errors()->add('submission_text', 'Provide submission text or upload an attachment.');
            }
        });
    }
}
