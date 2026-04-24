<?php

namespace App\Http\Requests\Examination;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreResultSubmissionRequest extends FormRequest
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
            'section' => ['nullable', 'string', 'max:60'],
            'class_id' => ['required', 'exists:classes,id'],
            'arm_id' => ['nullable', 'exists:class_arms,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'term_id' => ['required', 'exists:academic_terms,id'],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'assessment_type' => ['required', 'in:first_test,second_test,exam,full_result'],
            'import_mode' => ['required', 'in:create_only,update_existing,replace_existing'],
            'action' => ['nullable', 'in:draft,submit'],
            'staff_note' => ['nullable', 'string', 'max:3000'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $classId = (int) $this->input('class_id');
            $armId = (int) $this->input('arm_id');
            $subjectId = (int) $this->input('subject_id');

            if ($armId > 0) {
                $armBelongs = \App\Models\ClassArm::query()
                    ->where('id', $armId)
                    ->where('class_id', $classId)
                    ->exists();

                if (!$armBelongs) {
                    $validator->errors()->add('arm_id', 'Selected arm does not belong to selected class.');
                }
            }

            if ($subjectId > 0) {
                $subjectInClass = \Illuminate\Support\Facades\DB::table('class_subject')
                    ->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->exists();

                if (!$subjectInClass) {
                    $validator->errors()->add('subject_id', 'Selected subject is not assigned to the selected class.');
                }
            }
        });
    }
}
