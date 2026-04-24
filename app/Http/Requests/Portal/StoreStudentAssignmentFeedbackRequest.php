<?php

namespace App\Http\Requests\Portal;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentAssignmentFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (string) ($user->role?->value ?? $user->role ?? '') === UserRole::STUDENT->value;
    }

    public function rules(): array
    {
        return [
            'student_feedback' => ['required', 'string', 'max:5000'],
        ];
    }
}
