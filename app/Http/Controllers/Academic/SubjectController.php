<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::paginate(20);
        return view('academic.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        Subject::create($request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:subjects,code',
            'department' => 'nullable|string',
            'is_compulsory' => 'boolean',
            'credit_unit' => 'integer|min:1|max:5',
        ]));
        return back()->with('success', 'Subject created.');
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate([
            'name' => 'required|string',
            'code' => "required|string|unique:subjects,code,{$subject->id}",
            'department' => 'nullable|string',
            'is_compulsory' => 'boolean',
        ]));
        return back()->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }
}
