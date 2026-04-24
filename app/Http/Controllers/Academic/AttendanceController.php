<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Notifications\AttendanceWarningNotification;
use App\Services\AttendanceSheetImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    private const ABSENCE_WARNING_THRESHOLD = 10;

    public function __construct(
        private readonly AttendanceSheetImportService $attendanceImportService
    ) {
    }

    // ── Admin: Record attendance ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $classId = $request->get('class_id');
        $date    = $request->get('date', now()->toDateString());
        $classes = SchoolClass::with('arms')->orderBy('order')->get();
        $sessions = AcademicSession::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();
        $terms = AcademicTerm::query()
            ->with('session')
            ->orderByDesc('is_current')
            ->orderByDesc('id')
            ->get();

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
            'classId', 'date', 'stats', 'trendData', 'warnings',
            'sessions', 'terms'
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

        if ($session && $term) {
            $studentIds = collect($validated['attendance'])
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
            $this->attendanceImportService->syncAttendanceToResultSummaries([
                'school_id' => (int) $school->id,
                'class_id' => (int) $validated['class_id'],
                'arm_id' => null,
                'session_id' => (int) $session->id,
                'term_id' => (int) $term->id,
            ], $studentIds, (int) auth()->id());
        }

        // Check for absence warnings
        if ($term) {
            $this->checkAndSendWarnings($validated['class_id'], $term->id, $school->name);
        }

        return back()->with('success', 'Attendance saved for ' . Carbon::parse($validated['date'])->format('d M Y') . '.');
    }

    public function importSheet(Request $request)
    {
        $validated = $request->validate([
            'import_class_id' => ['required', 'exists:classes,id'],
            'import_arm_id' => ['nullable', 'exists:class_arms,id'],
            'import_session_id' => ['required', 'exists:academic_sessions,id'],
            'import_term_id' => ['required', 'exists:academic_terms,id'],
            'attendance_month' => ['required', 'regex:/^\d{4}\-(0[1-9]|1[0-2])$/'],
            'public_holiday_dates' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $schoolId = (int) auth()->user()->school_id;
        $class = SchoolClass::query()
            ->where('id', (int) $validated['import_class_id'])
            ->where('school_id', $schoolId)
            ->first();

        if (!$class) {
            return back()
                ->withInput()
                ->withErrors(['attendance_import' => 'Attendance import is not valid. Selected class is outside your school scope.']);
        }

        $selectedArmId = !empty($validated['import_arm_id']) ? (int) $validated['import_arm_id'] : null;
        if ($selectedArmId && !$class->arms()->where('id', $selectedArmId)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['attendance_import' => 'Attendance import is not valid. Selected arm does not belong to the selected class.']);
        }

        $term = AcademicTerm::query()->find((int) $validated['import_term_id']);
        if (!$term || (int) $term->session_id !== (int) $validated['import_session_id']) {
            return back()
                ->withInput()
                ->withErrors(['attendance_import' => 'Attendance import is not valid. Selected term does not belong to the selected session.']);
        }

        $month = Carbon::createFromFormat('Y-m', (string) $validated['attendance_month']);

        try {
            $holidayDates = $this->parseHolidayDatesForMonth(
                (string) ($validated['public_holiday_dates'] ?? ''),
                (int) $month->year,
                (int) $month->month
            );
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['attendance_import' => $exception->getMessage()]);
        }

        foreach ($holidayDates as $holidayDate) {
            SchoolHoliday::query()->updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'session_id' => (int) $validated['import_session_id'],
                    'term_id' => (int) $term->id,
                    'holiday_date' => $holidayDate,
                ],
                [
                    'name' => 'Public Holiday',
                    'is_public' => true,
                    'created_by' => auth()->id(),
                ]
            );
        }

        $context = [
            'school_id' => $schoolId,
            'class_id' => (int) $class->id,
            'arm_id' => $selectedArmId,
            'session_id' => (int) $validated['import_session_id'],
            'term_id' => (int) $term->id,
            'year' => (int) $month->year,
            'month' => (int) $month->month,
        ];

        $result = $this->attendanceImportService->importMonthlySheet(
            $request->file('file'),
            $context,
            (int) auth()->id()
        );

        $errorCount = count($result['errors']);
        $excludedMarks = (int) ($result['excluded_marks'] ?? 0);
        $message = "Attendance import completed: {$result['students_matched']} student(s) matched, {$result['records_written']} day record(s) written.";
        if ($excludedMarks > 0) {
            $message .= " {$excludedMarks} mark(s) were ignored on weekends/public holidays.";
        }

        $response = back()
            ->with('attendance_import_summary', [
                'rows_read' => $result['rows_read'],
                'students_matched' => $result['students_matched'],
                'records_written' => $result['records_written'],
                'excluded_marks' => $excludedMarks,
                'error_count' => $errorCount,
                'month_label' => $month->format('F Y'),
            ])
            ->with('attendance_import_errors', array_slice($result['errors'], 0, 200));

        if ($result['records_written'] === 0) {
            return $response->withErrors([
                'attendance_import' => 'Attendance import is not valid. No school-day P/A marks were found in day columns (1-31). Weekends and configured public holidays are automatically ignored.',
            ]);
        }

        if ($errorCount > 0) {
            return $response->with('success', $message . " {$errorCount} row issue(s) were skipped.");
        }

        return $response->with('success', $message);
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

    private function parseHolidayDatesForMonth(string $rawValue, int $year, int $month): array
    {
        $value = trim($rawValue);
        if ($value === '') {
            return [];
        }

        $tokens = preg_split('/[\s,;]+/', $value) ?: [];
        $dates = [];
        $invalidTokens = [];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }

            if (preg_match('/^\d{1,2}$/', $token)) {
                $day = (int) $token;
                if ($day < 1 || $day > $daysInMonth) {
                    $invalidTokens[] = $token;
                    continue;
                }
                $dates[] = Carbon::create($year, $month, $day)->toDateString();
                continue;
            }

            if (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $token)) {
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $token);
                } catch (\Throwable) {
                    $invalidTokens[] = $token;
                    continue;
                }

                if ((int) $date->year !== $year || (int) $date->month !== $month) {
                    $invalidTokens[] = $token;
                    continue;
                }

                $dates[] = $date->toDateString();
                continue;
            }

            $invalidTokens[] = $token;
        }

        if (!empty($invalidTokens)) {
            throw new \InvalidArgumentException(
                'Attendance import is not valid. Public holiday dates must be day numbers (e.g. 18, 21) or full dates (e.g. 2026-04-18) within the selected month.'
            );
        }

        return array_values(array_unique($dates));
    }
}
