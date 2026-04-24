<?php

namespace App\Http\Controllers\Examination;

use App\Exports\ResultsExport;
use App\Http\Controllers\Controller;
use App\Imports\ResultsImport;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ReportCard;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ResultController extends Controller
{
    // ── Shared context ────────────────────────────────────────────────────────

    private function pageContext(): array
    {
        $classes  = SchoolClass::orderBy('order')->get();
        $sessions = AcademicSession::orderByDesc('is_current')->orderByDesc('start_date')->get();
        $terms    = AcademicTerm::with('session')->orderByDesc('is_current')->get();
        return compact('classes', 'sessions', 'terms');
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $ctx     = $this->pageContext();
        $results = collect();

        if ($request->filled('class_id') && $request->filled('term_id')) {
            $results = Result::with(['student', 'subject'])
                ->where('class_id', $request->class_id)
                ->where('term_id', $request->term_id)
                ->get()
                ->groupBy('student_id');
        }

        return view('examination.results.index', array_merge($ctx, compact('results')));
    }

    // ── Enter scores manually ────────────────────────────────────────────────

    public function enterScores(Request $request)
    {
        $ctx      = $this->pageContext();
        $subjects = Subject::where('school_id', auth()->user()->school_id)->orderBy('name')->get();

        $students       = collect();
        $existingScores = collect();
        $selectedTerm   = null;

        if ($request->filled('class_id') && $request->filled('term_id') && $request->filled('subject_id')) {
            $students = Student::active()
                ->inClass($request->class_id)
                ->orderBy('first_name')
                ->get();

            $selectedTerm = AcademicTerm::with('session')->find($request->term_id);

            $existingScores = Result::where('class_id', $request->class_id)
                ->where('term_id', $request->term_id)
                ->where('subject_id', $request->subject_id)
                ->get()
                ->keyBy('student_id');
        }

        return view('examination.results.enter-scores',
            array_merge($ctx, compact('subjects', 'students', 'existingScores', 'selectedTerm'))
        );
    }

    public function storeScores(Request $request)
    {
        $validated = $request->validate([
            'class_id'             => 'required|exists:classes,id',
            'subject_id'           => 'required|exists:subjects,id',
            'term_id'              => 'required|exists:academic_terms,id',
            'scores'               => 'required|array',
            'scores.*.student_id'  => 'required|exists:students,id',
            'scores.*.exam'        => 'nullable|numeric|min:0|max:100',
            'scores.*.first_test'  => 'nullable|numeric|min:0|max:100',
            'scores.*.second_test' => 'nullable|numeric|min:0|max:100',
        ]);

        $term    = AcademicTerm::findOrFail($validated['term_id']);
        $session = $term->session;

        foreach ($validated['scores'] as $score) {
            $exam  = (float) ($score['exam']        ?? 0);
            $test1 = (float) ($score['first_test']  ?? 0);
            $test2 = (float) ($score['second_test'] ?? 0);
            $total = $exam + $test1 + $test2;
            $info  = Result::calculateGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $score['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'session_id' => $session->id,
                    'term_id'    => $validated['term_id'],
                ],
                [
                    'school_id'      => auth()->user()->school_id,
                    'class_id'       => $validated['class_id'],
                    'exam_score'     => $exam,
                    'ca1_score'      => $test1,
                    'ca2_score'      => $test2,
                    'ca3_score'      => 0,
                    'total_score'    => $total,
                    'grade'          => $info['grade'],
                    'grade_point'    => $info['point'],
                    'teacher_remark' => $info['remark'],
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully.');
    }

    // ── CSV/Excel Import ──────────────────────────────────────────────────────

    public function importForm(Request $request)
    {
        $ctx = $this->pageContext();
        return view('examination.results.import', $ctx);
    }

    public function import(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'term_id'   => 'required|exists:academic_terms,id',
            'file'      => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $term    = AcademicTerm::findOrFail($request->term_id);
        $session = $term->session;

        $import = new ResultsImport($request->class_id, $session->id, $request->term_id);
        Excel::import($import, $request->file('file'));

        $msg = "Import complete: {$import->saved} records saved, {$import->skipped} skipped.";
        if ($import->errors) {
            $msg .= ' Issues: ' . implode(' | ', array_slice($import->errors, 0, 5));
        }

        return redirect()->route('examination.results.index', [
            'class_id' => $request->class_id,
            'term_id'  => $request->term_id,
        ])->with('success', $msg);
    }

    public function downloadTemplate()
    {
        // Show real admission numbers so the user knows the correct format
        $students = \App\Models\Student::where('school_id', auth()->user()->school_id)
            ->whereNotNull('admission_number')
            ->take(2)
            ->get(['admission_number', 'first_name', 'last_name']);

        $fallback = Student::generateAdmissionNumber((int) auth()->user()->school_id);
        $eg1 = $students->first()?->admission_number ?? $fallback;
        $eg2 = $students->skip(1)->first()?->admission_number ?? $this->incrementAdmissionNumber($fallback);

        $csv = "registration_number,subject,exam,first_test,second_test\n";
        $csv .= "{$eg1},Mathematics,68,6,15\n";
        $csv .= "{$eg1},English Studies,67,10,20\n";
        $csv .= "{$eg2},Mathematics,72,8,14\n";
        $csv .= "{$eg2},English Studies,55,7,12\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="results_import_template.csv"',
        ]);
    }

    // ── Update / Delete individual result ────────────────────────────────────

    public function update(Request $request, \App\Models\Result $result)
    {
        $validated = $request->validate([
            'exam'        => 'required|numeric|min:0|max:100',
            'first_test'  => 'required|numeric|min:0|max:100',
            'second_test' => 'required|numeric|min:0|max:100',
        ]);

        $total = $validated['exam'] + $validated['first_test'] + $validated['second_test'];
        $info  = \App\Models\Result::calculateGrade($total);

        $result->update([
            'exam_score'     => $validated['exam'],
            'ca1_score'      => $validated['first_test'],
            'ca2_score'      => $validated['second_test'],
            'total_score'    => $total,
            'grade'          => $info['grade'],
            'grade_point'    => $info['point'],
            'teacher_remark' => $info['remark'],
            'is_approved'    => false, // reset approval when scores change
        ]);

        return back()->with('success', 'Score updated.');
    }

    public function destroy(\App\Models\Result $result)
    {
        $result->delete();
        return back()->with('success', 'Result deleted.');
    }

    // ── Compute positions & report cards ─────────────────────────────────────

    public function computeResults(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id'  => 'required|exists:academic_terms,id',
        ]);

        $term    = AcademicTerm::findOrFail($request->term_id);
        $session = $term->session;

        // ── Step 1: Position per subject (all subjects that have results) ────────
        // ── Step 2: Build per-student aggregates ─────────────────────────────────
        $students  = Student::where('class_id', $request->class_id)->where('status', 'active')->get();
        $classSize = $students->count();

        $reportData = [];
        foreach ($students as $student) {
            $studentResults = Result::where('student_id', $student->id)
                ->where('term_id', $request->term_id)
                ->where('session_id', $session->id)
                ->get();

            $totalSubjects = $studentResults->count();
            $totalScore    = round($studentResults->sum('total_score'), 2);
            $totalCa1      = round($studentResults->sum('ca1_score'), 2);
            $totalCa2      = round($studentResults->sum('ca2_score'), 2);
            $totalExam     = round($studentResults->sum('exam_score'), 2);
            $average       = $totalSubjects > 0 ? round($totalScore / $totalSubjects, 2) : 0;

            $reportData[] = [
                'student_id'      => $student->id,
                'total_score'     => $totalScore,
                'average_score'   => $average,
                'total_ca1'       => $totalCa1,
                'total_ca2'       => $totalCa2,
                'total_exam'      => $totalExam,
                'total_subjects'  => $totalSubjects,
                'subjects_passed' => $studentResults->where('total_score', '>=', 40)->count(),
                'subjects_failed' => $studentResults->where('total_score', '<', 40)->count(),
            ];
        }

        // ── Step 3: Class average = average of all students' mark averages ────────
        $classAverage = count($reportData) > 0
            ? round(collect($reportData)->avg('average_score'), 2)
            : 0;

        // ── Step 4: Rank by average descending, save report cards ─────────────────
        usort($reportData, fn($a, $b) => $b['average_score'] <=> $a['average_score']);

        foreach ($reportData as $i => $data) {
            ReportCard::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'session_id' => $session->id,
                    'term_id'    => $request->term_id,
                ],
                [
                    'school_id'         => auth()->user()->school_id,
                    'class_id'          => $request->class_id,
                    'total_score'       => $data['total_score'],
                    'average_score'     => $data['average_score'],
                    'class_average'     => $classAverage,
                    'total_ca1'         => $data['total_ca1'],
                    'total_ca2'         => $data['total_ca2'],
                    'total_exam'        => $data['total_exam'],
                    'position_in_class' => $i + 1,
                    'class_size'        => $classSize,
                    'total_subjects'    => $data['total_subjects'],
                    'subjects_passed'   => $data['subjects_passed'],
                    'subjects_failed'   => $data['subjects_failed'],
                ]
            );
        }

        return back()->with('success', "Positions computed for {$classSize} students. Class average: {$classAverage}.");
    }

    // ── Approve ───────────────────────────────────────────────────────────────

    public function approveResults(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id'  => 'required|exists:academic_terms,id',
        ]);

        Result::where('class_id', $request->class_id)
            ->where('term_id', $request->term_id)
            ->where('is_approved', false)
            ->update([
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        // Also publish report cards
        ReportCard::where('class_id', $request->class_id)
            ->where('term_id', $request->term_id)
            ->update(['is_published' => true]);

        return back()->with('success', 'Results approved and published to students.');
    }

    // ── Report Card ───────────────────────────────────────────────────────────

    public function reportCard(Student $student, $termId)
    {
        $reportCard = ReportCard::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->firstOrFail();

        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->get();

        $term   = AcademicTerm::with('session')->find($termId);
        $school = auth()->user()->school;

        return view('examination.results.report-card', compact('student', 'reportCard', 'results', 'school', 'term'));
    }

    public function downloadReportCard(Student $student, $termId)
    {
        $reportCard = ReportCard::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->firstOrFail();

        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->get();

        $term   = AcademicTerm::with('session')->find($termId);
        $school = auth()->user()->school;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pdf.report-card',
            compact('student', 'reportCard', 'results', 'school', 'term')
        );

        return $pdf->download("report-card-{$student->admission_number}-term{$termId}.pdf");
    }

    // ── Excel Export (admin: whole class) ─────────────────────────────────────

    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id'  => 'required|exists:academic_terms,id',
        ]);

        $term  = AcademicTerm::with('session')->findOrFail($request->term_id);
        $class = SchoolClass::findOrFail($request->class_id);
        $label = $class->grade_level?->label() ?? $class->name;
        $filename = "results-{$label}-{$term->name}.xlsx";
        $filename = preg_replace('/[^A-Za-z0-9\-_. ]/', '', $filename);

        return Excel::download(
            new ResultsExport($request->class_id, $request->term_id, auth()->user()->school_id),
            $filename
        );
    }

    // ── Excel Export (student/parent: own results) ────────────────────────────

    public function exportStudentResults(Student $student, $termId)
    {
        // Security: student can only download their own; parent can download their child's
        $user = auth()->user();
        if ($user->hasRole('student') && $user->student?->id !== $student->id) {
            abort(403);
        }

        $term  = AcademicTerm::with('session')->findOrFail($termId);
        $filename = "result-{$student->admission_number}-{$term->name}.xlsx";
        $filename = preg_replace('/[^A-Za-z0-9\-_. ]/', '', $filename);

        return Excel::download(
            new ResultsExport($student->class_id, $termId, $student->school_id, $student->id),
            $filename
        );
    }

    protected function incrementAdmissionNumber(string $admissionNumber): string
    {
        if (preg_match('/^(.*\/)(\d+)$/', $admissionNumber, $matches) !== 1) {
            return $admissionNumber;
        }

        $prefix = $matches[1];
        $number = $matches[2];
        $next = (string) (((int) $number) + 1);

        return $prefix . str_pad($next, strlen($number), '0', STR_PAD_LEFT);
    }
}
