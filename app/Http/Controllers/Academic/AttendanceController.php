<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->get('class_id');
        $date = $request->get('date', now()->toDateString());
        $classes = SchoolClass::orderBy('order')->get();

        $students = $classId ? Student::active()->inClass($classId)->get() : collect();
        $attendances = $classId ? StudentAttendance::where('class_id', $classId)->where('date', $date)->pluck('status', 'student_id') : collect();

        return view('academic.attendance.index', compact('classes', 'students', 'attendances', 'classId', 'date'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        $school = auth()->user()->school;
        $session = $school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        foreach ($validated['attendance'] as $record) {
            StudentAttendance::updateOrCreate(
                ['student_id' => $record['student_id'], 'date' => $validated['date']],
                [
                    'school_id' => $school->id,
                    'class_id' => $validated['class_id'],
                    'session_id' => $session?->id,
                    'term_id' => $term?->id,
                    'status' => $record['status'],
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Attendance saved.');
    }
}
