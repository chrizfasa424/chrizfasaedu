<?php

namespace App\Http\Controllers\Examination;

use App\Enums\UserRole;
use App\Exports\ResultSheetTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\DownloadResultSubmissionTemplateRequest;
use App\Http\Requests\Examination\ReviewResultSubmissionRequest;
use App\Http\Requests\Examination\StoreResultSubmissionRequest;
use App\Http\Requests\Examination\SubmitResultSubmissionRequest;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ExamType;
use App\Models\ResultSubmission;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\ResultSubmissionService;
use App\Services\ResultTemplateService;
use App\Services\StaffClassAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultSubmissionController extends Controller
{
    public function __construct(
        private readonly ResultSubmissionService $submissionService,
        private readonly ResultTemplateService $templateService,
        private readonly StaffClassAuthorizationService $authorizationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $this->ensureCanViewAny($user);
        $isAdmin = $this->isAdminReviewer($user?->role?->value ?? '');
        $authorizedClassIds = $this->authorizationService->authorizedClassIds($user);

        $query = ResultSubmission::query()
            ->with(['teacher', 'schoolClass', 'arm', 'subject', 'session', 'term', 'examType', 'reviewer', 'importer', 'resultBatch'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->input('status')))
            ->when($request->filled('assessment_type'), fn ($q) => $q->where('assessment_type', (string) $request->input('assessment_type')))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', (int) $request->input('class_id')))
            ->latest();

        if (!$isAdmin) {
            $query->where('teacher_id', (int) $user->id);
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('examination.result-submissions.index', array_merge(
            $this->pageContext(),
            [
                'submissions' => $submissions,
                'isAdmin' => $isAdmin,
                'authorizedClassIds' => $authorizedClassIds,
                'statuses' => [
                    ResultSubmission::STATUS_DRAFT,
                    ResultSubmission::STATUS_SUBMITTED,
                    ResultSubmission::STATUS_UNDER_REVIEW,
                    ResultSubmission::STATUS_REJECTED,
                    ResultSubmission::STATUS_APPROVED,
                    ResultSubmission::STATUS_IMPORTED,
                ],
            ]
        ));
    }

    public function store(StoreResultSubmissionRequest $request)
    {
        $this->ensureCanCreate($request->user());

        $submission = $this->submissionService->createSubmission(
            $request->validated(),
            $request->file('file'),
            $request->user()
        );

        $message = $submission->status === ResultSubmission::STATUS_DRAFT
            ? 'Result draft saved successfully.'
            : 'Result submission sent to admin for review.';

        return redirect()
            ->route('examination.result-submissions.show', $submission)
            ->with('success', $message);
    }

    public function show(Request $request, ResultSubmission $resultSubmission)
    {
        $this->ensureCanViewSubmission($request->user(), $resultSubmission);

        $user = $request->user();
        $isAdmin = $this->isAdminReviewer($user?->role?->value ?? '');

        if ($isAdmin && $resultSubmission->status === ResultSubmission::STATUS_SUBMITTED) {
            $resultSubmission = $this->submissionService->markUnderReview($resultSubmission);
        }

        $resultSubmission->load([
            'teacher',
            'schoolClass',
            'arm',
            'subject',
            'session',
            'term',
            'examType',
            'reviewer',
            'importer',
            'resultBatch',
        ]);

        $validation = $this->submissionService->validateSubmission($resultSubmission);
        $sheetPreview = $this->sheetPreview($resultSubmission->file_path);

        return view('examination.result-submissions.show', [
            'submission' => $resultSubmission,
            'validation' => $validation,
            'sheetPreview' => $sheetPreview,
            'isAdmin' => $isAdmin,
            'canEdit' => $request->user()->can('update', $resultSubmission),
        ]);
    }

    public function submit(SubmitResultSubmissionRequest $request, ResultSubmission $resultSubmission)
    {
        $this->ensureCanSubmitDraft($request->user(), $resultSubmission);

        $submission = $this->submissionService->submitDraft(
            $resultSubmission,
            $request->user(),
            $request->input('staff_note')
        );

        return redirect()
            ->route('examination.result-submissions.show', $submission)
            ->with('success', 'Submission sent to admin for review.');
    }

    public function review(ReviewResultSubmissionRequest $request, ResultSubmission $resultSubmission)
    {
        $this->ensureCanReviewSubmission($request->user(), $resultSubmission);

        $submission = $this->submissionService->reviewSubmission(
            $resultSubmission,
            $request->user(),
            (string) $request->input('decision'),
            $request->input('admin_note')
        );

        $message = $submission->status === ResultSubmission::STATUS_APPROVED
            ? 'Submission approved successfully.'
            : 'Submission rejected. Staff can fix and re-submit.';

        return redirect()
            ->route('examination.result-submissions.show', $submission)
            ->with('success', $message);
    }

    public function import(Request $request, ResultSubmission $resultSubmission)
    {
        $this->ensureCanReviewSubmission($request->user(), $resultSubmission);

        $submission = $this->submissionService->importApprovedSubmission($resultSubmission, $request->user());

        return redirect()
            ->route('examination.result-submissions.show', $submission)
            ->with('success', 'Approved submission imported into official result records.');
    }

    public function template(DownloadResultSubmissionTemplateRequest $request)
    {
        $this->ensureCanCreate($request->user());

        $validated = $request->validated();
        $user = $request->user();
        $classId = (int) $validated['class_id'];
        $armId = !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null;
        $assessmentType = (string) $validated['assessment_type'];

        if (!$this->authorizationService->canManageClass($user, $classId)) {
            abort(403, 'You are not authorized to download template for this class.');
        }

        $rows = $this->templateService->buildWideTemplateRows(
            (int) $user->school_id,
            $classId,
            $armId,
            $assessmentType
        );

        $class = SchoolClass::query()->find($classId);
        $classLabel = $class?->grade_level?->label() ?? $class?->name ?? 'class';
        $typeLabel = strtolower(str_replace(' ', '-', $assessmentType));
        $fileName = 'result-submission-template-' . preg_replace('/[^A-Za-z0-9\-_.]/', '-', $classLabel) . '-' . $typeLabel . '.xlsx';

        return Excel::download(new ResultSheetTemplateExport($rows), $fileName);
    }

    public function download(ResultSubmission $resultSubmission)
    {
        $this->ensureCanViewSubmission(auth()->user(), $resultSubmission);

        if (!Storage::disk('local')->exists($resultSubmission->file_path)) {
            return redirect()->back()->withErrors([
                'file' => 'Submission file is no longer available.',
            ]);
        }

        return Storage::disk('local')->download($resultSubmission->file_path, $resultSubmission->original_file_name);
    }

    private function pageContext(): array
    {
        $classes = SchoolClass::query()
            ->with(['arms', 'subjects' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('order')
            ->get();

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sessions = AcademicSession::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $terms = AcademicTerm::query()
            ->with('session')
            ->orderByDesc('is_current')
            ->orderByDesc('id')
            ->get();

        $examTypes = ExamType::query()
            ->where('school_id', (int) auth()->user()->school_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assessmentTypes = [
            'first_test' => 'First Test',
            'second_test' => 'Second Test',
            'exam' => 'Exam',
            'full_result' => 'Full Terminal Result',
        ];

        return compact('classes', 'subjects', 'sessions', 'terms', 'examTypes', 'assessmentTypes');
    }

    private function sheetPreview(string $relativePath): array
    {
        if (!Storage::disk('local')->exists($relativePath)) {
            return [];
        }

        try {
            $path = Storage::disk('local')->path($relativePath);
            $rows = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
            return array_slice($rows, 0, 25);
        } catch (\Throwable) {
            return [];
        }
    }

    private function isAdminReviewer(string $role): bool
    {
        return in_array((string) $role, [
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
        $this->ensureCanViewAny($user);
    }

    private function ensureCanViewSubmission($user, ResultSubmission $submission): void
    {
        $this->ensureCanViewAny($user);

        $sameSchool = (int) $submission->school_id === (int) ($user?->school_id ?? 0);
        $isOwner = (int) $submission->teacher_id === (int) ($user?->id ?? 0);
        $isAdmin = $this->isAdminReviewer($this->roleValue($user));

        abort_unless($sameSchool && ($isOwner || $isAdmin), 403, 'Unauthorized.');
    }

    private function ensureCanSubmitDraft($user, ResultSubmission $submission): void
    {
        $this->ensureCanViewSubmission($user, $submission);

        $isOwner = (int) $submission->teacher_id === (int) ($user?->id ?? 0);
        $canSubmitState = in_array((string) $submission->status, [
            ResultSubmission::STATUS_DRAFT,
            ResultSubmission::STATUS_REJECTED,
        ], true);

        abort_unless($isOwner && $canSubmitState, 403, 'Unauthorized.');
    }

    private function ensureCanReviewSubmission($user, ResultSubmission $submission): void
    {
        $sameSchool = (int) $submission->school_id === (int) ($user?->school_id ?? 0);
        $isAdmin = $this->isAdminReviewer($this->roleValue($user));

        abort_unless($sameSchool && $isAdmin, 403, 'Unauthorized.');
    }
}
