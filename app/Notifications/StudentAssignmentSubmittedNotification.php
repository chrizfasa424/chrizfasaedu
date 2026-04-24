<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentAssignmentSubmittedNotification extends Notification
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
        return [
            'assignment_id' => (int) $this->assignment->id,
            'assignment_title' => (string) $this->assignment->title,
            'student_id' => (int) $this->student->id,
            'student_name' => (string) $this->student->full_name,
            'student_admission_number' => (string) ($this->student->admission_number ?? ''),
            'submission_id' => (int) $this->submission->id,
            'status' => (string) $this->submission->status,
            'submitted_at' => optional($this->submission->submitted_at)?->toIso8601String(),
            'route' => route('academic.assignments.submissions.index', $this->assignment),
            'message' => sprintf(
                '%s submitted assignment "%s".',
                (string) $this->student->full_name,
                (string) $this->assignment->title
            ),
        ];
    }
}
