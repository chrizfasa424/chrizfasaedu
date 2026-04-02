<?php

namespace App\Http\Controllers\Portal;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\StudentAttendance;
use App\Models\Invoice;
use App\Models\Testimonial;
use App\Models\Timetable;
use App\Support\PublicPageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $student = $user->student;
        $school = $user->school;
        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }
        $session = $school->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();
        $publicPage = PublicPageContent::forSchool($school);

        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('is_approved', true)
            ->latest()
            ->take(10)
            ->get();

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->where('session_id', $session?->id)
            ->where('term_id', $term?->id)
            ->get();

        $invoices = Invoice::where('student_id', $student->id)->latest()->take(5)->get();

        $timetable = Timetable::with(['subject', 'teacher.user'])
            ->where('class_id', $student->class_id)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $studentTestimonials = Testimonial::query()
            ->where('school_id', $school->id)
            ->where('full_name', $student->full_name)
            ->latest()
            ->take(8)
            ->get();

        return view('portal.student.dashboard', compact(
            'student',
            'results',
            'attendance',
            'invoices',
            'timetable',
            'publicPage',
            'studentTestimonials'
        ));
    }

    public function submitTestimonial(Request $request)
    {
        $user = auth()->user();
        if (($user?->role?->value ?? null) !== UserRole::STUDENT->value) {
            abort(403, 'Only students can submit testimonials here.');
        }

        $student = $user->student;
        $school = $user->school;

        if (!$student || !$school) {
            abort(403, 'Student profile is not available.');
        }

        $publicPage = PublicPageContent::forSchool($school);
        $testimonialSuccessText = trim((string) ($publicPage['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.'));
        $testimonialErrorText = trim((string) ($publicPage['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.'));

        if (trim((string) $request->input('website', '')) !== '') {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
        }

        $validated = $request->validate([
            'role_title' => ['nullable', 'string', 'max:140'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'message' => ['required', 'string', 'min:20', 'max:1200'],
            'started_at' => ['required', 'integer'],
            'website' => ['nullable', 'max:0'],
        ]);

        $startedAt = (int) $validated['started_at'];
        if ($startedAt <= 0 || (now()->timestamp - $startedAt) < 3) {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        $sanitize = static function (?string $value): string {
            return trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');
        };

        $roleTitle = $sanitize($validated['role_title'] ?? '');
        $message = $sanitize($validated['message']);

        if ($message === '') {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        if (preg_match('/https?:\/\/|www\./i', $message)) {
            return redirect()
                ->to(route('student.dashboard') . '#student-testimonial-form')
                ->withErrors(['testimonial_form' => 'Links are not allowed in testimonials.'])
                ->withInput();
        }

        Testimonial::query()->create([
            'school_id' => $school->id,
            'full_name' => $student->full_name,
            'role_title' => $roleTitle !== '' ? $roleTitle : 'Student',
            'rating' => (int) $validated['rating'],
            'message' => $message,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        return redirect()
            ->to(route('student.dashboard') . '#student-testimonial-form')
            ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
    }
}
