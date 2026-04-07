<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('classes')->paginate(20);
        $classes  = SchoolClass::orderBy('order')->get();
        return view('academic.subjects.index', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:10|unique:subjects,code',
            'is_compulsory'=> 'boolean',
            'class_ids'    => 'nullable|array',
            'class_ids.*'  => 'exists:classes,id',
        ]);

        $classIds = $validated['class_ids'] ?? [];
        unset($validated['class_ids']);

        $subject = Subject::create(array_merge($validated, [
            'school_id' => auth()->user()->school_id,
        ]));

        if ($classIds) {
            $subject->classes()->sync($classIds);
        }

        return back()->with('success', 'Subject created.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => "required|string|max:10|unique:subjects,code,{$subject->id}",
            'is_compulsory'=> 'boolean',
            'class_ids'    => 'nullable|array',
            'class_ids.*'  => 'exists:classes,id',
        ]);

        $classIds = $validated['class_ids'] ?? [];
        unset($validated['class_ids']);

        $subject->update($validated);
        $subject->classes()->sync($classIds);

        return back()->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->classes()->detach();
        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }
}
