<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function store(Request $request, AcademicSession $session)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:50',
            'term'       => 'required|in:first,second,third',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AcademicTerm::create([
            ...$validated,
            'school_id'  => auth()->user()->school_id,
            'session_id' => $session->id,
        ]);

        return back()->with('success', "Term \"{$validated['name']}\" created.");
    }

    public function update(Request $request, AcademicTerm $term)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $term->update($validated);

        return back()->with('success', "Term \"{$validated['name']}\" updated.");
    }

    public function destroy(AcademicTerm $term)
    {
        if ($term->is_current) {
            return back()->with('error', 'Cannot delete the current term. Set another term as current first.');
        }

        $name = $term->name;
        $term->delete();

        return back()->with('success', "Term \"{$name}\" deleted.");
    }

    public function setCurrent(AcademicTerm $term)
    {
        // Unset all terms for this school
        AcademicTerm::where('school_id', auth()->user()->school_id)
            ->update(['is_current' => false]);

        // Also set the parent session as current
        AcademicSession::where('school_id', auth()->user()->school_id)
            ->update(['is_current' => false]);

        $term->update(['is_current' => true]);
        $term->session->update(['is_current' => true]);

        return back()->with('success', "{$term->name} ({$term->session->name}) set as the current term.");
    }
}
