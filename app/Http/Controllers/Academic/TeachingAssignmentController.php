<?php

namespace App\Http\Controllers\Academic;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeachingAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->ensureCanManageAssignments($user);

        $classes = SchoolClass::query()
            ->with([
                'classTeacher.user:id,first_name,last_name',
                'subjects' => fn ($query) => $query
                    ->where('subjects.is_active', true)
                    ->orderBy('subjects.name')
                    ->select('subjects.id', 'subjects.name'),
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $teachers = Staff::query()
            ->with('user:id,first_name,last_name,role')
            ->active()
            ->whereHas('user', function ($query) {
                $query->where('role', UserRole::TEACHER->value);
            })
            ->get()
            ->sortBy(function (Staff $staff) {
                return strtolower(trim((string) $staff->full_name));
            })
            ->values();

        return view('academic.teaching-assignments.index', [
            'classes' => $classes,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $this->ensureCanManageAssignments($user);

        $validated = $request->validate([
            'class_teacher_ids' => ['nullable', 'array'],
            'class_teacher_ids.*' => ['nullable', 'integer', 'exists:staff,id'],
            'subject_teacher_ids' => ['nullable', 'array'],
            'subject_teacher_ids.*' => ['nullable', 'array'],
            'subject_teacher_ids.*.*' => ['nullable', 'integer', 'exists:staff,id'],
        ]);

        $classes = SchoolClass::query()
            ->with(['subjects:id'])
            ->get(['id']);

        $classIds = $classes->pluck('id')->map(fn ($id) => (int) $id)->all();
        $validClassIdMap = array_fill_keys($classIds, true);

        $classTeacherIds = collect((array) ($validated['class_teacher_ids'] ?? []))
            ->mapWithKeys(fn ($teacherId, $classId) => [(int) $classId => $teacherId ? (int) $teacherId : null])
            ->all();

        $subjectTeacherIds = collect((array) ($validated['subject_teacher_ids'] ?? []))
            ->mapWithKeys(function ($subjects, $classId) {
                $normalizedSubjects = collect((array) $subjects)
                    ->mapWithKeys(fn ($teacherId, $subjectId) => [(int) $subjectId => $teacherId ? (int) $teacherId : null])
                    ->all();

                return [(int) $classId => $normalizedSubjects];
            })
            ->all();

        foreach (array_keys($classTeacherIds) as $classId) {
            if (!isset($validClassIdMap[(int) $classId])) {
                throw ValidationException::withMessages([
                    'class_teacher_ids' => 'One or more class assignments are invalid.',
                ]);
            }
        }

        foreach (array_keys($subjectTeacherIds) as $classId) {
            if (!isset($validClassIdMap[(int) $classId])) {
                throw ValidationException::withMessages([
                    'subject_teacher_ids' => 'One or more subject assignments are invalid.',
                ]);
            }
        }

        $validSubjectsPerClass = $classes->mapWithKeys(function (SchoolClass $class) {
            return [(int) $class->id => $class->subjects->pluck('id')->map(fn ($id) => (int) $id)->all()];
        })->all();

        foreach ($subjectTeacherIds as $classId => $subjects) {
            $allowedSubjects = array_fill_keys($validSubjectsPerClass[(int) $classId] ?? [], true);
            foreach (array_keys($subjects) as $subjectId) {
                if (!isset($allowedSubjects[(int) $subjectId])) {
                    throw ValidationException::withMessages([
                        'subject_teacher_ids' => 'One or more class-subject combinations are invalid.',
                    ]);
                }
            }
        }

        $selectedTeacherIds = collect($classTeacherIds)
            ->merge(
                collect($subjectTeacherIds)->flatMap(fn ($subjects) => array_values((array) $subjects))
            )
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedTeacherIds->isNotEmpty()) {
            $allowedTeacherIds = Staff::query()
                ->active()
                ->whereIn('id', $selectedTeacherIds->all())
                ->whereHas('user', function ($query) {
                    $query->where('role', UserRole::TEACHER->value);
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (count($allowedTeacherIds) !== $selectedTeacherIds->count()) {
                throw ValidationException::withMessages([
                    'subject_teacher_ids' => 'One or more selected teachers are invalid or inactive.',
                ]);
            }
        }

        $updatedClasses = 0;
        $updatedSubjects = 0;

        DB::transaction(function () use (
            $classTeacherIds,
            $subjectTeacherIds,
            &$updatedClasses,
            &$updatedSubjects
        ) {
            foreach ($classTeacherIds as $classId => $teacherId) {
                $affected = SchoolClass::query()
                    ->where('id', (int) $classId)
                    ->update([
                        'class_teacher_id' => $teacherId ?: null,
                    ]);

                $updatedClasses += (int) $affected;
            }

            foreach ($subjectTeacherIds as $classId => $subjects) {
                foreach ($subjects as $subjectId => $teacherId) {
                    $affected = DB::table('class_subject')
                        ->where('class_id', (int) $classId)
                        ->where('subject_id', (int) $subjectId)
                        ->update([
                            'teacher_id' => $teacherId ?: null,
                            'updated_at' => now(),
                        ]);

                    $updatedSubjects += (int) $affected;
                }
            }
        });

        return redirect()
            ->route('academic.teaching-assignments.index')
            ->with('success', "Teacher assignments updated. Classes updated: {$updatedClasses}; class-subject assignments updated: {$updatedSubjects}.");
    }

    private function ensureCanManageAssignments($user): void
    {
        $role = (string) ($user?->role?->value ?? $user?->role ?? '');
        abort_unless(in_array($role, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ], true), 403, 'You are not authorized to manage teacher assignments.');
    }
}

