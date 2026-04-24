<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Staff;
use App\Models\StudentAttendance;
use App\Models\Invoice;
use App\Models\Admission;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Timetable;
use App\Support\PublicPageContent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $schoolId = $user?->school_id ? (int) $user->school_id : null;

        $stats = [
            'total_students' => Student::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->active()
                ->count(),
            'total_staff' => Staff::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->active()
                ->count(),
            'pending_admissions' => Admission::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->pending()
                ->count(),
            'outstanding_fees' => Invoice::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->where('status', '!=', 'paid')
                ->sum('balance'),
            'recent_payments' => Invoice::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->where('status', 'paid')
                ->whereMonth('updated_at', now()->month)
                ->sum('amount_paid'),
            'failed_logins_today' => AuditLog::query()
                ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
                ->where('action', 'failed_login')
                ->whereDate('created_at', today())
                ->count(),
        ];

        $recentAdmissions = Admission::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = \App\Models\Payment::with('student')
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->whereIn('status', ['approved', 'confirmed'])
            ->latest()
            ->take(10)
            ->get();

        $classDistribution = SchoolClass::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->where('is_active', true)
            ->withCount([
                'students as active_students_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(function (SchoolClass $class): array {
                $label = $class->grade_level?->label() ?? $class->name;
                $section = trim((string) ($class->section ?? ''));
                if ($section !== '') {
                    $label .= ' - ' . $section;
                }

                return [
                    'label' => $label,
                    'count' => (int) $class->active_students_count,
                ];
            })
            ->values();

        $staffRoleCounts = Staff::query()
            ->selectRaw('LOWER(COALESCE(users.role, "staff")) as role_key, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->when($schoolId, fn ($query) => $query->where('staff.school_id', $schoolId))
            ->where('staff.status', 'active')
            ->groupBy('role_key')
            ->pluck('total', 'role_key');

        $academicRoles = ['teacher', 'principal', 'vice_principal'];
        $academicCount = (int) $staffRoleCounts
            ->filter(fn ($count, $role) => in_array((string) $role, $academicRoles, true))
            ->sum();
        $nonAcademicCount = (int) $staffRoleCounts->sum() - $academicCount;

        $staffComposition = [
            ['label' => 'Academic Staff', 'count' => max(0, $academicCount)],
            ['label' => 'Non-Academic Staff', 'count' => max(0, $nonAcademicCount)],
        ];

        $genderCounts = Student::query()
            ->selectRaw('LOWER(TRIM(COALESCE(gender, "unspecified"))) as gender_key, COUNT(*) as total')
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->where('status', 'active')
            ->groupBy('gender_key')
            ->pluck('total', 'gender_key');

        $maleCount = 0;
        $femaleCount = 0;
        $otherCount = 0;

        foreach ($genderCounts as $genderKey => $count) {
            $normalized = (string) $genderKey;
            $value = (int) $count;

            if (in_array($normalized, ['male', 'm'], true)) {
                $maleCount += $value;
            } elseif (in_array($normalized, ['female', 'f'], true)) {
                $femaleCount += $value;
            } else {
                $otherCount += $value;
            }
        }

        $studentGenderDistribution = [
            ['label' => 'Male', 'count' => $maleCount],
            ['label' => 'Female', 'count' => $femaleCount],
            ['label' => 'Other / Unspecified', 'count' => $otherCount],
        ];

        $attendanceRecords = StudentAttendance::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->whereDate('date', '>=', now()->startOfWeek()->subWeeks(7)->toDateString())
            ->get(['date', 'status']);

        $attendanceTrendLabels = [];
        $attendanceTrendRates = [];

        for ($i = 7; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end = (clone $start)->endOfWeek();

            $attendanceTrendLabels[] = 'Wk ' . $start->format('d M');

            $weekRecords = $attendanceRecords->filter(
                fn ($record) => $record->date && $record->date->between($start, $end)
            );

            $total = $weekRecords->count();
            $presentOrLate = $weekRecords
                ->whereIn('status', ['present', 'late'])
                ->count();

            $attendanceTrendRates[] = $total > 0
                ? round(($presentOrLate / $total) * 100, 1)
                : 0;
        }

        $attendanceTrend = [
            'labels' => $attendanceTrendLabels,
            'rates' => $attendanceTrendRates,
        ];

        return view('dashboard.index', compact(
            'stats',
            'recentAdmissions',
            'recentPayments',
            'classDistribution',
            'staffComposition',
            'studentGenderDistribution',
            'attendanceTrend'
        ));
    }

    public function auditLogs(Request $request)
    {
        $user = auth()->user();

        abort_unless($user && in_array((string) ($user->role?->value ?? ''), [
            'super_admin',
            'school_admin',
            'principal',
            'vice_principal',
        ], true), 403, 'Unauthorized access.');

        $query = AuditLog::query()->with('user')->latest();

        if ($user->school_id) {
            $query->where('school_id', (int) $user->school_id);
        }

        if ($request->filled('action')) {
            $query->where('action', (string) $request->string('action'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));
            $query->where(function ($scope) use ($search) {
                $scope->where('action', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $actions = AuditLog::query()
            ->select('action')
            ->when($user->school_id, fn ($scope) => $scope->where('school_id', (int) $user->school_id))
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('system.audit-logs.index', compact('logs', 'actions'));
    }

    public function teacherDashboard()
    {
        $user = auth()->user();
        $school = $user->school;
        $staff = $user->staff;

        $publicPage = PublicPageContent::forSchool($school);

        $timetable = Timetable::with(['subject', 'schoolClass'])
            ->where('teacher_id', $staff?->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(LOWER(day_of_week), 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Timetable $slot) => ucfirst(strtolower((string) $slot->day_of_week)));

        $myClasses = $timetable->collapse()
            ->pluck('class_id')
            ->unique()
            ->values();

        $totalStudents = $myClasses->count()
            ? Student::whereIn('class_id', $myClasses)->where('status', 'active')->count()
            : 0;

        $recentResults = Result::with(['student', 'subject'])
            ->where('school_id', $school?->id)
            ->whereIn('class_id', $myClasses->all())
            ->latest()
            ->take(10)
            ->get();

        $pendingApproval = Result::where('school_id', $school?->id)
            ->whereIn('class_id', $myClasses->all())
            ->where('is_approved', false)
            ->count();

        return view('dashboard.teacher', compact(
            'staff', 'timetable', 'totalStudents',
            'recentResults', 'pendingApproval', 'publicPage'
        ));
    }

    public function staffDashboard()
    {
        $user = auth()->user();
        $staff = $user?->staffProfile;
        $schoolId = $user?->school_id ? (int) $user->school_id : null;

        $activeStudentsCount = Student::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->active()
            ->count();

        $activeStaffCount = Staff::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->active()
            ->count();

        return view('dashboard.staff', compact('user', 'staff', 'activeStudentsCount', 'activeStaffCount'));
    }

    public function studentDashboard()
    {
        return view('dashboard.student');
    }

    public function parentDashboard()
    {
        return view('dashboard.parent');
    }
}

