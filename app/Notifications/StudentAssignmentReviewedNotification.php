<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentAssignmentReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Assignment $assignment,
        private readonly Student $student,
        private readonly AssignmentSubmission $submission
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = sprintf(
            'Your assignment "%s" has been reviewed by your teacher.',
            (string) $this->assignment->title
        );

        if (!is_null($this->submission->score)) {
            $message .= ' Score: ' . number_format((float) $this->submission->score, 2) . '.';
        }

        return [
            'assignment_id' => (int) $this->assignment->id,
            'assignment_title' => (string) $this->assignment->title,
            'student_id' => (int) $this->student->id,
            'student_name' => (string) $this->student->full_name,
            'submission_id' => (int) $this->submission->id,
            'status' => (string) $this->submission->status,
            'score' => $this->submission->score,
            'teacher_feedback' => (string) ($this->submission->teacher_feedback ?? ''),
            'reviewed_at' => optional($this->submission->reviewed_at)?->toIso8601String(),
            'route' => route('portal.assignments.show', $this->assignment),
            'message' => $message,
        ];
    }
}
