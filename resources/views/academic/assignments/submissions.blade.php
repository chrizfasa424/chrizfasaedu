@extends('layouts.app')
@section('title', 'Assignment Submissions')
@section('header', 'Assignment Submissions')

@section('content')
<div class="space-y-6">
    @php
        $targetSubjects = $assignment->targets
            ->pluck('subject.name')
            ->filter()
            ->unique()
            ->values();
        $subjectSummary = $targetSubjects->isNotEmpty() ? $targetSubjects->join(', ') : 'General';
    @endphp
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $assignment->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $subjectSummary }}
                @if($assignment->due_date)
                    | Due {{ $assignment->due_date->format('d M Y') }}
                @endif
                | Teacher: {{ $assignment->teacher?->full_name ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('academic.assignments.index') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Back to Assignments</a>
    </div>

    <div class="space-y-3">
        @forelse($submissions as $submission)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $submission->student?->full_name ?? 'Unknown Student' }}</h2>
                        <p class="text-xs text-slate-500">
                            {{ $submission->student?->admission_number ?? 'N/A' }}
                            @if($submission->student?->schoolClass)
                                | {{ $submission->student->schoolClass->grade_level?->label() ?? $submission->student->schoolClass->name }}
                            @endif
                            @if($submission->student?->arm)
                                - {{ $submission->student->arm->name }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Submitted: {{ $submission->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}</p>
                        @if($submission->reviewed_at)
                            <p class="text-xs text-slate-500">Reviewed: {{ $submission->reviewed_at->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700">{{ ucfirst($submission->status) }}</span>
                        <span class="inline-flex rounded-full bg-indigo-100 px-2 py-1 font-semibold text-indigo-700">
                            Score: {{ is_null($submission->score) ? 'Not set' : number_format((float) $submission->score, 2) }}
                        </span>
                        @if($submission->attachment_path)
                            <a href="{{ route('academic.assignment-submissions.download', $submission) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1.5 font-medium text-indigo-700 hover:bg-indigo-100">Download Submission</a>
                        @endif
                    </div>
                </div>

                @if($submission->submission_text)
                    <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                        <p class="text-xs font-semibold text-slate-600 mb-1">Submission Text</p>
                        {{ $submission->submission_text }}
                    </div>
                @endif

                @if($submission->student_feedback)
                    <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                        <p class="text-xs font-semibold mb-1">Student Feedback</p>
                        {{ $submission->student_feedback }}
                        @if($submission->student_feedback_at)
                            <p class="mt-1 text-[11px] text-blue-700">{{ $submission->student_feedback_at->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('academic.assignment-submissions.review', $submission) }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Score (0 - 100)</label>
                        <input type="number" name="score" min="0" max="100" step="0.01" value="{{ old('score') }}" placeholder="{{ is_null($submission->score) ? 'e.g. 75.5' : number_format((float) $submission->score, 2, '.', '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Teacher Feedback</label>
                        <textarea name="teacher_feedback" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Write feedback for this student...">{{ old('teacher_feedback') }}</textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700">Save Review</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-400">No student submissions yet for this assignment.</div>
        @endforelse
    </div>

    {{ $submissions->links() }}
</div>
@endsection
