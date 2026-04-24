<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financialDashboard()
    {
        $schoolId = auth()->user()->school_id;

        $totalRevenue = Payment::where('school_id', $schoolId)->whereIn('status', ['approved', 'confirmed'])->sum('amount');
        $outstandingFees = Invoice::where('school_id', $schoolId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');

        $monthlyRevenue = Payment::where('school_id', $schoolId)
            ->whereIn('status', ['approved', 'confirmed'])
            ->whereYear('paid_at', now()->year)
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $paymentsByMethod = Payment::where('school_id', $schoolId)
            ->whereIn('status', ['approved', 'confirmed'])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        return view('reporting.financial', compact('totalRevenue', 'outstandingFees', 'monthlyRevenue', 'paymentsByMethod'));
    }

    public function academicDashboard()
    {
        $schoolId = auth()->user()->school_id;

        $enrollmentByClass = Student::where('school_id', $schoolId)
            ->active()
            ->selectRaw('class_id, COUNT(*) as count')
            ->groupBy('class_id')
            ->with('schoolClass')
            ->get();

        $genderDistribution = Student::where('school_id', $schoolId)
            ->active()
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        return view('reporting.academic', compact('enrollmentByClass', 'genderDistribution'));
    }

    public function attendanceReport(Request $request)
    {
        $data = StudentAttendance::where('school_id', auth()->user()->school_id)
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->when($request->month, fn($q) => $q->whereMonth('date', $request->month))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('reporting.attendance', compact('data'));
    }
}

