<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::with('terms')->latest()->paginate(15);
        return view('academic.sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $session = AcademicSession::create([
            ...$validated,
            'slug' => str($validated['name'])->slug(),
            'school_id' => auth()->user()->school_id,
        ]);

        // Auto-create 3 terms
        foreach (['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'] as $key => $name) {
            AcademicTerm::create([
                'school_id' => auth()->user()->school_id,
                'session_id' => $session->id,
                'name' => $name,
                'term' => $key,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);
        }

        return redirect()->route('academic.sessions.index')->with('success', 'Session created with 3 terms.');
    }

    public function setAsCurrent(AcademicSession $session)
    {
        AcademicSession::where('school_id', auth()->user()->school_id)->update(['is_current' => false]);
        $session->update(['is_current' => true]);
        return back()->with('success', "{$session->name} set as current session.");
    }
}
