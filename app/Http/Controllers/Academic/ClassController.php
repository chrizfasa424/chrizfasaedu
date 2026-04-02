<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\Staff;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['arms', 'classTeacher.user', 'students'])->orderBy('order')->paginate(20);
        return view('academic.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'grade_level' => 'required|string',
            'section' => 'nullable|string',
            'capacity' => 'integer|min:1',
            'class_teacher_id' => 'nullable|exists:staff,id',
            'arms' => 'nullable|array',
        ]);

        $class = SchoolClass::create($validated);

        foreach ($request->input('arms', []) as $arm) {
            ClassArm::create([
                'school_id' => auth()->user()->school_id,
                'class_id' => $class->id,
                'name' => $arm,
                'capacity' => $validated['capacity'] ?? 40,
            ]);
        }

        return redirect()->route('academic.classes.index')->with('success', 'Class created.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['arms', 'classTeacher.user', 'students', 'subjects']);
        return view('academic.classes.show', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $class->update($request->validate([
            'name' => 'required|string',
            'capacity' => 'integer|min:1',
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]));
        return back()->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('academic.classes.index')->with('success', 'Class deleted.');
    }
}
