<?php

namespace App\Http\Controllers\Academic;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreAssignmentRequest;
use App\Http\Requests\Academic\UpdateAssignmentRequest;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Services\AssignmentService;
use App\Services\StaffClassAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly AssignmentService $assignmentService,
        private readonly StaffClassAuthorizationService $authorizationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $this->ensureCanViewAny($user);
        $isAdmin = $this->isAdminRole((string) ($user?->role?->value ?? ''));
        $authorizedClassIds = $this->authorizationService->authorizedClassIds($user);

        $query = Assignment::query()
            ->with(['teacher', 'subject', 'session', 'term', 'targets.schoolClass', 'targets.arm', 'targets.subject'])
            ->withCount([
                'submissions',
                'submissions as pending_submissions_count' => fn ($q) => $q->whereIn('status', ['submitted', 'resubmitted']),
            ])
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->input('status')))
            ->latest();

        if (!$isAdmin) {
            $query->where(function ($scope) use ($user, $authorizedClassIds) {
                $scope->where('teacher_id', (int) $user->id);

                if (!empty($authorizedClassIds)) {
                    $scope->orWhereHas('targets', function ($targetQuery) use ($authorizedClassIds) {
                        $targetQuery->whereIn('class_id', $authorizedClassIds);
                    });
                }
            });
        }

        $assignments = $query->paginate(20)->withQueryString();

        $classes = SchoolClass::query()
            ->with([
                'arms',
                'subjects' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
            ])
            ->orderBy('order')
            ->get();
        $sessions = AcademicSession::query()->orderByDesc('is_current')->orderByDesc('start_date')->get();
        $terms = AcademicTerm::query()->with('session')->orderByDesc('is_current')->orderByDesc('id')->get();

        return view('academic.assignments.index', [
            'assignments' => $assignments,
            'classes' => $classes,
            'sessions' => $sessions,
            'terms' => $terms,
            'isAdmin' => $isAdmin,
            'authorizedClassIds' => $authorizedClassIds,
        ]);
    }

    public function store(StoreAssignmentRequest $request)
    {
        $this->ensureCanCreate($request->user());

        $assignment = $this->assignmentService->create(
            $request->validated(),
            $request->file('attachment'),
            $request->user()
        );

        return redirect()
            ->route('academic.assignments.index')
            ->with('success', "Assignment '{$assignment->title}' created successfully.");
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $this->ensureCanManageAssignment($request->user(), $assignment);

        $updated = $this->assignmentService->update(
            $assignment,
            $request->validated(),
            $request->file('attachment'),
            $request->user()
        );

        return redirect()
            ->route('academic.assignments.index')
            ->with('success', "Assignment '{$updated->title}' updated successfully.");
    }

    public function publish(Request $request, Assignment $assignment)
    {
        $this->ensureCanManageAssignment($request->user(), $assignment);

        $this->assignmentService->publish($assignment, $request->user());

        return redirect()
            ->route('academic.assignments.index')
            ->with('success', 'Assignment published. Students in target classes can now view it.');
    }

    public function unpublish(Assignment $assignment)
    {
        $this->ensureCanManageAssignment(auth()->user(), $assignment);

        $this->assignmentService->unpublish($assignment);

        return redirect()
            ->route('academic.assignments.index')
            ->with('success', 'Assignment unpublished successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->ensureCanManageAssignment(auth()->user(), $assignment);

        $attachmentPath = $assignment->attachment_path;
        $submissionAttachmentPaths = $assignment->submissions()
            ->whereNotNull('attachment_path')
            ->pluck('attachment_path')
            ->filter()
            ->values()
            ->all();

        $assignment->targets()->delete();
        $assignment->submissions()->delete();
        $assignment->delete();

        $pathsToDelete = collect(array_merge([$attachmentPath], $submissionAttachmentPaths))
            ->filter()
            ->unique()
            ->values();

        foreach ($pathsToDelete as $path) {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }

        return redirect()
            ->route('academic.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    public function download(Assignment $assignment)
    {
        $this->ensureCanViewAssignment(auth()->user(), $assignment);

        if (!$assignment->attachment_path || !Storage::disk('local')->exists($assignment->attachment_path)) {
            return redirect()->back()->withErrors([
                'assignment' => 'Attachment file not found.',
            ]);
        }

        $name = basename((string) $assignment->attachment_path);

        return Storage::disk('local')->download($assignment->attachment_path, $name);
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

    private function roleValue($user): string
    {
        return (string) ($user?->role?->value ?? $user?->role ?? '');
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

    private function ensureCanViewAny($user): void
    {
        abort_unless($user && $this->isAcademicRole($this->roleValue($user)), 403, 'Unauthorized.');
    }

    private function ensureCanCreate($user): void
    {
        abort_unless($user && $this->isAcademicRole($this->roleValue($user)), 403, 'Unauthorized.');
    }

    private function ensureCanViewAssignment($user, Assignment $assignment): void
    {
        $this->ensureCanViewAny($user);
        abort_unless((int) $assignment->school_id === (int) ($user?->school_id ?? 0), 403, 'Unauthorized.');
    }

    private function ensureCanManageAssignment($user, Assignment $assignment): void
    {
        $this->ensureCanViewAssignment($user, $assignment);

        $isOwner = (int) $assignment->teacher_id === (int) ($user?->id ?? 0);
        $isAdmin = $this->isAdminRole($this->roleValue($user));

        abort_unless($isOwner || $isAdmin, 403, 'Unauthorized.');
    }
}
