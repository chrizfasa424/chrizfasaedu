<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Admission;
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
        ];

        $recentAdmissions = Admission::latest()->take(5)->get();
        $recentPayments = \App\Models\Payment::with('student')
            ->where('status', 'confirmed')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact('stats', 'recentAdmissions', 'recentPayments'));
    }

    public function teacherDashboard()
    {
        return view('dashboard.teacher');
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
