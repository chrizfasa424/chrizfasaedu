<?php

namespace App\Http\Controllers\Health;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalController extends Controller
{
    public function index(Request $request)
    {
        $records = MedicalRecord::with('student')->latest()->paginate(20);
        return view('health.index', compact('records'));
    }

    public function store(Request $request)
    {
        MedicalRecord::create([
            ...$request->validate([
                'student_id' => 'required|exists:students,id',
                'type' => 'required|in:checkup,clinic_visit,emergency,vaccination',
                'title' => 'required|string',
                'description' => 'nullable|string',
                'diagnosis' => 'nullable|string',
                'treatment' => 'nullable|string',
                'doctor_name' => 'nullable|string',
                'visit_date' => 'required|date',
                'follow_up_date' => 'nullable|date',
            ]),
            'school_id' => auth()->user()->school_id,
            'recorded_by' => auth()->id(),
        ]);
        return back()->with('success', 'Medical record saved.');
    }
}
