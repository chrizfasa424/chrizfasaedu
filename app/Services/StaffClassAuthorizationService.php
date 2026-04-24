<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StaffClassAuthorizationService
{
    public function hasPrivilegedAcademicRole(User $user): bool
    {
        return in_array($this->roleValue($user), [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ], true);
    }

    public function authorizedClassIds(User $user): array
    {
        $schoolId = (int) $user->school_id;

        if ($this->hasPrivilegedAcademicRole($user)) {
            return SchoolClass::query()
                ->where('school_id', $schoolId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        $staffId = (int) ($user->staffProfile?->id ?? 0);
        if ($staffId <= 0) {
            return [];
        }

        $classTeacherClassIds = SchoolClass::query()
            ->where('school_id', $schoolId)
            ->where('class_teacher_id', $staffId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $subjectClassIds = DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', $schoolId)
            ->where('class_subject.teacher_id', $staffId)
            ->pluck('class_subject.class_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return collect(array_merge($classTeacherClassIds, $subjectClassIds))
            ->unique()
            ->values()
            ->all();
    }

    public function canManageClass(User $user, int $classId): bool
    {
        $schoolId = (int) $user->school_id;
        if ($classId <= 0 || $schoolId <= 0) {
            return false;
        }

        if ($this->hasPrivilegedAcademicRole($user)) {
            return SchoolClass::query()
                ->where('school_id', $schoolId)
                ->where('id', $classId)
                ->exists();
        }

        $staffId = (int) ($user->staffProfile?->id ?? 0);
        if ($staffId <= 0) {
            return false;
        }

        $classTeacherAuthorized = SchoolClass::query()
            ->where('school_id', $schoolId)
            ->where('id', $classId)
            ->where('class_teacher_id', $staffId)
            ->exists();

        if ($classTeacherAuthorized) {
            return true;
        }

        return DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', $schoolId)
            ->where('class_subject.class_id', $classId)
            ->where('class_subject.teacher_id', $staffId)
            ->exists();
    }

    public function canManageClassSubject(User $user, int $classId, ?int $subjectId): bool
    {
        if (!$this->canManageClass($user, $classId)) {
            return false;
        }

        if (!$subjectId) {
            return true;
        }

        $schoolId = (int) $user->school_id;
        $subjectExistsInClass = DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', $schoolId)
            ->where('class_subject.class_id', $classId)
            ->where('class_subject.subject_id', $subjectId)
            ->exists();

        if (!$subjectExistsInClass) {
            return false;
        }

        if ($this->hasPrivilegedAcademicRole($user)) {
            return true;
        }

        $staffId = (int) ($user->staffProfile?->id ?? 0);
        if ($staffId <= 0) {
            return false;
        }

        $isClassTeacher = SchoolClass::query()
            ->where('school_id', $schoolId)
            ->where('id', $classId)
            ->where('class_teacher_id', $staffId)
            ->exists();

        if ($isClassTeacher) {
            return true;
        }

        return DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', $schoolId)
            ->where('class_subject.class_id', $classId)
            ->where('class_subject.subject_id', $subjectId)
            ->where('class_subject.teacher_id', $staffId)
            ->exists();
    }

    private function roleValue(User $user): string
    {
        return (string) ($user->role?->value ?? $user->role ?? '');
    }
}
