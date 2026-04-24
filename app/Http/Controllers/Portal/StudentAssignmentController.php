<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreStudentAssignmentFeedbackRequest;
use App\Http\Requests\Portal\StoreStudentAssignmentSubmissionRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Notifications\StudentAssignmentReviewedNotification;
use App\Services\AssignmentSubmissionService;

class StudentAssignmentController extends Controller
{
    public function __construct(
        private readonly AssignmentSubmissionService $submissionService
    ) {
    }

    protected function portalUser()
    {
        return auth('portal')->user() ?? auth()->user();
    }

    public function show(Assignment $assignment)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        $assignment = $this->resolveVisibleAssignmentForStudent($assignment, $student);
        $this->markAssignmentReviewNotificationsAsRead($user, $assignment);

        $submission = AssignmentSubmission::query()
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->first();

        return view('portal.student.assignment-show', [
            'student' => $student,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    public function submit(StoreStudentAssignmentSubmissionRequest $request, Assignment $assignment)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        $assignment = $this->resolveVisibleAssignmentForStudent($assignment, $student);

        $this->submissionService->submitByStudent(
            $assignment,
            $student,
            $request->validated(),
            $request->file('attachment')
        );

        return redirect()
            ->route('portal.assignments.show', $assignment)
            ->with('success', 'Assignment submitted successfully.');
    }

    public function feedback(StoreStudentAssignmentFeedbackRequest $request, Assignment $assignment)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        $assignment = $this->resolveVisibleAssignmentForStudent($assignment, $student);

        $submission = AssignmentSubmission::query()
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->first();

        if (!$submission) {
            return redirect()
                ->route('portal.assignments.show', $assignment)
                ->withErrors([
                    'student_feedback' => 'Submit your assignment first before sending feedback.',
                ]);
        }

        $this->submissionService->saveStudentFeedback(
            $submission,
            (string) $request->validated()['student_feedback']
        );

        return redirect()
            ->route('portal.assignments.show', $assignment)
            ->with('success', 'Feedback sent successfully.');
    }

    private function resolveVisibleAssignmentForStudent(Assignment $assignment, Student $student): Assignment
    {
        $visible = Assignment::query()
            ->with(['subject', 'session', 'term', 'teacher', 'targets.schoolClass', 'targets.arm', 'targets.subject'])
            ->where('id', (int) $assignment->id)
            ->where('school_id', (int) $student->school_id)
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->whereHas('targets', function ($query) use ($student) {
                $query->where('class_id', (int) $student->class_id)
                    ->where(function ($scope) use ($student) {
                        $scope->whereNull('arm_id');
                        if (!empty($student->arm_id)) {
                            $scope->orWhere('arm_id', (int) $student->arm_id);
                        }
                    });
            })
            ->first();

        abort_unless($visible, 403, 'Unauthorized assignment access.');

        return $visible;
    }

    private function markAssignmentReviewNotificationsAsRead($user, Assignment $assignment): void
    {
        if (!$user) {
            return;
        }

        try {
            $user->unreadNotifications()
                ->where('type', StudentAssignmentReviewedNotification::class)
                ->where('data->assignment_id', (int) $assignment->id)
                ->update(['read_at' => now()]);
        } catch (\Throwable) {
            // Non-blocking: notification read state should not break page load.
        }
    }
}
