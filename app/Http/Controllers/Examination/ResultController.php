<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Assessment;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('order')->get();
        $results = collect();

        if ($request->filled('class_id') && $request->filled('term_id')) {
            $results = Result::with(['student', 'subject'])
                ->where('class_id', $request->class_id)
                ->where('term_id', $request->term_id)
                ->get()
                ->groupBy('student_id');
        }

        return view('examination.results.index', compact('classes', 'results'));
    }

    public function enterScores(Request $request)
    {
        $classes = SchoolClass::with('subjects')->orderBy('order')->get();
        return view('examination.results.enter-scores', compact('classes'));
    }

    public function storeScores(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.ca1' => 'nullable|numeric|min:0|max:20',
            'scores.*.ca2' => 'nullable|numeric|min:0|max:20',
            'scores.*.ca3' => 'nullable|numeric|min:0|max:20',
            'scores.*.exam' => 'nullable|numeric|min:0|max:60',
        ]);

        $session = auth()->user()->school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        foreach ($validated['scores'] as $score) {
            $total = ($score['ca1'] ?? 0) + ($score['ca2'] ?? 0) + ($score['ca3'] ?? 0) + ($score['exam'] ?? 0);
            $gradeInfo = Result::calculateGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $score['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'session_id' => $session->id,
                    'term_id' => $term->id,
                ],
                [
                    'school_id' => auth()->user()->school_id,
                    'class_id' => $validated['class_id'],
                    'ca1_score' => $score['ca1'] ?? 0,
                    'ca2_score' => $score['ca2'] ?? 0,
                    'ca3_score' => $score['ca3'] ?? 0,
                    'exam_score' => $score['exam'] ?? 0,
                    'total_score' => $total,
                    'grade' => $gradeInfo['grade'],
                    'grade_point' => $gradeInfo['point'],
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully.');
    }

    public function computeResults(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:academic_terms,id',
        ]);

        $session = auth()->user()->school->currentSession();

        // Calculate positions per subject
        $subjects = Subject::whereHas('classes', fn($q) => $q->where('classes.id', $request->class_id))->get();

        foreach ($subjects as $subject) {
            $results = Result::where('class_id', $request->class_id)
                ->where('term_id', $request->term_id)
                ->where('subject_id', $subject->id)
                ->orderByDesc('total_score')
                ->get();

            $position = 1;
            foreach ($results as $result) {
                $result->update(['position_in_subject' => $position++]);
            }
        }

        // Generate report cards
        $students = Student::active()->inClass($request->class_id)->get();
        $classSize = $students->count();

        $reportData = [];
        foreach ($students as $student) {
            $studentResults = Result::where('student_id', $student->id)
                ->where('term_id', $request->term_id)
                ->where('session_id', $session->id)
                ->get();

            $totalScore = $studentResults->sum('total_score');
            $totalSubjects = $studentResults->count();
            $average = $totalSubjects > 0 ? $totalScore / $totalSubjects : 0;

            $reportData[] = [
                'student_id' => $student->id,
                'total_score' => $totalScore,
                'average_score' => round($average, 2),
                'total_subjects' => $totalSubjects,
                'subjects_passed' => $studentResults->where('total_score', '>=', 40)->count(),
                'subjects_failed' => $studentResults->where('total_score', '<', 40)->count(),
            ];
        }

        // Sort by average for positions
        usort($reportData, fn($a, $b) => $b['average_score'] <=> $a['average_score']);

        foreach ($reportData as $i => $data) {
            ReportCard::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'session_id' => $session->id,
                    'term_id' => $request->term_id,
                ],
                [
                    'school_id' => auth()->user()->school_id,
                    'class_id' => $request->class_id,
                    'total_score' => $data['total_score'],
                    'average_score' => $data['average_score'],
                    'position_in_class' => $i + 1,
                    'class_size' => $classSize,
                    'total_subjects' => $data['total_subjects'],
                    'subjects_passed' => $data['subjects_passed'],
                    'subjects_failed' => $data['subjects_failed'],
                ]
            );
        }

        return back()->with('success', "Results computed for {$classSize} students.");
    }

    public function approveResults(Request $request)
    {
        Result::where('class_id', $request->class_id)
            ->where('term_id', $request->term_id)
            ->where('is_approved', false)
            ->update([
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', 'Results approved.');
    }

    public function reportCard(Student $student, $termId)
    {
        $reportCard = ReportCard::where('student_id', $student->id)->where('term_id', $termId)->firstOrFail();
        $results = Result::with('subject')->where('student_id', $student->id)->where('term_id', $termId)->get();
        $school = auth()->user()->school;

        return view('examination.results.report-card', compact('student', 'reportCard', 'results', 'school'));
    }

    public function downloadReportCard(Student $student, $termId)
    {
        $reportCard = ReportCard::where('student_id', $student->id)->where('term_id', $termId)->firstOrFail();
        $results = Result::with('subject')->where('student_id', $student->id)->where('term_id', $termId)->get();
        $school = auth()->user()->school;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-card', compact('student', 'reportCard', 'results', 'school'));
        return $pdf->download("report-card-{$student->admission_number}-{$termId}.pdf");
    }
}
