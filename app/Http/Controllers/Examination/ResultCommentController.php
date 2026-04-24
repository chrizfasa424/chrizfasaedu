<?php

namespace App\Http\Controllers\Examination;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\ResultSheetFilterRequest;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ExamType;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentResult;
use App\Services\TeacherCommentAiService;
use Illuminate\Http\Request;

class ResultCommentController extends Controller
{
    public function __construct(
        private readonly TeacherCommentAiService $teacherCommentAiService
    ) {
    }

    public function index(ResultSheetFilterRequest $request)
    {
        $user = $request->user();
        $schoolId = (int) $user->school_id;
        $filters = $request->validated();

        $studentsForFilter = collect();
        if (!empty($filters['class_id'])) {
            $studentsForFilter = Student::query()
                ->where('school_id', $schoolId)
                ->where('class_id', (int) $filters['class_id'])
                ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();
        }

        $results = StudentResult::query()
            ->with([
                'student:id,first_name,last_name,admission_number,registration_number',
                'schoolClass:id,name,grade_level',
                'arm:id,name',
                'session:id,name',
                'term:id,name',
                'examType:id,name',
            ])
            ->where('school_id', $schoolId)
            ->when(!empty($filters['section']), fn ($q) => $q->where('section', (string) $filters['section']))
            ->when(!empty($filters['class_id']), fn ($q) => $q->where('class_id', (int) $filters['class_id']))
            ->when(!empty($filters['arm_id']), fn ($q) => $q->where('arm_id', (int) $filters['arm_id']))
            ->when(!empty($filters['session_id']), fn ($q) => $q->where('session_id', (int) $filters['session_id']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']))
            ->when(!empty($filters['student_id']), fn ($q) => $q->where('student_id', (int) $filters['student_id']))
            ->orderBy('class_position')
            ->paginate(20)
            ->withQueryString();

        $hasAnyResultRecords = StudentResult::query()
            ->where('school_id', $schoolId)
            ->exists();

        $editableRoles = array_values(array_filter([
            $this->canEditTeacherRemark($user) ? 'Teacher Comment' : null,
            $this->canEditPrincipalRemark($user) ? 'Principal Comment' : null,
            $this->canEditVicePrincipalRemark($user) ? 'Vice Principal Comment' : null,
        ]));

        return view('examination.result-comments.index', array_merge(
            $this->pageContext($schoolId),
            [
                'results' => $results,
                'filters' => $filters,
                'studentsForFilter' => $studentsForFilter,
                'hasAnyResultRecords' => $hasAnyResultRecords,
                'editableRoles' => $editableRoles,
                'canEditTeacherRemark' => $this->canEditTeacherRemark($user),
                'canEditPrincipalRemark' => $this->canEditPrincipalRemark($user),
                'canEditVicePrincipalRemark' => $this->canEditVicePrincipalRemark($user),
                'canManageVisibility' => $this->canManageVisibility($user),
            ]
        ));
    }

    public function update(Request $request, StudentResult $studentResult)
    {
        $user = $request->user();
        $this->assertSameSchool($studentResult, (int) $user->school_id);

        $canEditTeacherRemark = $this->canEditTeacherRemark($user);
        $canEditPrincipalRemark = $this->canEditPrincipalRemark($user);
        $canEditVicePrincipalRemark = $this->canEditVicePrincipalRemark($user);

        if (!$canEditTeacherRemark && !$canEditPrincipalRemark && !$canEditVicePrincipalRemark) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'class_teacher_remark' => ['nullable', 'string', 'max:2500'],
            'principal_remark' => ['nullable', 'string', 'max:2500'],
            'vice_principal_remark' => ['nullable', 'string', 'max:2500'],
        ]);

        $payload = [];
        if ($canEditTeacherRemark && $request->has('class_teacher_remark')) {
            $payload['class_teacher_remark'] = $this->cleanRemark($validated['class_teacher_remark'] ?? null);
        }

        if ($canEditPrincipalRemark && $request->has('principal_remark')) {
            $payload['principal_remark'] = $this->cleanRemark($validated['principal_remark'] ?? null);
        }

        if ($canEditVicePrincipalRemark && $request->has('vice_principal_remark')) {
            $payload['vice_principal_remark'] = $this->cleanRemark($validated['vice_principal_remark'] ?? null);
        }

        if (empty($payload)) {
            return back()->withErrors([
                'comments' => 'No permitted comment field was submitted.',
            ]);
        }

        $payload['updated_by'] = (int) $user->id;
        $studentResult->update($payload);

        return back()->with('success', 'Result comment updated successfully.');
    }

    public function generateTeacherComment(Request $request, StudentResult $studentResult)
    {
        $user = $request->user();
        $this->assertSameSchool($studentResult, (int) $user->school_id);
        abort_unless($this->canEditTeacherRemark($user), 403, 'Unauthorized.');

        try {
            $remark = $this->teacherCommentAiService->generateForStudentResult($studentResult);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'ai_teacher_comment' => $exception->getMessage(),
            ]);
        }

        $studentResult->update([
            'class_teacher_remark' => $remark,
            'class_teacher_remark_active' => true,
            'updated_by' => (int) $user->id,
        ]);

        return back()->with('success', 'Teacher comment generated with OpenAI for this student.');
    }

    public function generateTeacherComments(Request $request)
    {
        $user = $request->user();
        abort_unless($this->canEditTeacherRemark($user), 403, 'Unauthorized.');

        $validated = $request->validate([
            'result_ids' => ['required', 'array', 'min:1', 'max:30'],
            'result_ids.*' => ['required', 'integer', 'exists:student_results,id'],
        ]);

        $resultIds = collect((array) $validated['result_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $results = StudentResult::query()
            ->where('school_id', (int) $user->school_id)
            ->whereIn('id', $resultIds->all())
            ->with(['student', 'items.subject', 'schoolClass', 'arm', 'session', 'term', 'examType'])
            ->get();

        if ($results->isEmpty()) {
            return back()->withErrors([
                'ai_teacher_comment' => 'No valid student result records were found for AI comment generation.',
            ]);
        }

        $generated = 0;
        $failedStudents = [];

        foreach ($results as $result) {
            try {
                $remark = $this->teacherCommentAiService->generateForStudentResult($result);
                $result->update([
                    'class_teacher_remark' => $remark,
                    'class_teacher_remark_active' => true,
                    'updated_by' => (int) $user->id,
                ]);
                $generated++;
            } catch (\Throwable $exception) {
                $failedStudents[] = (string) ($result->student?->full_name ?: ('Student #' . $result->id));
            }
        }

        if ($generated === 0) {
            return back()->withErrors([
                'ai_teacher_comment' => 'OpenAI could not generate teacher comments for the selected students.',
            ]);
        }

        $message = "OpenAI teacher comments generated for {$generated} student(s).";
        if (!empty($failedStudents)) {
            $sample = implode(', ', array_slice($failedStudents, 0, 3));
            $remaining = count($failedStudents) - min(3, count($failedStudents));
            $message .= $remaining > 0
                ? " Failed for {$sample} and {$remaining} more."
                : " Failed for {$sample}.";
        }

        return back()->with('success', $message);
    }

    public function updateVisibility(Request $request, StudentResult $studentResult)
    {
        $user = $request->user();
        $this->assertSameSchool($studentResult, (int) $user->school_id);
        abort_unless($this->canManageVisibility($user), 403, 'Unauthorized.');

        $validated = $request->validate([
            'class_teacher_remark_active' => ['required', 'boolean'],
            'principal_remark_active' => ['required', 'boolean'],
            'vice_principal_remark_active' => ['required', 'boolean'],
        ]);

        $studentResult->update([
            'class_teacher_remark_active' => (bool) $validated['class_teacher_remark_active'],
            'principal_remark_active' => (bool) $validated['principal_remark_active'],
            'vice_principal_remark_active' => (bool) $validated['vice_principal_remark_active'],
            'updated_by' => (int) $user->id,
        ]);

        return back()->with('success', 'Comment visibility updated.');
    }

    protected function pageContext(int $schoolId): array
    {
        $classes = SchoolClass::query()
            ->where('school_id', $schoolId)
            ->with('arms')
            ->orderBy('order')
            ->get();

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $terms = AcademicTerm::query()
            ->where('school_id', $schoolId)
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

        return compact('classes', 'sessions', 'terms', 'examTypes', 'sections');
    }

    protected function canEditTeacherRemark($user): bool
    {
        return in_array((string) $user->role?->value, [
            UserRole::TEACHER->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::SUPER_ADMIN->value,
        ], true);
    }

    protected function canEditPrincipalRemark($user): bool
    {
        return in_array((string) $user->role?->value, [
            UserRole::PRINCIPAL->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::SUPER_ADMIN->value,
        ], true);
    }

    protected function canEditVicePrincipalRemark($user): bool
    {
        return in_array((string) $user->role?->value, [
            UserRole::VICE_PRINCIPAL->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::SUPER_ADMIN->value,
        ], true);
    }

    protected function canManageVisibility($user): bool
    {
        return in_array((string) $user->role?->value, [
            UserRole::SCHOOL_ADMIN->value,
            UserRole::SUPER_ADMIN->value,
        ], true);
    }

    protected function cleanRemark(?string $value): ?string
    {
        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    protected function assertSameSchool(StudentResult $studentResult, int $schoolId): void
    {
        abort_unless((int) $studentResult->school_id === $schoolId, 404);
    }
}
