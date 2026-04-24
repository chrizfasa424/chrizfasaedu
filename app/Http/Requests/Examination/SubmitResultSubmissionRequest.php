<?php

namespace App\Http\Requests\Examination;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class SubmitResultSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return in_array((string) $user->role?->value, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
        ], true);
    }

    public function rules(): array
    {
        return [
            'staff_note' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
