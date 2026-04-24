<?php

namespace App\Http\Controllers\Academic;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\ReviewAssignmentSubmissionRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Notifications\StudentAssignmentSubmittedNotification;
use App\Services\StaffClassAuthorizationService;
use App\Services\AssignmentSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function __construct(
        private readonly AssignmentSubmissionService $submissionService,
        private readonly StaffClassAuthorizationService $authorizationService
    ) {
    }

    public function index(Request $request, Assignment $assignment)
    {
        $this->ensureCanViewAssignment($request->user(), $assignment);
        $this->markAssignmentSubmissionNotificationsAsRead($request, $assignment);

        $assignment->loadMissing(['subject', 'teacher', 'session', 'term', 'targets.schoolClass', 'targets.arm', 'targets.subject']);

        $submissions = AssignmentSubmission::query()
            ->with(['student.schoolClass', 'student.arm', 'reviewedBy'])
            ->where('assignment_id', (int) $assignment->id)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('academic.assignments.submissions', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'isAdmin' => $this->isAdminRole($this->roleValue($request->user())),
        ]);
    }

    public function review(ReviewAssignmentSubmissionRequest $request, AssignmentSubmission $assignmentSubmission)
    {
        $assignmentSubmission->loadMissing('assignment');

        $this->ensureCanManageAssignment($request->user(), $assignmentSubmission->assignment);

        $this->submissionService->reviewSubmission(
            $assignmentSubmission,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('academic.assignments.submissions.index', $assignmentSubmission->assignment)
            ->with('success', 'Submission review saved successfully.');
    }

    public function download(Request $request, AssignmentSubmission $assignmentSubmission)
    {
        $assignmentSubmission->loadMissing('assignment');

        $this->ensureCanViewAssignment($request->user(), $assignmentSubmission->assignment);

        $path = (string) ($assignmentSubmission->attachment_path ?? '');

        if ($path === '' || !Storage::disk('local')->exists($path)) {
            return redirect()->back()->withErrors([
                'assignment_submission' => 'Submission attachment file not found.',
            ]);
        }

        return Storage::disk('local')->download($path, basename($path));
    }

    private function roleValue($user): string
    {
        return (string) ($user?->role?->value ?? $user?->role ?? '');
    }

    private function isAdminRole(string $role): bool
    {
        return in_array($role, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ], true);
    }

    private function isAcademicRole(string $role): bool
    {
        return in_array($role, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
        ], true);
    }

    private function ensureCanViewAssignment($user, Assignment $assignment): void
    {
        abort_unless($user && $this->isAcademicRole($this->roleValue($user)), 403, 'Unauthorized.');
        abort_unless((int) $assignment->school_id === (int) ($user?->school_id ?? 0), 403, 'Unauthorized.');

        if ($this->isAdminRole($this->roleValue($user))) {
            return;
        }

        if ((int) $assignment->teacher_id === (int) ($user?->id ?? 0)) {
            return;
        }

        $assignment->loadMissing('targets');

        $authorizedByTarget = $assignment->targets->contains(function ($target) use ($user) {
            return $this->authorizationService->canManageClassSubject(
                $user,
                (int) $target->class_id,
                !empty($target->subject_id) ? (int) $target->subject_id : null
            );
        });

        abort_unless($authorizedByTarget, 403, 'Unauthorized.');
    }

    private function ensureCanManageAssignment($user, Assignment $assignment): void
    {
        $this->ensureCanViewAssignment($user, $assignment);
    }

    private function markAssignmentSubmissionNotificationsAsRead(Request $request, Assignment $assignment): void
    {
        $user = $request->user();
        if (!$user) {
            return;
        }

        try {
            $user->unreadNotifications()
                ->where('type', StudentAssignmentSubmittedNotification::class)
                ->where('data->assignment_id', (int) $assignment->id)
                ->update(['read_at' => now()]);
        } catch (\Throwable) {
            // Non-blocking: notification table/json differences should not break page load.
        }
    }
}
