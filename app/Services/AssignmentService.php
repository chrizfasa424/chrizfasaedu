<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\ClassArm;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    public function __construct(
        private readonly StaffClassAuthorizationService $authorizationService
    ) {
    }

    public function create(array $payload, UploadedFile $attachment, User $actor): Assignment
    {
        $targets = $this->normalizeTargets($payload['targets'] ?? []);

        if (empty($targets)) {
            throw ValidationException::withMessages([
                'targets' => 'Select at least one class target for this assignment.',
            ]);
        }

        $this->validateTargetsAuthorization($actor, $targets);

        $attachmentPath = $attachment->storeAs(
            'assignments/attachments',
            now()->format('YmdHis') . '-' . uniqid('', true) . '-' . $attachment->getClientOriginalName(),
            'local'
        );

        return DB::transaction(function () use ($payload, $actor, $targets, $attachmentPath, $attachment) {
            $status = (string) ($payload['status'] ?? Assignment::STATUS_DRAFT);
            if (!in_array($status, [Assignment::STATUS_DRAFT, Assignment::STATUS_PUBLISHED, Assignment::STATUS_UNPUBLISHED], true)) {
                $status = Assignment::STATUS_DRAFT;
            }

            $assignment = Assignment::query()->create([
                'school_id' => (int) $actor->school_id,
                'teacher_id' => (int) $actor->id,
                'subject_id' => null,
                'session_id' => !empty($payload['session_id']) ? (int) $payload['session_id'] : null,
                'term_id' => !empty($payload['term_id']) ? (int) $payload['term_id'] : null,
                'title' => (string) $payload['title'],
                'description' => $payload['description'] ?? null,
                'due_date' => $payload['due_date'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_type' => strtolower((string) $attachment->getClientOriginalExtension()),
                'status' => $status,
                'published_by' => $status === Assignment::STATUS_PUBLISHED ? (int) $actor->id : null,
                'published_at' => $status === Assignment::STATUS_PUBLISHED ? now() : null,
            ]);

            $assignment->targets()->createMany($targets);

            return $assignment;
        });
    }

    public function update(Assignment $assignment, array $payload, ?UploadedFile $attachment, User $actor): Assignment
    {
        $targets = $this->normalizeTargets($payload['targets'] ?? []);

        if (empty($targets)) {
            throw ValidationException::withMessages([
                'targets' => 'Select at least one class target for this assignment.',
            ]);
        }

        $this->validateTargetsAuthorization($actor, $targets);

        return DB::transaction(function () use ($assignment, $payload, $attachment, $actor, $targets) {
            $updates = [
                'subject_id' => null,
                'session_id' => !empty($payload['session_id']) ? (int) $payload['session_id'] : null,
                'term_id' => !empty($payload['term_id']) ? (int) $payload['term_id'] : null,
                'title' => (string) $payload['title'],
                'description' => $payload['description'] ?? null,
                'due_date' => $payload['due_date'] ?? null,
            ];

            if ($attachment) {
                $oldPath = $assignment->attachment_path;
                $newPath = $attachment->storeAs(
                    'assignments/attachments',
                    now()->format('YmdHis') . '-' . uniqid('', true) . '-' . $attachment->getClientOriginalName(),
                    'local'
                );

                $updates['attachment_path'] = $newPath;
                $updates['attachment_type'] = strtolower((string) $attachment->getClientOriginalExtension());

                if ($oldPath && Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }
            }

            $assignment->update($updates);

            $assignment->targets()->delete();
            $assignment->targets()->createMany($targets);

            if (!empty($payload['status'])) {
                $status = (string) $payload['status'];
                if ($status === Assignment::STATUS_PUBLISHED) {
                    $this->publish($assignment, $actor);
                } elseif ($status === Assignment::STATUS_UNPUBLISHED) {
                    $this->unpublish($assignment);
                }
            }

            return $assignment;
        });
    }

    public function publish(Assignment $assignment, User $actor): Assignment
    {
        $assignment->update([
            'status' => Assignment::STATUS_PUBLISHED,
            'published_by' => (int) $actor->id,
            'published_at' => now(),
        ]);

        return $assignment->fresh();
    }

    public function unpublish(Assignment $assignment): Assignment
    {
        $assignment->update([
            'status' => Assignment::STATUS_UNPUBLISHED,
            'published_by' => null,
            'published_at' => null,
        ]);

        return $assignment->fresh();
    }

    private function normalizeTargets(array $targets): array
    {
        return collect($targets)
            ->map(function ($target) {
                $classId = (int) ($target['class_id'] ?? 0);
                $armId = !empty($target['arm_id']) ? (int) $target['arm_id'] : null;
                $subjectId = (int) ($target['subject_id'] ?? 0);

                if ($classId <= 0 || $subjectId <= 0) {
                    return null;
                }

                return [
                    'class_id' => $classId,
                    'arm_id' => $armId,
                    'subject_id' => $subjectId,
                ];
            })
            ->filter()
            ->unique(fn ($target) => $target['class_id'] . ':' . ($target['arm_id'] ?? 'all') . ':' . $target['subject_id'])
            ->values()
            ->all();
    }

    private function validateTargetsAuthorization(User $actor, array $targets): void
    {
        $useTeacherFallbackScope = $this->shouldUseTeacherFallbackScope($actor);

        foreach ($targets as $index => $target) {
            $classId = (int) $target['class_id'];
            $armId = !empty($target['arm_id']) ? (int) $target['arm_id'] : null;
            $subjectId = (int) ($target['subject_id'] ?? 0);

            if ($subjectId <= 0) {
                throw ValidationException::withMessages([
                    "targets.{$index}.subject_id" => 'Select a subject for this class target.',
                ]);
            }

            if ($useTeacherFallbackScope) {
                $classExistsInSchool = SchoolClass::query()
                    ->where('school_id', (int) $actor->school_id)
                    ->where('id', $classId)
                    ->exists();

                if (!$classExistsInSchool) {
                    throw ValidationException::withMessages([
                        "targets.{$index}.class_id" => 'You are not authorized to assign work to this class/subject.',
                    ]);
                }

                $subjectExistsInClass = DB::table('class_subject')
                    ->join('classes', 'classes.id', '=', 'class_subject.class_id')
                    ->where('classes.school_id', (int) $actor->school_id)
                    ->where('class_subject.class_id', $classId)
                    ->where('class_subject.subject_id', $subjectId)
                    ->exists();

                if (!$subjectExistsInClass) {
                    throw ValidationException::withMessages([
                        "targets.{$index}.subject_id" => 'Selected subject is not offered in the selected class.',
                    ]);
                }
            } elseif (!$this->authorizationService->canManageClassSubject($actor, $classId, $subjectId)) {
                throw ValidationException::withMessages([
                    "targets.{$index}.subject_id" => 'You are not authorized to assign work to this class/subject.',
                ]);
            }

            if ($armId) {
                $validArm = ClassArm::query()
                    ->where('id', $armId)
                    ->where('class_id', $classId)
                    ->exists();

                if (!$validArm) {
                    throw ValidationException::withMessages([
                        "targets.{$index}.arm_id" => 'Selected arm does not belong to the selected class target.',
                    ]);
                }
            }
        }
    }

    private function shouldUseTeacherFallbackScope(User $actor): bool
    {
        $roleValue = (string) ($actor->role?->value ?? $actor->role ?? '');
        if ($roleValue !== UserRole::TEACHER->value) {
            return false;
        }

        return empty($this->authorizationService->authorizedClassIds($actor));
    }
}
