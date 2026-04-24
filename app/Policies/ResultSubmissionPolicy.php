<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ResultSubmission;
use App\Models\User;

class ResultSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($this->roleValue($user), [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
        ], true);
    }

    public function view(User $user, ResultSubmission $submission): bool
    {
        if ((int) $submission->school_id !== (int) $user->school_id) {
            return false;
        }

        return $this->isAdminReviewer($user) || (int) $submission->teacher_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($this->roleValue($user), [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
        ], true);
    }

    public function update(User $user, ResultSubmission $submission): bool
    {
        if ((int) $submission->school_id !== (int) $user->school_id) {
            return false;
        }

        if ($this->isAdminReviewer($user)) {
            return true;
        }

        return (int) $submission->teacher_id === (int) $user->id
            && in_array($submission->status, [ResultSubmission::STATUS_DRAFT, ResultSubmission::STATUS_REJECTED], true);
    }

    public function review(User $user, ResultSubmission $submission): bool
    {
        return (int) $submission->school_id === (int) $user->school_id
            && $this->isAdminReviewer($user);
    }

    public function import(User $user, ResultSubmission $submission): bool
    {
        return (int) $submission->school_id === (int) $user->school_id
            && $this->isAdminReviewer($user);
    }

    private function isAdminReviewer(User $user): bool
    {
        return in_array($this->roleValue($user), [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ], true);
    }

    private function roleValue(User $user): string
    {
        return (string) ($user->role?->value ?? $user->role ?? '');
    }
}
