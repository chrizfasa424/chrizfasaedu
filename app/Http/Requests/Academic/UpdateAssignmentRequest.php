<?php

namespace App\Http\Requests\Academic;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateAssignmentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:8000'],
            'session_id' => ['nullable', 'exists:academic_sessions,id'],
            'term_id' => ['nullable', 'exists:academic_terms,id'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,published,unpublished'],
            'targets' => ['required', 'array', 'min:1'],
            'targets.*.class_id' => ['required', 'exists:classes,id'],
            'targets.*.arm_id' => ['nullable', 'exists:class_arms,id'],
            'targets.*.subject_id' => ['required', 'exists:subjects,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->input('targets', []) as $index => $target) {
                $classId = (int) ($target['class_id'] ?? 0);
                $armId = (int) ($target['arm_id'] ?? 0);
                $subjectId = (int) ($target['subject_id'] ?? 0);

                if ($armId > 0) {
                    $valid = \App\Models\ClassArm::query()
                        ->where('id', $armId)
                        ->where('class_id', $classId)
                        ->exists();

                    if (!$valid) {
                        $validator->errors()->add("targets.{$index}.arm_id", 'Selected arm does not belong to the selected class.');
                    }
                }

                if ($classId > 0 && $subjectId > 0) {
                    $subjectInClass = \Illuminate\Support\Facades\DB::table('class_subject')
                        ->where('class_id', $classId)
                        ->where('subject_id', $subjectId)
                        ->exists();

                    if (!$subjectInClass) {
                        $validator->errors()->add("targets.{$index}.subject_id", 'Selected subject is not assigned to the selected class.');
                    }
                }
            }

            $sessionId = (int) $this->input('session_id');
            $termId = (int) $this->input('term_id');
            if ($sessionId > 0 && $termId > 0) {
                $termBelongs = \App\Models\AcademicTerm::query()
                    ->where('id', $termId)
                    ->where('session_id', $sessionId)
                    ->exists();

                if (!$termBelongs) {
                    $validator->errors()->add('term_id', 'Selected term does not belong to the selected session.');
                }
            }
        });
    }
}
