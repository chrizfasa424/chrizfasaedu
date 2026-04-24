<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
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

    public function view(User $user, Assignment $assignment): bool
    {
        return (int) $assignment->school_id === (int) $user->school_id;
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

    public function update(User $user, Assignment $assignment): bool
    {
        if ((int) $assignment->school_id !== (int) $user->school_id) {
            return false;
        }

        return $this->isAdmin($user) || (int) $assignment->teacher_id === (int) $user->id;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }

    public function publish(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }

    private function isAdmin(User $user): bool
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
