@extends('layouts.app')

@section('title', 'Student Panel')
@section('header', 'Student Panel')

@php
    $testimonialsFormTitle = trim((string) ($publicPage['testimonials_form_title'] ?? 'Share Your Testimonial'));
    $testimonialsFormRoleLabel = trim((string) ($publicPage['testimonials_form_role_label'] ?? 'Role or Context'));
    $testimonialsFormRolePlaceholder = trim((string) ($publicPage['testimonials_form_role_placeholder'] ?? 'Student'));
    $testimonialsFormRatingLabel = trim((string) ($publicPage['testimonials_form_rating_label'] ?? 'Rating'));
    $testimonialsFormMessageLabel = trim((string) ($publicPage['testimonials_form_message_label'] ?? 'Your Testimonial'));
    $testimonialsFormMessagePlaceholder = trim((string) ($publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...'));
    $testimonialsFormSubmitText = trim((string) ($publicPage['testimonials_form_submit_text'] ?? 'Submit Testimonial'));
    $testimonialFormStartedAt = now()->timestamp;
@endphp

@section('content')
<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-xl font-semibold text-gray-900">Welcome, {{ $student->full_name }}</h3>
        <p class="mt-1 text-sm text-gray-600">
            Class: {{ $student->schoolClass->name ?? 'Not assigned' }}
            @if($student->arm && $student->arm->name)
                • Arm: {{ $student->arm->name }}
            @endif
        </p>

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recent Approved Results</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $results->count() }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attendance Records</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $attendance->count() }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recent Invoices</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $invoices->count() }}</p>
            </div>
        </div>
    </section>

    <section id="student-testimonial-form" class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <h3 class="text-xl font-semibold text-gray-900">{{ $testimonialsFormTitle !== '' ? $testimonialsFormTitle : 'Share Your Testimonial' }}</h3>
            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Admin Approval Required</span>
        </div>

        @if(session('testimonial_success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('testimonial_success') }}
            </div>
        @endif

        @if($errors->has('testimonial_form'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $errors->first('testimonial_form') }}
            </div>
        @endif

        <form action="{{ route('student.testimonials.submit') }}" method="POST" class="grid gap-4 md:grid-cols-2">
            @csrf
            <input type="hidden" name="started_at" value="{{ old('started_at', $testimonialFormStartedAt) }}">
            <div class="hidden" aria-hidden="true">
                <label for="website" class="sr-only">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Student Name</label>
                <input type="text" value="{{ $student->full_name }}" readonly class="w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-700">
            </div>

            <div>
                <label for="testimonial-role-title" class="mb-1 block text-sm font-semibold text-gray-700">{{ $testimonialsFormRoleLabel !== '' ? $testimonialsFormRoleLabel : 'Role or Context' }}</label>
                <input id="testimonial-role-title" type="text" name="role_title" maxlength="140" value="{{ old('role_title', 'Student') }}" placeholder="{{ $testimonialsFormRolePlaceholder !== '' ? $testimonialsFormRolePlaceholder : 'Student' }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                @error('role_title')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="testimonial-rating" class="mb-1 block text-sm font-semibold text-gray-700">{{ $testimonialsFormRatingLabel !== '' ? $testimonialsFormRatingLabel : 'Rating' }}</label>
                <select id="testimonial-rating" name="rating" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <option value="{{ $rating }}" {{ (int) old('rating', 5) === $rating ? 'selected' : '' }}>{{ $rating }} / 5</option>
                    @endforeach
                </select>
                @error('rating')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="testimonial-message" class="mb-1 block text-sm font-semibold text-gray-700">{{ $testimonialsFormMessageLabel !== '' ? $testimonialsFormMessageLabel : 'Your Testimonial' }}</label>
                <textarea id="testimonial-message" name="message" rows="5" required minlength="20" maxlength="1200" placeholder="{{ $testimonialsFormMessagePlaceholder !== '' ? $testimonialsFormMessagePlaceholder : 'Write your experience with the school...' }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">For security and moderation, links are not allowed and all submissions are reviewed before publication.</p>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="inline-flex rounded-full bg-indigo-700 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-800">
                    {{ $testimonialsFormSubmitText !== '' ? $testimonialsFormSubmitText : 'Submit Testimonial' }}
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-lg font-semibold text-gray-900">Your Recent Testimonial Submissions</h3>
        <p class="mt-1 text-sm text-gray-500">Track the review status of your submissions.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Rating</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Message</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($studentTestimonials as $testimonial)
                        @php
                            $status = strtolower((string) $testimonial->status);
                            $badgeClass = $status === 'approved'
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                        @endphp
                        <tr>
                            <td class="px-3 py-3 text-gray-600">{{ $testimonial->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                            <td class="px-3 py-3 text-gray-700">{{ max(1, min(5, (int) $testimonial->rating)) }}/5</td>
                            <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($testimonial->message, 130) }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">No testimonial submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
