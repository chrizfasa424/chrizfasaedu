<?php

namespace App\Http\Controllers\Academic;

use App\Enums\GradeLevel;
use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\SchoolClass;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

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
            'grade_level' => ['required', new Enum(GradeLevel::class)],
            'section' => 'nullable|string',
            'capacity' => 'integer|min:1',
            'class_teacher_id' => 'nullable|exists:staff,id',
            'arms' => 'nullable|array',
        ]);

        $arms = array_filter($validated['arms'] ?? []);
        unset($validated['arms']);

        $class = SchoolClass::create($validated);

        foreach ($arms as $arm) {
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
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'grade_level'      => ['required', new Enum(GradeLevel::class)],
            'capacity'         => 'integer|min:1',
            'class_teacher_id' => 'nullable|exists:staff,id',
            'arms'             => 'nullable|array',
        ]);

        $arms = array_filter($validated['arms'] ?? []);
        unset($validated['arms']);

        $class->update($validated);

        // Sync arms: delete removed, add new
        $existingNames = $class->arms->pluck('name')->toArray();
        $newNames      = array_values($arms);

        foreach ($class->arms as $arm) {
            if (!in_array($arm->name, $newNames)) {
                $arm->delete();
            }
        }
        foreach ($newNames as $armName) {
            if (!in_array($armName, $existingNames)) {
                ClassArm::create([
                    'school_id' => auth()->user()->school_id,
                    'class_id'  => $class->id,
                    'name'      => $armName,
                    'capacity'  => $validated['capacity'] ?? $class->capacity ?? 40,
                ]);
            }
        }

        return redirect()->route('academic.classes.index')->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('academic.classes.index')->with('success', 'Class deleted.');
    }
}
