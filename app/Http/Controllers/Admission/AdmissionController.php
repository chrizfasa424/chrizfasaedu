<?php

namespace App\Http\Controllers\Admission;

use App\Enums\AdmissionStatus;
use App\Http\Controllers\Controller;
use App\Mail\AdmissionReceived;
use App\Mail\AdmissionStatusChanged;
use App\Models\Admission;
use App\Models\AcademicSession;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentGuardian;
use App\Support\DomainHelper;
use App\Support\NigeriaData;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdmissionController extends Controller
{
    // ── Public: show form ──────────────────────────────────────────
    public function applyOnline(Request $request)
    {
        $school     = $this->resolvePublicSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $states     = NigeriaData::statesWithLgas();

        try {
            $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
        } catch (\Throwable $e) {
            $theme = [];
        }

        $primary     = data_get($theme, 'primary.500',     '#2D1D5C');
        $secondary   = data_get($theme, 'secondary.500',   '#DFE753');
        $header      = data_get($theme, 'header',          '#2D1D5C');
        $bg          = data_get($theme, 'site_background', '#F8FAFC');
        $ink         = data_get($theme, 'ink',             '#0F172A');
        $muted       = data_get($theme, 'muted',           '#475569');
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $schoolName  = $school?->name ?? 'Our School';

        return view('admission.apply', compact(
            'school', 'publicPage', 'states', 'theme',
            'primary', 'secondary', 'header', 'bg', 'ink', 'muted',
            'faviconPath', 'schoolName'
        ));
    }

    // ── Public: handle submission ──────────────────────────────────
    public function store(Request $request)
    {
        // Honeypot — silent fake-success for bots
        if ($request->filled('hp_website')) {
            return redirect()->route('admission.success')
                ->with('application_number', 'PENDING')
                ->with('email_sent', false)
                ->with('parent_email', '');
        }

        $validated = $request->validate([
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'other_names'         => 'nullable|string|max:100',
            'gender'              => 'required|in:male,female',
            'date_of_birth'       => 'required|date|before:today',
            'class_applied_for'   => 'required|string|max:100',
            'parent_name'         => 'required|string|max:200',
            'parent_phone'        => ['required','string','max:20','regex:/^[0-9\+\-\s\(\)]{7,20}$/'],
            'parent_email'        => 'required|email:rfc,filter|max:255',
            'parent_occupation'   => 'nullable|string|max:100',
            'state_of_origin'     => 'required|string|max:100',
            'lga'                 => 'required|string|max:100',
            'address'             => 'nullable|string|max:500',
            'previous_school'     => 'nullable|string|max:200',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birth_certificate'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'previous_result'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $school   = $this->resolvePublicSchool($request);
        $schoolId = $school?->id ?? $request->integer('school_id');
        $session  = AcademicSession::where('school_id', $schoolId)->current()->first()
                 ?? AcademicSession::where('school_id', $schoolId)->latest()->first();

        $data = array_merge(
            collect($validated)->except(['photo', 'birth_certificate', 'previous_result'])->toArray(),
            [
                'school_id'  => $schoolId,
                'session_id' => $session?->id,
                'status'     => AdmissionStatus::PENDING,
            ]
        );

        foreach (['photo', 'birth_certificate', 'previous_result'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $data[$fileField] = $request->file($fileField)->store("admissions/{$fileField}s", 'public');
            }
        }

        $admission = DB::transaction(function () use ($data, $schoolId) {
            $data['application_number'] = Admission::generateApplicationNumber($schoolId);
            return Admission::create($data);
        });

        // Attempt confirmation email — force success even if email fails
        $emailSent = false;
        try {
            Mail::to($admission->parent_email)->send(new AdmissionReceived($admission, $school));
            $emailSent = true;
        } catch (\Throwable $e) {
            Log::warning('Admission confirmation email failed', [
                'admission_id' => $admission->id,
                'error'        => $e->getMessage(),
            ]);
        }

        return redirect()->route('admission.success')
            ->with('application_number', $admission->application_number)
            ->with('email_sent', $emailSent)
            ->with('parent_email', $admission->parent_email);
    }

    // ── Public: success page ───────────────────────────────────────
    public function success(Request $request)
    {
        if (!session()->has('application_number')) {
            return redirect()->route('admission.apply');
        }

        $school     = $this->resolvePublicSchool($request);
        $publicPage = PublicPageContent::forSchool($school);

        try {
            $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
        } catch (\Throwable $e) {
            $theme = [];
        }

        $primary     = data_get($theme, 'primary.500',     '#2D1D5C');
        $secondary   = data_get($theme, 'secondary.500',   '#DFE753');
        $header      = data_get($theme, 'header',          '#2D1D5C');
        $bg          = data_get($theme, 'site_background', '#F8FAFC');
        $schoolName  = $school?->name ?? 'Our School';
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $appNum      = session('application_number', '—');
        $emailSent   = session('email_sent', false);
        $parentEmail = session('parent_email', '');

        return view('admission.success', compact(
            'school', 'publicPage', 'schoolName', 'theme',
            'primary', 'secondary', 'header', 'bg',
            'faviconPath', 'appNum', 'emailSent', 'parentEmail'
        ));
    }

    // ── Admin: list ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Admission::with('session')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $admissions = $query->paginate(10)->withQueryString();

        $stats = [
            'total'    => Admission::count(),
            'pending'  => Admission::where('status', AdmissionStatus::PENDING)->count(),
            'approved' => Admission::where('status', AdmissionStatus::APPROVED)->count(),
            'rejected' => Admission::where('status', AdmissionStatus::REJECTED)->count(),
            'enrolled' => Admission::where('status', AdmissionStatus::ENROLLED)->count(),
        ];

        return view('admission.index', compact('admissions', 'stats'));
    }

    // ── Admin: detail ──────────────────────────────────────────────
    public function show(Admission $admission)
    {
        $admission->load('session', 'reviewer');
        return view('admission.show', compact('admission'));
    }

    // ── Admin: review (approve / reject / screening) ───────────────
    public function review(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'status'          => 'required|in:approved,rejected,screening,under_review',
            'review_notes'    => 'nullable|string|max:2000',
            'screening_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $previousStatus = $admission->status->value;

        $admission->update([
            ...$validated,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send email when status changes to approved or rejected
        if (
            $admission->parent_email &&
            in_array($validated['status'], ['approved', 'rejected']) &&
            $previousStatus !== $validated['status']
        ) {
            try {
                $school = auth()->user()?->school;
                Mail::to($admission->parent_email)->send(new AdmissionStatusChanged($admission, $school));
            } catch (\Throwable $e) {
                Log::warning('Admission status email failed', [
                    'admission_id' => $admission->id,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $label = ucfirst(str_replace('_', ' ', $validated['status']));
        return back()->with('success', "Application status updated to: {$label}.");
    }

    // ── Admin: delete ──────────────────────────────────────────────
    public function destroy(Admission $admission)
    {
        foreach (['photo', 'birth_certificate', 'previous_result'] as $fileField) {
            if ($admission->$fileField) {
                Storage::disk('public')->delete($admission->$fileField);
            }
        }

        $admission->delete();

        return redirect()->route('admission.index')
            ->with('success', 'Application deleted successfully.');
    }

    // ── Admin: enroll approved applicant ──────────────────────────
    public function enroll(Admission $admission)
    {
        abort_if($admission->status !== AdmissionStatus::APPROVED, 403, 'Only approved applications can be enrolled.');

        DB::transaction(function () use ($admission) {
            $schoolId = $admission->school_id;

            $user = User::create([
                'school_id'  => $schoolId,
                'first_name' => $admission->first_name,
                'last_name'  => $admission->last_name,
                'email'      => $admission->parent_email
                    ?? strtolower($admission->first_name . '.' . $admission->last_name . '@student.school.ng'),
                'password'   => Hash::make('changeme123'),
                'role'       => 'student',
            ]);

            $student = Student::create([
                'school_id'        => $schoolId,
                'user_id'          => $user->id,
                'admission_id'     => $admission->id,
                'first_name'       => $admission->first_name,
                'last_name'        => $admission->last_name,
                'other_names'      => $admission->other_names,
                'gender'           => $admission->gender,
                'date_of_birth'    => $admission->date_of_birth,
                'state_of_origin'  => $admission->state_of_origin,
                'lga'              => $admission->lga,
                'address'          => $admission->address,
                'photo'            => $admission->photo,
                'previous_school'  => $admission->previous_school,
                'admission_number' => Student::generateAdmissionNumber($schoolId),
                'session_admitted' => $admission->session?->name,
                'status'           => 'active',
            ]);

            $nameParts = explode(' ', $admission->parent_name, 2);
            $parent = ParentGuardian::create([
                'school_id'  => $schoolId,
                'first_name' => $nameParts[0],
                'last_name'  => $nameParts[1] ?? $nameParts[0],
                'phone'      => $admission->parent_phone,
                'email'      => $admission->parent_email,
                'occupation' => $admission->parent_occupation,
            ]);

            $student->parents()->attach($parent->id, ['relationship' => 'parent']);

            $admission->update([
                'status'           => AdmissionStatus::ENROLLED,
                'admission_number' => $student->admission_number,
            ]);
        });

        return back()->with('success', 'Student enrolled and account created successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function resolvePublicSchool(Request $request): ?School
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return SchoolContext::current($request);
        }

        $host           = $request->getHost();
        $normalizedHost = DomainHelper::normalize($host);

        if ($normalizedHost) {
            $school = School::query()
                ->where('is_active', true)
                ->where('domain', $normalizedHost)
                ->first();
            if ($school) {
                return $school;
            }
        }

        return School::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('domain')->orWhere('domain', '');
            })
            ->orderBy('id')
            ->first();
    }
}
