<?php

namespace App\Http\Requests\Examination;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DownloadResultSubmissionTemplateRequest extends FormRequest
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
            'class_id' => ['required', 'exists:classes,id'],
            'arm_id' => ['nullable', 'exists:class_arms,id'],
            'assessment_type' => ['required', 'in:first_test,second_test,exam,full_result'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->filled('arm_id')) {
                return;
            }

            $arm = \App\Models\ClassArm::query()->find((int) $this->input('arm_id'));
            if ($arm && (int) $arm->class_id !== (int) $this->input('class_id')) {
                $validator->errors()->add('arm_id', 'Selected arm does not belong to selected class.');
            }
        });
    }
}
