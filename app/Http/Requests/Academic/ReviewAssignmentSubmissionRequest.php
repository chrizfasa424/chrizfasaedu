<?php

namespace App\Http\Requests\Academic;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReviewAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return in_array((string) ($user->role?->value ?? $user->role ?? ''), [
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
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'teacher_feedback' => ['nullable', 'string', 'max:8000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $score = $this->input('score');
            $feedback = trim((string) $this->input('teacher_feedback', ''));

            $hasScore = !is_null($score) && $score !== '';
            $hasFeedback = $feedback !== '';

            if (!$hasScore && !$hasFeedback) {
                $validator->errors()->add('score', 'Add a score or teacher feedback before saving review.');
            }
        });
    }
}
