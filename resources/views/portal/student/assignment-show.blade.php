@extends('layouts.app')
@section('title', 'Assignment Details')
@section('header', 'Assignment Details')

@section('content')
<div class="space-y-6">
    @php
        $matchingSubjectNames = $assignment->targets->filter(function ($target) use ($student) {
            $sameClass = (int) ($target->class_id ?? 0) === (int) ($student->class_id ?? 0);
            $armMatches = is_null($target->arm_id) || (int) $target->arm_id === (int) ($student->arm_id ?? 0);

            return $sameClass && $armMatches;
        })->pluck('subject.name')->filter()->unique()->values();
        $subjectLabel = $matchingSubjectNames->isNotEmpty()
            ? $matchingSubjectNames->join(', ')
            : ($assignment->subject?->name ?? 'General Assignment');
    @endphp
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $assignment->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $subjectLabel }}
                @if($assignment->due_date)
                    | Due {{ $assignment->due_date->format('d M Y') }}
                @endif
            </p>
            <p class="text-xs text-slate-500 mt-1">Teacher: {{ $assignment->teacher?->full_name ?? 'N/A' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('portal.assignments.download', $assignment) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Download Attachment</a>
            <a href="{{ route('portal.assignments') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Back</a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700">Instruction</h2>
        <p class="mt-2 text-sm text-slate-700">{{ $assignment->description ?: 'No instruction provided.' }}</p>
        <div class="mt-3 text-xs text-slate-500">
            @if($assignment->session)
                {{ $assignment->session->name }}
            @endif
            @if($assignment->term)
                @if($assignment->session) | @endif {{ $assignment->term->name }}
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700">My Submission</h2>

        @if($submission)
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Status</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ ucfirst($submission->status) }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Submitted At</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ $submission->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Score</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ is_null($submission->score) ? 'Not graded yet' : number_format((float) $submission->score, 2) }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Teacher Feedback</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ $submission->teacher_feedback ?: 'No feedback yet' }}</p>
                </div>
            </div>

            @if($submission->submission_text)
                <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Your Submission Text</p>
                    {{ $submission->submission_text }}
                </div>
            @endif
        @else
            <p class="mt-3 text-sm text-amber-700">You have not submitted this assignment yet.</p>
        @endif

        <form method="POST" action="{{ route('portal.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Submission Text</label>
                <textarea name="submission_text" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('submission_text', $submission?->submission_text) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Upload File (pdf, jpg, jpeg, png, doc, docx)</label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    {{ $submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700">My Feedback / Review</h2>
        <p class="mt-2 text-xs text-slate-500">Use this to ask follow-up questions or comment on your assignment score/feedback.</p>

        @if($submission?->student_feedback)
            <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                <p class="text-xs font-semibold mb-1">Last Feedback Sent</p>
                {{ $submission->student_feedback }}
                @if($submission->student_feedback_at)
                    <p class="mt-1 text-[11px] text-blue-700">{{ $submission->student_feedback_at->format('d M Y, h:i A') }}</p>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('portal.assignments.feedback', $assignment) }}" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Feedback</label>
                <textarea name="student_feedback" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Share your review or question for your teacher...">{{ old('student_feedback') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-slate-800 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-900">Send Feedback</button>
            </div>
        </form>
    </div>
</div>
@endsection
