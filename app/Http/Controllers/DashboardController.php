<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Admission;
use App\Models\Result;
use App\Models\Timetable;
use App\Support\PublicPageContent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::active()->count(),
            'total_staff' => Staff::active()->count(),
            'pending_admissions' => Admission::pending()->count(),
            'outstanding_fees' => Invoice::where('status', '!=', 'paid')->sum('balance'),
            'recent_payments' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', now()->month)
                ->sum('amount_paid'),
            'failed_logins_today' => AuditLog::query()
                ->where('action', 'failed_login')
                ->whereDate('created_at', today())
                ->count(),
        ];

        $recentAdmissions = Admission::latest()->take(5)->get();
        $recentPayments = \App\Models\Payment::with('student')
            ->where('status', 'confirmed')
            ->latest()
            ->take(10)
            ->get();
        $recentAuditLogs = AuditLog::query()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact('stats', 'recentAdmissions', 'recentPayments', 'recentAuditLogs'));
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
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

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

    public function studentDashboard()
    {
        return view('dashboard.student');
    }

    public function parentDashboard()
    {
        return view('dashboard.parent');
    }
}
