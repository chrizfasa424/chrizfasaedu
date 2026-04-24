<?php

namespace App\Http\Controllers\Examination;

use App\Exports\ResultImportErrorsExport;
use App\Exports\ResultSheetTemplateExport;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\PreviewResultImportRequest;
use App\Http\Requests\Examination\ResultSheetFilterRequest;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ExamType;
use App\Models\ResultBatch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentResult;
use App\Services\GradingService;
use App\Services\ResultImportService;
use App\Services\ResultTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ResultSheetController extends Controller
{
    public function __construct(
        private readonly ResultImportService $importService,
        private readonly ResultTemplateService $templateService,
        private readonly GradingService $gradingService
    ) {
    }

    public function importForm(?string $assessmentType = null)
    {
        $assessmentType = $this->normalizeAssessmentType($assessmentType);

        return view('examination.result-sheets.import', array_merge(
            $this->pageContext(),
            ['selectedAssessmentType' => $assessmentType]
        ));
    }

    public function previewImport(PreviewResultImportRequest $request)
    {
        $validated = $request->validated();
        $validated['school_id'] = (int) $request->user()->school_id;
        $validated['section'] = $validated['section'] ?? $this->resolveSection((int) $validated['class_id']);
        $validated['assessment_type'] = $this->normalizeAssessmentType((string) $request->input('assessment_type', 'full_result'));

        $batch = $this->importService->previewImport(
            $validated,
            $request->file('file'),
            (int) $request->user()->id
        );

        return view('examination.result-sheets.preview', array_merge(
            $this->pageContext(),
            ['batch' => $batch]
        ));
    }

    public function commitImport(Request $request, ResultBatch $batch)
    {
        $this->authorizeResultImportAction($request);

        try {
            $this->importService->commitImport($batch, (int) $request->user()->id);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('examination.result-sheets.preview.show', $batch)
                ->withErrors($exception->errors());
        }

        return redirect()
            ->route('examination.result-sheets.history')
            ->with('success', 'Result import completed successfully.');
    }

    public function showPreview(ResultBatch $batch)
    {
        return view('examination.result-sheets.preview', array_merge(
            $this->pageContext(),
            ['batch' => $batch->load(['errors', 'schoolClass', 'arm', 'session', 'term', 'examType'])]
        ));
    }

    public function history(ResultSheetFilterRequest $request)
    {
        $filters = $request->validated();
        $assessmentType = $this->normalizeAssessmentType((string) $request->input('assessment_type', ''));
        if ($assessmentType !== 'full_result' || $request->filled('assessment_type')) {
            $filters['assessment_type'] = $assessmentType;
        }

        $batches = ResultBatch::query()
            ->with(['schoolClass', 'arm', 'session', 'term', 'examType', 'importer'])
            ->when(!empty($filters['assessment_type']), fn ($q) => $q->where('assessment_type', (string) $filters['assessment_type']))
            ->when(!empty($filters['class_id']), fn ($q) => $q->where('class_id', (int) $filters['class_id']))
            ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
            ->when(!empty($filters['session_id']), fn ($q) => $q->where('session_id', (int) $filters['session_id']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('examination.result-sheets.history', array_merge(
            $this->pageContext(),
            [
                'batches' => $batches,
                'filters' => $filters,
            ]
        ));
    }

    public function publishing(ResultSheetFilterRequest $request)
    {
        $filters = $request->validated();
        $assessmentType = $this->normalizeAssessmentType((string) $request->input('assessment_type', 'full_result'));
        $scopeQuery = $this->scopedStudentResultsQuery($request, $filters);
        $rows = $scopeQuery->get();

        $publishedCount = match ($assessmentType) {
            'first_test' => $rows->whereNotNull('first_test_published_at')->count(),
            'second_test' => $rows->whereNotNull('second_test_published_at')->count(),
            'exam' => $rows->whereNotNull('exam_published_at')->count(),
            default => $rows->whereNotNull('full_result_published_at')->count(),
        };

        return view('examination.result-sheets.publishing', array_merge(
            $this->pageContext(),
            [
                'filters' => array_merge($filters, ['assessment_type' => $assessmentType]),
                'scopedCount' => $rows->count(),
                'publishedCount' => $publishedCount,
            ]
        ));
    }

    public function publish(Request $request)
    {
        $this->authorizeResultImportAction($request);
        $validated = $this->validatePublicationRequest($request);
        $assessmentType = $this->normalizeAssessmentType((string) $validated['assessment_type']);

        $updated = DB::transaction(function () use ($request, $validated, $assessmentType) {
            $scope = $this->scopedStudentResultsQuery($request, $validated);
            $count = $scope->count();
            if ($count === 0) {
                return 0;
            }

            $scope->update($this->publicationDataFor($assessmentType, true, (int) $request->user()->id));
            return $count;
        });

        if ($updated === 0) {
            return back()->withErrors(['publish' => 'No result records found in the selected scope.']);
        }

        $label = $this->assessmentTypes()[$assessmentType] ?? 'Selected Result';
        return back()->with('success', "{$label} published for {$updated} student(s).");
    }

    public function unpublish(Request $request)
    {
        $this->authorizeResultImportAction($request);
        $validated = $this->validatePublicationRequest($request);
        $assessmentType = $this->normalizeAssessmentType((string) $validated['assessment_type']);

        $updated = DB::transaction(function () use ($request, $validated, $assessmentType) {
            $scope = $this->scopedStudentResultsQuery($request, $validated);
            $count = $scope->count();
            if ($count === 0) {
                return 0;
            }

            $scope->update($this->publicationDataFor($assessmentType, false, (int) $request->user()->id));
            return $count;
        });

        if ($updated === 0) {
            return back()->withErrors(['unpublish' => 'No result records found in the selected scope.']);
        }

        $label = $this->assessmentTypes()[$assessmentType] ?? 'Selected Result';
        return back()->with('success', "{$label} unpublished for {$updated} student(s).");
    }

    public function classSheet(ResultSheetFilterRequest $request)
    {
        $filters = $request->validated();
        $studentsForFilter = collect();
        if (!empty($filters['class_id'])) {
            $studentsForFilter = Student::query()
                ->where('class_id', (int) $filters['class_id'])
                ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();
        }

        $query = StudentResult::query()
            ->with([
                'student',
                'schoolClass',
                'arm',
                'session',
                'term',
                'examType',
                'promotedToClass',
                'items.subject',
            ])
            ->when(!empty($filters['section']), fn ($q) => $q->where('section', $filters['section']))
            ->when(!empty($filters['class_id']), fn ($q) => $q->where('class_id', (int) $filters['class_id']))
            ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
            ->when(!empty($filters['session_id']), fn ($q) => $q->where('session_id', (int) $filters['session_id']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']))
            ->when(!empty($filters['student_id']), fn ($q) => $q->where('student_id', (int) $filters['student_id']));

        $results = $query
            ->orderBy('class_position')
            ->paginate(25)
            ->withQueryString();

        return view('examination.result-sheets.class-sheet', array_merge(
            $this->pageContext(),
            [
                'results' => $results,
                'filters' => $filters,
                'studentsForFilter' => $studentsForFilter,
            ]
        ));
    }

    public function studentSheet(StudentResult $studentResult)
    {
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

        return view('examination.result-sheets.student-print', [
            'sheet' => $studentResult,
            'school' => auth()->user()->school,
            'interpretation' => $interpretation,
            'asPdf' => false,
            'respectVisibility' => false,
        ]);
    }

    public function studentSheetPdf(StudentResult $studentResult)
    {
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
            'school' => auth()->user()->school,
            'interpretation' => $interpretation,
            'asPdf' => true,
            'respectVisibility' => false,
        ]);

        $identity = (string) ($studentResult->student?->admission_number ?: $studentResult->student_id);
        $identity = preg_replace('/[\\\\\\/]+/', '-', $identity);
        $identity = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $identity);
        $identity = trim((string) $identity, '-_.');
        if ($identity === '') {
            $identity = (string) $studentResult->student_id;
        }

        $filename = 'result-sheet-' . $identity . '.pdf';
        return $pdf->download($filename);
    }

    public function bulkPrint(ResultSheetFilterRequest $request)
    {
        $filters = $request->validated();
        $query = StudentResult::query()
            ->with([
                'student',
                'schoolClass',
                'arm',
                'session',
                'term',
                'examType',
                'promotedToClass',
                'items.subject',
            ])
            ->when(!empty($filters['section']), fn ($q) => $q->where('section', $filters['section']))
            ->when(!empty($filters['class_id']), fn ($q) => $q->where('class_id', (int) $filters['class_id']))
            ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
            ->when(!empty($filters['session_id']), fn ($q) => $q->where('session_id', (int) $filters['session_id']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']))
            ->orderBy('class_position');

        $sheets = $query->get();
        if ($sheets->isEmpty()) {
            return redirect()->back()->withErrors([
                'bulk_print' => 'No result sheets found for the selected filters.',
            ]);
        }

        $interpretation = $this->gradingService->interpretationForSchool(
            (int) auth()->user()->school_id,
            !empty($filters['class_id']) ? (int) $filters['class_id'] : null,
            (string) ($filters['section'] ?? '')
        );

        $pdf = Pdf::loadView('examination.result-sheets.bulk-print', [
            'sheets' => $sheets,
            'school' => auth()->user()->school,
            'interpretation' => $interpretation,
        ]);

        return $pdf->download('class-result-sheets.pdf');
    }

    public function downloadTemplate(ResultSheetFilterRequest $request)
    {
        $validated = $request->validated();
        if (empty($validated['class_id'])) {
            return redirect()->back()->withErrors([
                'class_id' => 'Select a class before downloading template.',
            ]);
        }
        $assessmentType = $this->normalizeAssessmentType((string) $request->input('assessment_type', 'full_result'));

        $rows = $this->templateService->buildWideTemplateRows(
            (int) $request->user()->school_id,
            (int) $validated['class_id'],
            !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null,
            $assessmentType
        );

        $class = SchoolClass::query()->find((int) $validated['class_id']);
        $label = $class?->grade_level?->label() ?? $class?->name ?? 'class';
        $typeLabel = strtolower(str_replace(' ', '-', (string) ($this->assessmentTypes()[$assessmentType] ?? $assessmentType)));
        $filename = 'result-template-' . preg_replace('/[^A-Za-z0-9\-_.]/', '-', $label) . '-' . $typeLabel . '.xlsx';

        return Excel::download(new ResultSheetTemplateExport($rows), $filename);
    }

    public function downloadErrors(ResultBatch $batch)
    {
        if ($batch->errors()->count() === 0) {
            return redirect()->back()->withErrors([
                'errors' => 'This batch has no validation errors to download.',
            ]);
        }

        $filename = 'result-import-errors-batch-' . $batch->id . '.xlsx';
        return Excel::download(new ResultImportErrorsExport($batch), $filename);
    }

    protected function pageContext(): array
    {
        $schoolId = (int) auth()->user()->school_id;
        $this->ensureDefaultExamTypes($schoolId);

        $classes = SchoolClass::query()
            ->with('arms')
            ->orderBy('order')
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
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sections = $classes
            ->pluck('section')
            ->filter()
            ->unique()
            ->values();

        $assessmentTypes = $this->assessmentTypes();

        return compact('classes', 'sessions', 'terms', 'examTypes', 'sections', 'assessmentTypes');
    }

    protected function ensureDefaultExamTypes(int $schoolId): void
    {
        if (ExamType::query()->where('school_id', $schoolId)->exists()) {
            return;
        }

        $defaults = [
            ['name' => 'First Terminal Examination', 'slug' => 'first-terminal'],
            ['name' => 'Second Terminal Examination', 'slug' => 'second-terminal'],
            ['name' => 'Third Terminal Examination', 'slug' => 'third-terminal'],
        ];

        foreach ($defaults as $entry) {
            ExamType::query()->firstOrCreate(
                ['school_id' => $schoolId, 'slug' => $entry['slug']],
                ['name' => $entry['name'], 'is_active' => true]
            );
        }
    }

    protected function resolveSection(int $classId): string
    {
        $class = SchoolClass::query()->find($classId);
        return (string) ($class?->section ?: ($class?->grade_level?->section() ?? ''));
    }

    protected function authorizeResultImportAction(Request $request): void
    {
        $user = $request->user();
        $allowed = $user && in_array((string) $user->role?->value, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ], true);

        abort_unless($allowed, 403, 'Unauthorized.');
    }

    protected function assessmentTypes(): array
    {
        return [
            'first_test' => 'First Test',
            'second_test' => 'Second Test',
            'exam' => 'Exam',
            'full_result' => 'Full Terminal Result',
        ];
    }

    protected function normalizeAssessmentType(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        return array_key_exists($value, $this->assessmentTypes()) ? $value : 'full_result';
    }

    protected function validatePublicationRequest(Request $request): array
    {
        return $request->validate([
            'section' => ['nullable', 'string', 'max:60'],
            'class_id' => ['required', 'exists:classes,id'],
            'arm_id' => ['nullable', 'exists:class_arms,id'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'term_id' => ['required', 'exists:academic_terms,id'],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'assessment_type' => ['required', 'in:first_test,second_test,exam,full_result'],
        ]);
    }

    protected function scopedStudentResultsQuery(Request $request, array $filters)
    {
        $schoolId = (int) $request->user()->school_id;

        return StudentResult::query()
            ->where('school_id', $schoolId)
            ->when(!empty($filters['section']), fn ($q) => $q->where('section', (string) $filters['section']))
            ->when(!empty($filters['class_id']), fn ($q) => $q->where('class_id', (int) $filters['class_id']))
            ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
            ->when(!empty($filters['session_id']), fn ($q) => $q->where('session_id', (int) $filters['session_id']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']));
    }

    protected function publicationDataFor(string $assessmentType, bool $publish, int $userId): array
    {
        $now = now();

        if ($assessmentType === 'full_result') {
            return [
                'is_published' => $publish,
                'published_at' => $publish ? $now : null,
                'full_result_published_at' => $publish ? $now : null,
                'full_result_published_by' => $publish ? $userId : null,
                'updated_by' => $userId,
            ];
        }

        $columnMap = [
            'first_test' => ['first_test_published_at', 'first_test_published_by'],
            'second_test' => ['second_test_published_at', 'second_test_published_by'],
            'exam' => ['exam_published_at', 'exam_published_by'],
        ];

        [$publishedAtColumn, $publishedByColumn] = $columnMap[$assessmentType];

        return [
            $publishedAtColumn => $publish ? $now : null,
            $publishedByColumn => $publish ? $userId : null,
            'updated_by' => $userId,
        ];
    }
}

