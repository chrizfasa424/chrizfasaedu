<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentGuardian;
use App\Models\School;
use App\Support\PublicPageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::with('session')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        $admissions = $query->paginate(25);
        return view('admission.index', compact('admissions'));
    }

    public function create()
    {
        return view('admission.create');
    }

    public function applyOnline(Request $request)
    {
        $host = $request->getHost();
        $school = School::query()
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN domain = ? THEN 0 ELSE 1 END', [$host])
            ->orderBy('id')
            ->first();
        $publicPage = PublicPageContent::forSchool($school);

        return view('admission.apply', compact('school', 'publicPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'other_names' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'class_applied_for' => 'required|string',
            'parent_name' => 'required|string',
            'parent_phone' => 'required|string',
            'parent_email' => 'nullable|email',
            'parent_occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'state_of_origin' => 'nullable|string',
            'lga' => 'nullable|string',
            'previous_school' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'birth_certificate' => 'nullable|file|max:5120',
            'previous_result' => 'nullable|file|max:5120',
        ]);

        $schoolId = auth()->user()?->school_id ?? $request->input('school_id');
        $session = \App\Models\AcademicSession::where('school_id', $schoolId)->current()->first();

        $validated['school_id'] = $schoolId;
        $validated['session_id'] = $session?->id;
        $validated['application_number'] = Admission::generateApplicationNumber($schoolId);
        $validated['status'] = AdmissionStatus::PENDING;

        foreach (['photo', 'birth_certificate', 'previous_result'] as $file) {
            if ($request->hasFile($file)) {
                $validated[$file] = $request->file($file)->store("admissions/{$file}s", 'public');
            }
        }

        $admission = Admission::create($validated);

        return redirect()->route('admission.show', $admission)
            ->with('success', "Application submitted. Number: {$admission->application_number}");
    }

    public function show(Admission $admission)
    {
        return view('admission.show', compact('admission'));
    }

    public function review(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,screening',
            'review_notes' => 'nullable|string',
            'screening_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $admission->update([
            ...$validated,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Application {$validated['status']}.");
    }

    public function enroll(Admission $admission)
    {
        abort_if($admission->status !== AdmissionStatus::APPROVED, 403, 'Only approved admissions can be enrolled.');

        DB::transaction(function () use ($admission) {
            $schoolId = $admission->school_id;

            // Create user account
            $user = User::create([
                'school_id' => $schoolId,
                'first_name' => $admission->first_name,
                'last_name' => $admission->last_name,
                'email' => $admission->email ?? strtolower($admission->first_name . '.' . $admission->last_name . '@student.chrizfasa.ng'),
                'password' => Hash::make('changeme123'),
                'role' => 'student',
            ]);

            // Create student record
            $student = Student::create([
                'school_id' => $schoolId,
                'user_id' => $user->id,
                'admission_id' => $admission->id,
                'first_name' => $admission->first_name,
                'last_name' => $admission->last_name,
                'other_names' => $admission->other_names,
                'gender' => $admission->gender,
                'date_of_birth' => $admission->date_of_birth,
                'state_of_origin' => $admission->state_of_origin,
                'lga' => $admission->lga,
                'address' => $admission->address,
                'photo' => $admission->photo,
                'previous_school' => $admission->previous_school,
                'admission_number' => Student::generateAdmissionNumber($schoolId),
                'session_admitted' => $admission->session?->name,
                'status' => 'active',
            ]);

            // Create parent record
            $nameParts = explode(' ', $admission->parent_name, 2);
            $parent = ParentGuardian::create([
                'school_id' => $schoolId,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? $nameParts[0],
                'phone' => $admission->parent_phone,
                'email' => $admission->parent_email,
                'occupation' => $admission->parent_occupation,
            ]);

            $student->parents()->attach($parent->id, ['relationship' => 'parent']);

            $admission->update([
                'status' => AdmissionStatus::ENROLLED,
                'admission_number' => $student->admission_number,
            ]);
        });

        return back()->with('success', 'Student enrolled successfully.');
    }
}
