<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Notifications\AttendanceWarningNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    private const ABSENCE_WARNING_THRESHOLD = 10;

    // ── Admin: Record attendance ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $classId = $request->get('class_id');
        $date    = $request->get('date', now()->toDateString());
        $classes = SchoolClass::orderBy('order')->get();

        $students   = collect();
        $attendances = collect();
        $stats      = null;
        $trendData  = null;
        $warnings   = collect();

        if ($classId) {
            $students = Student::with(['arm'])
                ->active()
                ->inClass($classId)
                ->orderBy('first_name')
                ->get();

            $attendances = StudentAttendance::where('class_id', $classId)
                ->where('date', $date)
                ->pluck('status', 'student_id');

            // Summary stats for today
            $stats = [
                'total'   => $students->count(),
                'present' => $attendances->filter(fn($s) => $s === 'present')->count(),
                'absent'  => $attendances->filter(fn($s) => $s === 'absent')->count(),
                'late'    => $attendances->filter(fn($s) => $s === 'late')->count(),
                'excused' => $attendances->filter(fn($s) => $s === 'excused')->count(),
                'male'    => $students->where('gender', 'male')->count(),
                'female'  => $students->where('gender', 'female')->count(),
            ];

            // 30-day trend for the class
            $school  = auth()->user()->school;
            $session = $school->currentSession();
            $term    = $session?->terms()->where('is_current', true)->first();

            $trendRaw = StudentAttendance::where('class_id', $classId)
                ->when($term, fn($q) => $q->where('term_id', $term->id))
                ->where('date', '>=', now()->subDays(29)->toDateString())
                ->select('date', 'status', DB::raw('count(*) as count'))
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get()
                ->groupBy(fn($r) => Carbon::parse($r->date)->format('d M'));

            $trendLabels  = [];
            $trendPresent = [];
            $trendAbsent  = [];

            for ($i = 29; $i >= 0; $i--) {
                $label          = now()->subDays($i)->format('d M');
                $trendLabels[]  = $label;
                $dayData        = $trendRaw->get($label, collect());
                $trendPresent[] = $dayData->where('status', 'present')->sum('count')
                                + $dayData->where('status', 'late')->sum('count');
                $trendAbsent[]  = $dayData->where('status', 'absent')->sum('count');
            }

            $trendData = compact('trendLabels', 'trendPresent', 'trendAbsent');

            // Students with >= threshold absences this term
            if ($term) {
                $absentCounts = StudentAttendance::where('class_id', $classId)
                    ->where('term_id', $term->id)
                    ->where('status', 'absent')
                    ->select('student_id', DB::raw('count(*) as absent_count'))
                    ->groupBy('student_id')
                    ->having('absent_count', '>=', self::ABSENCE_WARNING_THRESHOLD)
                    ->pluck('absent_count', 'student_id');

                $warnings = $students->filter(fn($s) => $absentCounts->has($s->id))
                    ->map(fn($s) => ['student' => $s, 'count' => $absentCounts[$s->id]]);
            }
        }

        return view('academic.attendance.index', compact(
            'classes', 'students', 'attendances',
            'classId', 'date', 'stats', 'trendData', 'warnings'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'                 => 'required|exists:classes,id',
            'date'                     => 'required|date',
            'attendance'               => 'required|array',
            'attendance.*.student_id'  => 'required|exists:students,id',
            'attendance.*.status'      => 'required|in:present,absent,late,excused',
        ]);

        $school  = auth()->user()->school;
        $session = $school->currentSession();
        $term    = $session?->terms()->where('is_current', true)->first();

        foreach ($validated['attendance'] as $record) {
            StudentAttendance::updateOrCreate(
                ['student_id' => $record['student_id'], 'date' => $validated['date']],
                [
                    'school_id'   => $school->id,
                    'class_id'    => $validated['class_id'],
                    'session_id'  => $session?->id,
                    'term_id'     => $term?->id,
                    'status'      => $record['status'],
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        // Check for absence warnings
        if ($term) {
            $this->checkAndSendWarnings($validated['class_id'], $term->id, $school->name);
        }

        return back()->with('success', 'Attendance saved for ' . Carbon::parse($validated['date'])->format('d M Y') . '.');
    }

    // ── Attendance history / analytics ────────────────────────────────────────

    public function history(Request $request)
    {
        $classId = $request->get('class_id');
        $classes = SchoolClass::orderBy('order')->get();
        $school  = auth()->user()->school;
        $session = $school->currentSession();
        $term    = $session?->terms()->where('is_current', true)->first();

        $studentStats = collect();
        $classChart   = null;

        if ($classId && $term) {
            $students = Student::active()->inClass($classId)->orderBy('first_name')->get();

            $allAttendance = StudentAttendance::where('class_id', $classId)
                ->where('term_id', $term->id)
                ->select('student_id', 'status', DB::raw('count(*) as count'))
                ->groupBy('student_id', 'status')
                ->get()
                ->groupBy('student_id');

            $studentStats = $students->map(function ($student) use ($allAttendance) {
                $data    = $allAttendance->get($student->id, collect());
                $present = $data->where('status', 'present')->sum('count')
                         + $data->where('status', 'late')->sum('count');
                $absent  = $data->where('status', 'absent')->sum('count');
                $excused = $data->where('status', 'excused')->sum('count');
                $total   = $present + $absent + $excused;
                return [
                    'student' => $student,
                    'present' => $present,
                    'absent'  => $absent,
                    'excused' => $excused,
                    'total'   => $total,
                    'rate'    => $total > 0 ? round(($present / $total) * 100) : 0,
                    'warning' => $absent >= self::ABSENCE_WARNING_THRESHOLD,
                ];
            });

            // Overall class chart data (donut)
            $totals = $allAttendance->flatten(1)->groupBy('status');
            $classChart = [
                'present' => $totals->get('present', collect())->sum('count'),
                'absent'  => $totals->get('absent',  collect())->sum('count'),
                'late'    => $totals->get('late',     collect())->sum('count'),
                'excused' => $totals->get('excused',  collect())->sum('count'),
            ];
        }

        return view('academic.attendance.history', compact(
            'classes', 'classId', 'studentStats', 'classChart', 'term'
        ));
    }

    // ── Portal: student views own attendance ──────────────────────────────────

    public function portalView(Request $request)
    {
        $user    = auth()->user();
        $student = $user->student;
        if (!$student) abort(403);

        $school  = $user->school;
        $session = $school->currentSession();
        $term    = $session?->terms()->where('is_current', true)->first();

        $records = StudentAttendance::where('student_id', $student->id)
            ->when($term, fn($q) => $q->where('term_id', $term->id))
            ->orderByDesc('date')
            ->get();

        $stats = [
            'total'   => $records->count(),
            'present' => $records->whereIn('status', ['present', 'late'])->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'excused' => $records->where('status', 'excused')->count(),
            'late'    => $records->where('status', 'late')->count(),
        ];
        $stats['rate'] = $stats['total'] > 0
            ? round(($stats['present'] / $stats['total']) * 100)
            : 0;

        // Weekly trend (last 8 weeks)
        $weeklyLabels  = [];
        $weeklyPresent = [];
        $weeklyAbsent  = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end   = (clone $start)->endOfWeek();
            $weeklyLabels[]  = 'Wk ' . $start->format('d M');
            $week = $records->filter(fn($r) =>
                $r->date->between($start, $end)
            );
            $weeklyPresent[] = $week->whereIn('status', ['present', 'late'])->count();
            $weeklyAbsent[]  = $week->where('status', 'absent')->count();
        }

        $trendData = compact('weeklyLabels', 'weeklyPresent', 'weeklyAbsent');

        return view('portal.student.attendance', compact(
            'student', 'records', 'stats', 'trendData', 'term'
        ));
    }

    // ── Warning helper ────────────────────────────────────────────────────────

    private function checkAndSendWarnings(int $classId, int $termId, string $schoolName): void
    {
        $absentees = StudentAttendance::where('class_id', $classId)
            ->where('term_id', $termId)
            ->where('status', 'absent')
            ->select('student_id', DB::raw('count(*) as absent_count'))
            ->groupBy('student_id')
            ->having('absent_count', self::ABSENCE_WARNING_THRESHOLD)  // exactly at threshold
            ->get();

        foreach ($absentees as $row) {
            $student = Student::with(['user', 'parents.user'])->find($row->student_id);
            if (!$student) continue;

            $notification = new AttendanceWarningNotification($student, $row->absent_count, $schoolName);

            // Notify student's user account
            if ($student->user) {
                try { $student->user->notify($notification); } catch (\Throwable) {}
            }

            // Notify parents
            foreach ($student->parents as $parent) {
                if ($parent->user) {
                    try { $parent->user->notify($notification); } catch (\Throwable) {}
                }
            }
        }
    }
}
