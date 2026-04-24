@extends('layouts.app')
@section('title', 'My Assignments')
@section('header', 'My Assignments')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">My Assignments</h1>
        <p class="text-sm text-slate-500 mt-1">Assignments published for your class are listed here.</p>
    </div>

    <div class="space-y-3">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $matchingSubjectNames = $assignment->targets->filter(function ($target) use ($student) {
                    $sameClass = (int) ($target->class_id ?? 0) === (int) ($student->class_id ?? 0);
                    $armMatches = is_null($target->arm_id) || (int) $target->arm_id === (int) ($student->arm_id ?? 0);

                    return $sameClass && $armMatches;
                })->pluck('subject.name')->filter()->unique()->values();
                $subjectLabel = $matchingSubjectNames->isNotEmpty()
                    ? $matchingSubjectNames->join(', ')
                    : ($assignment->subject?->name ?? 'General Assignment');
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $assignment->title }}</h2>
                        <p class="text-xs text-slate-500">
                            {{ $subjectLabel }}
                            @if($assignment->due_date)
                                | Due {{ $assignment->due_date->format('d M Y') }}
                            @endif
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            @if(!$submission)
                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-1 font-semibold text-amber-700">Not Submitted</span>
                            @elseif($submission->status === 'reviewed')
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 font-semibold text-green-700">Reviewed</span>
                            @else
                                <span class="inline-flex rounded-full bg-indigo-100 px-2 py-1 font-semibold text-indigo-700">{{ ucfirst($submission->status) }}</span>
                            @endif
                            @if($submission && !is_null($submission->score))
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700">Score: {{ number_format((float) $submission->score, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('portal.assignments.download', $assignment) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Download</a>
                        <a href="{{ route('portal.assignments.show', $assignment) }}" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Open Assignment</a>
                    </div>
                </div>

                <p class="mt-3 text-sm text-slate-700">{{ $assignment->description ?: 'No instruction provided.' }}</p>

                <div class="mt-3 text-xs text-slate-500">
                    @if($assignment->session)
                        {{ $assignment->session->name }}
                    @endif
                    @if($assignment->term)
                        @if($assignment->session) | @endif {{ $assignment->term->name }}
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-400">No published assignments available for your class yet.</div>
        @endforelse
    </div>

    {{ $assignments->links() }}
</div>
@endsection
