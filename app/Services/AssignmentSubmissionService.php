<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentAssignmentReviewedNotification;
use App\Notifications\StudentAssignmentSubmittedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionService
{
    public function submitByStudent(Assignment $assignment, Student $student, array $payload, ?UploadedFile $attachment): AssignmentSubmission
    {
        return DB::transaction(function () use ($assignment, $student, $payload, $attachment) {
            $submission = AssignmentSubmission::query()->firstOrNew([
                'assignment_id' => (int) $assignment->id,
                'student_id' => (int) $student->id,
            ]);

            $existingAttachment = (string) ($submission->attachment_path ?? '');
            $newAttachment = $existingAttachment !== '' ? $existingAttachment : null;
            $newAttachmentType = $submission->attachment_type;

            if ($attachment) {
                $newAttachment = $attachment->storeAs(
                    'assignments/submissions',
                    now()->format('YmdHis') . '-' . uniqid('', true) . '-' . $attachment->getClientOriginalName(),
                    'local'
                );
                $newAttachmentType = strtolower((string) $attachment->getClientOriginalExtension());

                if ($existingAttachment !== '' && $existingAttachment !== $newAttachment && Storage::disk('local')->exists($existingAttachment)) {
                    Storage::disk('local')->delete($existingAttachment);
                }
            }

            $isExisting = $submission->exists;

            $submission->fill([
                'school_id' => (int) $student->school_id,
                'submission_text' => trim((string) ($payload['submission_text'] ?? '')) ?: null,
                'attachment_path' => $newAttachment,
                'attachment_type' => $newAttachmentType,
                'submitted_at' => now(),
                'status' => $isExisting ? AssignmentSubmission::STATUS_RESUBMITTED : AssignmentSubmission::STATUS_SUBMITTED,
                'score' => null,
                'teacher_feedback' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            $submission->save();

            $submission = $submission->fresh(['student', 'assignment.teacher']);

            $teacherUser = $submission->assignment?->teacher;
            if ($teacherUser && (int) $teacherUser->id > 0) {
                try {
                    $teacherUser->notify(new StudentAssignmentSubmittedNotification(
                        $submission->assignment,
                        $submission->student,
                        $submission
                    ));
                } catch (\Throwable) {
                    // Notification failure should not block submission save.
                }
            }

            return $submission;
        });
    }

    public function reviewSubmission(AssignmentSubmission $submission, User $reviewer, array $payload): AssignmentSubmission
    {
        return DB::transaction(function () use ($submission, $reviewer, $payload) {
            $scoreValue = $payload['score'] ?? null;
            $score = ($scoreValue === null || $scoreValue === '')
                ? null
                : round((float) $scoreValue, 2);

            $submission->update([
                'score' => $score,
                'teacher_feedback' => !empty($payload['teacher_feedback'])
                    ? trim((string) $payload['teacher_feedback'])
                    : null,
                'reviewed_by' => (int) $reviewer->id,
                'reviewed_at' => now(),
                'status' => AssignmentSubmission::STATUS_REVIEWED,
            ]);

            $submission = $submission->fresh(['student.user', 'assignment', 'reviewedBy']);

            $studentUser = $submission->student?->user;
            if ($studentUser && (int) $studentUser->id > 0) {
                try {
                    $studentUser->unreadNotifications()
                        ->where('type', StudentAssignmentReviewedNotification::class)
                        ->where('data->submission_id', (int) $submission->id)
                        ->update(['read_at' => now()]);

                    $studentUser->notify(new StudentAssignmentReviewedNotification(
                        $submission->assignment,
                        $submission->student,
                        $submission
                    ));
                } catch (\Throwable) {
                    // Notification failure should not block review save.
                }
            }

            return $submission;
        });
    }

    public function saveStudentFeedback(AssignmentSubmission $submission, string $feedback): AssignmentSubmission
    {
        $submission->update([
            'student_feedback' => trim($feedback),
            'student_feedback_at' => now(),
        ]);

        return $submission->fresh(['student', 'assignment', 'reviewedBy']);
    }
}
