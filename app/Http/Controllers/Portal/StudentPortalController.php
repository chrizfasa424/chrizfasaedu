<?php

namespace App\Http\Controllers\Portal;

use App\Enums\UserRole;
use App\Exports\ResultsExport;
use App\Http\Requests\Portal\StoreStudentResultFeedbackRequest;
use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\AcademicTerm;
use App\Models\ReportCard;
use App\Models\Result;
use App\Models\Assignment;
use App\Models\Invoice;
use App\Models\StudentResult;
use App\Models\StudentResultFeedback;
use App\Models\StudentAttendance;
use App\Models\Testimonial;
use App\Models\Timetable;
use App\Notifications\StudentAssignmentReviewedNotification;
use App\Services\GradingService;
use App\Services\Payments\StudentFeeSyncService;
use App\Support\PublicPageContent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentPortalController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService,
        private readonly StudentFeeSyncService $studentFeeSyncService
    )
    {
    }

    protected function portalUser()
    {
        return auth('portal')->user() ?? auth()->user();
    }

    public function dashboard()
    {
        $user = $this->portalUser();
        $student = $user->student;
        $school = $user->school;
        $student?->loadMissing(['schoolClass', 'arm']);

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }
        $session = $school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();
        $publicPage = PublicPageContent::forSchool($school);

        $this->studentFeeSyncService->syncCurrentForStudent($student, $school);

        // Results for current term only (approved)
        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('is_approved', true)
            ->when($term, fn($q) => $q->where('term_id', $term->id))
            ->get();

        // Published report cards for this student
        $reportCards = ReportCard::with(['term.session'])
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->orderByDesc('id')
            ->get();

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->where('session_id', $session?->id)
            ->where('term_id', $term?->id)
            ->get();

        $invoices = Invoice::where('student_id', $student->id)
            ->where('school_id', $school->id)
            ->latest()
            ->take(5)
            ->get();

        $timetable = $this->buildStudentTimetable(
            $student,
            $session?->id ? (int) $session->id : null,
            $term?->id ? (int) $term->id : null
        );

        $studentTestimonials = Testimonial::query()
            ->where('school_id', $school->id)
            ->where('full_name', $student->full_name)
            ->latest()
            ->take(8)
            ->get();

        return view('portal.student.dashboard', compact(
            'student',
            'results',
            'reportCards',
            'term',
            'attendance',
            'invoices',
            'timetable',
            'publicPage',
            'studentTestimonials'
        ));
    }

    public function timetable(Request $request)
    {
        $user = $this->portalUser();
        $student = $user?->student;
        $school = $user?->school;
        $student?->loadMissing(['schoolClass', 'arm']);

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $session = $school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        $dayOptions = ['all', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $selectedDay = strtolower(trim((string) $request->input('day', 'all')));
        if (!in_array($selectedDay, $dayOptions, true)) {
            $selectedDay = 'all';
        }

        $timetable = $this->buildStudentTimetable(
            $student,
            $session?->id ? (int) $session->id : null,
            $term?->id ? (int) $term->id : null
        );

        if ($selectedDay !== 'all') {
            $label = ucfirst($selectedDay);
            $timetable = collect([
                $label => $timetable->get($label, collect()),
            ])->filter(fn ($slots) => $slots->isNotEmpty());
        }

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $timetable = collect($dayOrder)
            ->mapWithKeys(fn (string $day) => [$day => $timetable->get($day, collect())])
            ->filter(fn ($slots) => $slots->isNotEmpty());

        $periodCount = $timetable->flatten(1)->count();
        $activeDaysCount = $timetable->count();

        return view('portal.student.timetable', [
            'student' => $student,
            'term' => $term,
            'session' => $session,
            'timetable' => $timetable,
            'selectedDay' => $selectedDay,
            'periodCount' => $periodCount,
            'activeDaysCount' => $activeDaysCount,
        ]);
    }

    public function assignments(Request $request)
    {
        $user = $this->portalUser();
        $student = $user?->student;
        $school = $user?->school;
        $student?->loadMissing(['schoolClass', 'arm']);

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $this->markAssignmentReviewNotificationsAsRead($user);

        $assignments = Assignment::query()
            ->with([
                'subject',
                'session',
                'term',
                'teacher',
                'targets.schoolClass',
                'targets.arm',
                'targets.subject',
                'submissions' => fn ($q) => $q
                    ->where('student_id', (int) $student->id)
                    ->latest('submitted_at')
                    ->latest('id'),
            ])
            ->where('school_id', (int) $school->id)
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
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('id')
            ->paginate(20);

        return view('portal.student.assignments', [
            'student' => $student,
            'assignments' => $assignments,
        ]);
    }

    private function markAssignmentReviewNotificationsAsRead($user): void
    {
        if (!$user) {
            return;
        }

        try {
            $user->unreadNotifications()
                ->where('type', StudentAssignmentReviewedNotification::class)
                ->update(['read_at' => now()]);
        } catch (\Throwable) {
            // Non-blocking: notification read state should not break page load.
        }
    }

    public function downloadAssignment(Assignment $assignment)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        $isVisible = Assignment::query()
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
            ->exists();

        abort_unless($isVisible, 403, 'Unauthorized assignment access.');

        if (!$assignment->attachment_path || !Storage::disk('local')->exists($assignment->attachment_path)) {
            return redirect()->route('portal.assignments')->withErrors([
                'assignment' => 'Attachment file not found.',
            ]);
        }

        $downloadName = basename((string) $assignment->attachment_path);

        return Storage::disk('local')->download($assignment->attachment_path, $downloadName);
    }

    protected function buildStudentTimetable($student, ?int $sessionId, ?int $termId)
    {
        $entries = Timetable::query()
            ->with(['subject', 'teacher.user', 'arm'])
            ->where('class_id', (int) $student->class_id)
            ->where('is_active', true)
            ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->when(
                $student->arm_id,
                fn ($q) => $q->where(function ($scope) use ($student) {
                    $scope->whereNull('arm_id')
                        ->orWhere('arm_id', (int) $student->arm_id);
                }),
                fn ($q) => $q->whereNull('arm_id')
            )
            ->orderByRaw("FIELD(LOWER(day_of_week), 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('start_time')
            ->get();

        return $entries
            ->sortBy(fn (Timetable $slot) => $slot->arm_id ? 0 : 1)
            ->unique(fn (Timetable $slot) => strtolower((string) $slot->day_of_week) . '|' . $slot->start_time . '|' . $slot->end_time)
            ->sortBy([
                fn (Timetable $slot) => array_search(strtolower((string) $slot->day_of_week), ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], true),
                'start_time',
            ])
            ->values()
            ->groupBy(fn (Timetable $slot) => ucfirst(strtolower((string) $slot->day_of_week)));
    }

    public function reportCard($termId)
    {
        $user    = $this->portalUser();
        $student = $user->student;
        $school  = $user->school;

        if (!$student) abort(403, 'Student profile not available.');

        $reportCard = ReportCard::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('is_published', true)
            ->firstOrFail();

        $results = \App\Models\Result::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('is_approved', true)
            ->get();

        $term = AcademicTerm::with('session')->find($termId);

        return view('portal.student.report-card', compact('student', 'reportCard', 'results', 'school', 'term'));
    }

    public function downloadReportCard($termId)
    {
        $user    = $this->portalUser();
        $student = $user->student;
        $school  = $user->school;

        if (!$student) abort(403, 'Student profile not available.');

        $reportCard = ReportCard::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('is_published', true)
            ->firstOrFail();

        $results = \App\Models\Result::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('is_approved', true)
            ->get();

        $term = AcademicTerm::with('session')->find($termId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pdf.report-card',
            compact('student', 'reportCard', 'results', 'school', 'term')
        );

        return $pdf->download("report-card-{$student->admission_number}-term{$termId}.pdf");
    }

    public function downloadResultExcel($termId)
    {
        $user    = $this->portalUser();
        $student = $user->student;

        if (!$student) abort(403, 'Student profile not available.');

        ReportCard::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->where('is_published', true)
            ->firstOrFail(); // must be published

        $term     = AcademicTerm::with('session')->findOrFail($termId);
        $filename = "result-{$student->admission_number}-{$term->name}.xlsx";
        $filename = preg_replace('/[^A-Za-z0-9\-_. ]/', '', $filename);

        return Excel::download(
            new ResultsExport($student->class_id, $termId, $student->school_id, $student->id),
            $filename
        );
    }

    public function resultsCenter(Request $request)
    {
        $user = $this->portalUser();
        $student = $user?->student;
        $school = $user?->school;

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $selectedTermSlot = strtolower(trim((string) $request->input('term_slot', '')));
        if (!in_array($selectedTermSlot, ['first', 'second', 'third'], true)) {
            $selectedTermSlot = '';
        }

        $selectedViewType = strtolower(trim((string) $request->input('view_type', '')));
        if (!in_array($selectedViewType, ['exam', 'test', 'terminal'], true)) {
            $selectedViewType = '';
        }

        $isFilterApplied = (string) $request->input('filter') === '1';

        $publishedScope = function ($query) {
            $query->whereNotNull('first_test_published_at')
                ->orWhereNotNull('second_test_published_at')
                ->orWhereNotNull('exam_published_at')
                ->orWhereNotNull('full_result_published_at')
                ->orWhere('is_published', true)
                ->orWhereNotNull('published_at');
        };

        $basePublishedQuery = StudentResult::query()
            ->where('school_id', (int) $school->id)
            ->where('student_id', (int) $student->id)
            ->where($publishedScope);

        $componentVisibility = [
            'first_test' => false,
            'second_test' => false,
            'exam' => false,
            'full_result' => false,
        ];

        $termOptions = [
            'first' => 'First Term',
            'second' => 'Second Term',
            'third' => 'Third Term',
        ];

        $viewTypeOptions = [
            'exam' => 'Exam',
            'test' => 'Test',
            'terminal' => 'Terminal',
        ];

        $sheets = collect();
        $activeSheet = null;
        $selectionNotice = null;
        $emptyStateMessage = 'No published result found for the selected filter.';

        if ($isFilterApplied && $selectedTermSlot !== '' && $selectedViewType !== '') {
            $matchingTermIds = AcademicTerm::query()
                ->where('school_id', (int) $school->id)
                ->get()
                ->filter(fn (AcademicTerm $term) => $this->matchesTermSlot((string) $term->name, $selectedTermSlot))
                ->pluck('id')
                ->values()
                ->all();

            if (!empty($matchingTermIds)) {
                $sheets = (clone $basePublishedQuery)
                    ->whereIn('term_id', $matchingTermIds)
                    ->with([
                        'session',
                        'term',
                        'examType',
                        'schoolClass',
                        'arm',
                        'promotedToClass',
                        'items.subject',
                    ])
                    ->orderByDesc('id')
                    ->get();
            }

            $selectedSheetId = (int) $request->integer('sheet_id');
            $activeSheet = $selectedSheetId > 0 ? $sheets->firstWhere('id', $selectedSheetId) : null;
            if (!$activeSheet) {
                $activeSheet = $sheets->first();
            }

            if ($activeSheet) {
                $terminalPublished = !is_null($activeSheet->full_result_published_at)
                    || (bool) $activeSheet->is_published
                    || !is_null($activeSheet->published_at);

                $availableStages = [
                    'first_test' => !is_null($activeSheet->first_test_published_at) || $terminalPublished,
                    'second_test' => !is_null($activeSheet->second_test_published_at) || $terminalPublished,
                    'exam' => !is_null($activeSheet->exam_published_at) || $terminalPublished,
                    'full_result' => $terminalPublished,
                ];

                if ($selectedViewType === 'exam') {
                    if ($availableStages['exam']) {
                        $componentVisibility['exam'] = true;
                    } else {
                        $selectionNotice = 'Exam result is not published for the selected term yet.';
                    }
                } elseif ($selectedViewType === 'test') {
                    if ($availableStages['first_test'] || $availableStages['second_test']) {
                        $componentVisibility['first_test'] = $availableStages['first_test'];
                        $componentVisibility['second_test'] = $availableStages['second_test'];
                    } else {
                        $selectionNotice = 'Test result is not published for the selected term yet.';
                    }
                } else {
                    if ($availableStages['full_result']) {
                        $componentVisibility['full_result'] = true;
                    } else {
                        $selectionNotice = 'Terminal result is not published for the selected term yet.';
                    }
                }
            }
        } elseif ($isFilterApplied) {
            $selectionNotice = 'Please select Term and Exam Term/Test, then click Filter.';
        }

        return view('portal.student.results-center', [
            'student' => $student,
            'school' => $school,
            'isFilterApplied' => $isFilterApplied,
            'termOptions' => $termOptions,
            'selectedTermSlot' => $selectedTermSlot,
            'sheets' => $sheets,
            'activeSheet' => $activeSheet,
            'componentVisibility' => $componentVisibility,
            'selectionNotice' => $selectionNotice,
            'emptyStateMessage' => $emptyStateMessage,
            'selectedViewType' => $selectedViewType,
            'viewTypeOptions' => $viewTypeOptions,
        ]);
    }

    protected function matchesTermSlot(string $termName, string $slot): bool
    {
        $normalized = Str::lower(trim($termName));
        if ($normalized === '') {
            return false;
        }

        return match ($slot) {
            'first' => str_contains($normalized, 'first'),
            'second' => str_contains($normalized, 'second'),
            'third' => str_contains($normalized, 'third'),
            default => false,
        };
    }

    public function resultFeedbackCenter(Request $request)
    {
        $user = $this->portalUser();
        $student = $user?->student;
        $school = $user?->school;

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $publishedScope = function ($query) {
            $query->whereNotNull('first_test_published_at')
                ->orWhereNotNull('second_test_published_at')
                ->orWhereNotNull('exam_published_at')
                ->orWhereNotNull('full_result_published_at')
                ->orWhere('is_published', true)
                ->orWhereNotNull('published_at');
        };

        $sheets = StudentResult::query()
            ->where('school_id', (int) $school->id)
            ->where('student_id', (int) $student->id)
            ->where($publishedScope)
            ->with(['term.session', 'examType', 'items.subject'])
            ->orderByDesc('id')
            ->get();

        $activeSheet = $sheets->first();

        $feedbackSubjects = collect();
        if ($activeSheet) {
            $feedbackSubjects = $activeSheet->items
                ->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();
        }

        $feedbackHistory = StudentResultFeedback::query()
            ->with(['term.session', 'examType', 'subject', 'studentResult'])
            ->where('school_id', (int) $school->id)
            ->where('student_id', (int) $student->id)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $unreadResponses = StudentResultFeedback::query()
            ->with(['term.session', 'examType', 'subject'])
            ->where('school_id', (int) $school->id)
            ->where('student_id', (int) $student->id)
            ->where('feedback_type', 'query')
            ->whereNotNull('admin_response')
            ->whereNotNull('responded_at')
            ->whereNull('student_read_at')
            ->latest('responded_at')
            ->limit(20)
            ->get();

        return view('portal.student.result-feedback', [
            'student' => $student,
            'school' => $school,
            'sheets' => $sheets,
            'activeSheet' => $activeSheet,
            'feedbackSubjects' => $feedbackSubjects,
            'feedbackHistory' => $feedbackHistory,
            'unreadResponses' => $unreadResponses,
        ]);
    }

    public function downloadResultSheetPdf(StudentResult $studentResult)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        abort_if((int) $studentResult->student_id !== (int) $student->id, 403, 'Unauthorized.');
        $terminalPublished = !is_null($studentResult->full_result_published_at)
            || (bool) $studentResult->is_published
            || !is_null($studentResult->published_at);
        abort_unless($terminalPublished, 403, 'Terminal result has not been published yet.');

        $studentResult->load([
            'student',
            'schoolClass',
            'arm',
            'session',
            'term',
            'examType',
            'promotedToClass',
            'items.subject',
        ]);

        $interpretation = $this->gradingService->interpretationForSchool(
            (int) $studentResult->school_id,
            (int) $studentResult->class_id,
            (string) $studentResult->section
        );

        $pdf = Pdf::loadView('examination.result-sheets.student-print', [
            'sheet' => $studentResult,
            'school' => $user->school,
            'interpretation' => $interpretation,
            'asPdf' => true,
            'respectVisibility' => true,
        ]);

        $identity = (string) ($studentResult->student?->admission_number ?: $studentResult->student_id);
        $identity = preg_replace('/[\\\\\\/]+/', '-', $identity);
        $identity = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $identity);
        $identity = trim((string) $identity, '-_.');
        if ($identity === '') {
            $identity = (string) $studentResult->student_id;
        }

        return $pdf->download('my-result-sheet-' . $identity . '.pdf');
    }

    public function storeResultFeedback(StoreStudentResultFeedbackRequest $request)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        $validated = $request->validated();

        $parentFeedback = null;
        $parentFeedbackId = (int) $request->integer('parent_feedback_id');
        if ($parentFeedbackId > 0) {
            $parentFeedback = StudentResultFeedback::query()
                ->where('id', $parentFeedbackId)
                ->where('school_id', (int) $student->school_id)
                ->where('student_id', (int) $student->id)
                ->firstOrFail();
        }

        $studentResult = null;
        if (!empty($validated['student_result_id'])) {
            $studentResult = StudentResult::query()
                ->where('id', (int) $validated['student_result_id'])
                ->where('student_id', (int) $student->id)
                ->where('school_id', (int) $student->school_id)
                ->firstOrFail();
        }

        StudentResultFeedback::query()->create([
            'school_id' => (int) $student->school_id,
            'student_id' => (int) $student->id,
            'student_result_id' => $studentResult?->id ?: $parentFeedback?->student_result_id,
            'term_id' => $studentResult?->term_id ?: $parentFeedback?->term_id ?: ($validated['term_id'] ?? null),
            'exam_type_id' => $studentResult?->exam_type_id ?: $parentFeedback?->exam_type_id ?: ($validated['exam_type_id'] ?? null),
            'subject_id' => $validated['subject_id'] ?? $parentFeedback?->subject_id,
            'feedback_type' => $parentFeedback?->feedback_type ?: $validated['feedback_type'],
            'title' => trim((string) $validated['title']),
            'message' => trim((string) $validated['message']),
            'status' => 'open',
        ]);

        return redirect()
            ->to(route('portal.results.feedback.index'))
            ->with('result_feedback_success', 'Submitted successfully. The academic team will review it.');
    }

    public function markResultFeedbackResponseRead(StudentResultFeedback $feedback)
    {
        $user = $this->portalUser();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Student profile is not available.');
        }

        abort_unless((int) $feedback->school_id === (int) $student->school_id, 404);
        abort_unless((int) $feedback->student_id === (int) $student->id, 404);

        if ($feedback->admin_response && $feedback->responded_at && is_null($feedback->student_read_at)) {
            $feedback->update([
                'student_read_at' => now(),
            ]);
        }

        return redirect()
            ->to(route('portal.results.feedback.index'))
            ->with('success', 'Response opened.');
    }

    public function submitTestimonial(Request $request)
    {
        $user = $this->portalUser();
        if (($user?->role?->value ?? null) !== UserRole::STUDENT->value) {
            abort(403, 'Only students can submit testimonials here.');
        }

        $student = $user->student;
        $school = $user->school;

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $publicPage = PublicPageContent::forSchool($school);
        $testimonialSuccessText = trim((string) ($publicPage['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.'));
        $testimonialErrorText = trim((string) ($publicPage['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.'));

        if (trim((string) $request->input('website', '')) !== '') {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
        }

        $validated = $request->validate([
            'role_title' => ['nullable', 'string', 'max:140'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'message' => ['required', 'string', 'min:20', 'max:1200'],
            'started_at' => ['required', 'integer'],
            'website' => ['nullable', 'max:0'],
        ]);

        $startedAt = (int) $validated['started_at'];
        if ($startedAt <= 0 || (now()->timestamp - $startedAt) < 3) {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        $sanitize = static function (?string $value): string {
            return trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');
        };

        $roleTitle = $sanitize($validated['role_title'] ?? '');
        $message = $sanitize($validated['message']);

        if ($message === '') {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        if (preg_match('/https?:\/\/|www\./i', $message)) {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => 'Links are not allowed in testimonials.'])
                ->withInput();
        }

        Testimonial::query()->create([
            'school_id'  => $school->id,
            'student_id' => $student->id,
            'full_name'  => $student->full_name,
            'role_title' => $roleTitle !== '' ? $roleTitle : 'Student',
            'rating'     => (int) $validated['rating'],
            'message'    => $message,
            'status'     => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        return redirect()
            ->to(route('student.dashboard') . '#student-testimonial-form')
            ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
    }
}
