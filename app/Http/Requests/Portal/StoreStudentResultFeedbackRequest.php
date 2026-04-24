<?php

namespace App\Http\Requests\Portal;

use App\Enums\UserRole;
use App\Models\StudentResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStudentResultFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('portal')->user() ?? $this->user();

        return $user
            && ($user->role?->value ?? null) === UserRole::STUDENT->value
            && $user->student;
    }

    public function rules(): array
    {
        return [
            'student_result_id' => ['nullable', 'integer', 'exists:student_results,id'],
            'term_id' => ['nullable', 'integer', 'exists:academic_terms,id'],
            'exam_type_id' => ['nullable', 'integer', 'exists:exam_types,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'feedback_type' => ['required', 'in:feedback,query'],
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:12', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = auth('portal')->user() ?? $this->user();
            $student = $user?->student;

            if (!$student) {
                $validator->errors()->add('student', 'Student profile is not available.');
                return;
            }

            if (!$this->filled('student_result_id')) {
                return;
            }

            $sheet = StudentResult::query()->find((int) $this->input('student_result_id'));
            if (!$sheet) {
                return;
            }

            if ((int) $sheet->student_id !== (int) $student->id || (int) $sheet->school_id !== (int) $student->school_id) {
                $validator->errors()->add('student_result_id', 'You are not allowed to submit feedback for this result.');
                return;
            }

            if ($this->filled('subject_id')) {
                $subjectExists = $sheet->items()->where('subject_id', (int) $this->input('subject_id'))->exists();
                if (!$subjectExists) {
                    $validator->errors()->add('subject_id', 'Selected subject does not belong to the selected result sheet.');
                }
            }
        });
    }
}

