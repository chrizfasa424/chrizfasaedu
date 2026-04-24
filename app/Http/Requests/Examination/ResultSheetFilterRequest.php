<?php

namespace App\Http\Requests\Examination;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class ResultSheetFilterRequest extends FormRequest
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
            UserRole::PARENT->value,
            UserRole::STUDENT->value,
        ], true);
    }

    public function rules(): array
    {
        return [
            'section' => ['nullable', 'string', 'max:60'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'arm_id' => ['nullable', 'exists:class_arms,id'],
            'session_id' => ['nullable', 'exists:academic_sessions,id'],
            'term_id' => ['nullable', 'exists:academic_terms,id'],
            'exam_type_id' => ['nullable', 'exists:exam_types,id'],
            'student_id' => ['nullable', 'exists:students,id'],
        ];
    }
}


