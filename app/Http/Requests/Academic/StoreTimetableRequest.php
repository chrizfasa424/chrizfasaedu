<?php

namespace App\Http\Requests\Academic;

use App\Models\SchoolClass;
use App\Models\Timetable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTimetableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'arm_id' => ['nullable', 'integer', 'exists:class_arms,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:staff,id'],
            'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:80'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $classId = (int) $this->input('class_id');
            $armId = $this->filled('arm_id') ? (int) $this->input('arm_id') : null;
            $subjectId = (int) $this->input('subject_id');
            $day = strtolower(trim((string) $this->input('day_of_week')));
            $startTime = (string) $this->input('start_time');
            $endTime = (string) $this->input('end_time');

            $class = SchoolClass::query()->with('subjects:id')->find($classId);
            if (!$class) {
                return;
            }

            if ($armId && !$class->arms()->where('id', $armId)->exists()) {
                $validator->errors()->add('arm_id', 'Selected arm does not belong to the selected class.');
                return;
            }

            $subjectIds = $class->subjects->pluck('id')->map(fn ($id) => (int) $id);
            if (!$subjectIds->contains($subjectId)) {
                $validator->errors()->add('subject_id', 'Selected subject is not assigned to the selected class.');
                return;
            }

            $session = $this->user()?->school?->currentSession();
            $term = $session?->terms()->where('is_current', true)->first();

            $overlapQuery = Timetable::query()
                ->where('class_id', $classId)
                ->whereRaw('LOWER(day_of_week) = ?', [$day])
                ->when(
                    $armId,
                    fn ($q) => $q->where('arm_id', $armId),
                    fn ($q) => $q->whereNull('arm_id')
                )
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);

            if ($session?->id) {
                $overlapQuery->where('session_id', (int) $session->id);
            }
            if ($term?->id) {
                $overlapQuery->where('term_id', (int) $term->id);
            }

            $ignoreId = $this->ignoredTimetableId();
            if ($ignoreId) {
                $overlapQuery->where('id', '!=', $ignoreId);
            }

            if ($overlapQuery->exists()) {
                $validator->errors()->add('start_time', 'This class timetable has an overlapping period in the selected day and scope.');
            }
        });
    }

    protected function ignoredTimetableId(): ?int
    {
        return null;
    }
}

