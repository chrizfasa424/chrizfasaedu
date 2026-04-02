<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->get('class_id');
        $classes = SchoolClass::orderBy('order')->get();

        $timetable = $classId
            ? Timetable::with(['subject', 'teacher.user'])->where('class_id', $classId)->where('is_active', true)->orderBy('start_time')->get()->groupBy('day_of_week')
            : collect();

        return view('academic.timetable.index', compact('classes', 'timetable', 'classId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:staff,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string',
        ]);

        $session = auth()->user()->school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        Timetable::create([...$validated, 'school_id' => auth()->user()->school_id, 'session_id' => $session?->id, 'term_id' => $term?->id]);
        return back()->with('success', 'Timetable entry added.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return back()->with('success', 'Entry removed.');
    }
}
