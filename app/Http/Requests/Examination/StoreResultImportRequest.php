<?php

namespace App\Http\Requests\Examination;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreResultImportRequest extends FormRequest
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
        ], true);
    }

    public function rules(): array
    {
        return [
            'section' => ['nullable', 'string', 'max:60'],
            'class_id' => ['required', 'exists:classes,id'],
            'arm_id' => ['nullable', 'exists:class_arms,id'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'term_id' => ['required', 'exists:academic_terms,id'],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'import_mode' => ['required', 'in:create_only,update_existing,replace_existing'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->filled('arm_id')) {
                return;
            }

            $arm = \App\Models\ClassArm::query()->find($this->integer('arm_id'));
            if ($arm && $arm->class_id !== $this->integer('class_id')) {
                $validator->errors()->add('arm_id', 'Selected arm does not belong to the selected class.');
            }
        });
    }
}


