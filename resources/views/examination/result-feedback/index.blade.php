@extends('layouts.app')
@section('title', 'Result Feedback')
@section('header', 'Result Feedback')

@section('content')
<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Open</p>
            <p class="mt-2 text-3xl font-extrabold text-amber-800">{{ $statusSummary['open'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">In Review</p>
            <p class="mt-2 text-3xl font-extrabold text-blue-800">{{ $statusSummary['in_review'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Resolved</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-800">{{ $statusSummary['resolved'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Closed</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-700">{{ $statusSummary['closed'] ?? 0 }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('examination.result-feedback.index') }}" class="grid gap-3 md:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All</option>
                    <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                    <option value="in_review" @selected(($filters['status'] ?? '') === 'in_review')>In Review</option>
                    <option value="resolved" @selected(($filters['status'] ?? '') === 'resolved')>Resolved</option>
                    <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Type</label>
                <select name="feedback_type" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All</option>
                    <option value="feedback" @selected(($filters['feedback_type'] ?? '') === 'feedback')>Feedback</option>
                    <option value="query" @selected(($filters['feedback_type'] ?? '') === 'query')>Query</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Term</label>
                <select name="term_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" @selected((int) ($filters['term_id'] ?? 0) === (int) $term->id)>
                            {{ $term->name }} - {{ $term->session?->name ?? 'Session' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Exam Type</label>
                <select name="exam_type_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All</option>
                    @foreach($examTypes as $examType)
                        <option value="{{ $examType->id }}" @selected((int) ($filters['exam_type_id'] ?? 0) === (int) $examType->id)>
                            {{ $examType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Student, title..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div class="md:col-span-5 flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Apply Filter</button>
                <a href="{{ route('examination.result-feedback.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-bold text-slate-900">Student Feedback and Queries</h2>
            <p class="mt-1 text-xs text-slate-500">Review and respond to students from the result center.</p>
        </div>

        @if($feedbacks->isEmpty())
            <div class="px-5 py-10 text-center text-sm text-slate-500">
                No result feedback found for this filter.
            </div>
        @else
            <div class="space-y-4 p-4">
                @foreach($feedbacks as $entry)
                    @php
                        $status = strtolower((string) $entry->status);
                        $statusBadge = match($status) {
                            'open' => 'bg-amber-100 text-amber-700',
                            'in_review' => 'bg-blue-100 text-blue-700',
                            'resolved' => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-slate-200 text-slate-700',
                        };
                        $typeBadge = $entry->feedback_type === 'query'
                            ? 'bg-red-100 text-red-700'
                            : 'bg-indigo-100 text-indigo-700';
                        $studentClass = $entry->student?->schoolClass?->grade_level?->label()
                            ?? $entry->student?->schoolClass?->name
                            ?? 'Class N/A';
                        $studentArm = $entry->student?->arm?->name ? (' ' . $entry->student->arm->name) : '';
                    @endphp
                    <article class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $typeBadge }}">
                                        {{ ucfirst($entry->feedback_type) }}
                                    </span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusBadge }}">
                                        {{ str_replace('_', ' ', ucfirst($status)) }}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-base font-bold text-slate-900">{{ $entry->title }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $entry->message }}</p>
                            </div>
                            <div class="text-right text-xs text-slate-500">
                                <p>{{ $entry->created_at?->format('d M Y, h:i A') }}</p>
                                <p class="mt-1">ID #{{ $entry->id }}</p>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-2 text-xs text-slate-600 md:grid-cols-2">
                            <p><span class="font-semibold text-slate-700">Student:</span> {{ $entry->student?->full_name ?? '-' }} ({{ $entry->student?->admission_number ?? $entry->student?->registration_number ?? '-' }})</p>
                            <p><span class="font-semibold text-slate-700">Class:</span> {{ $studentClass }}{{ $studentArm }}</p>
                            <p><span class="font-semibold text-slate-700">Term / Exam:</span> {{ $entry->term?->name ?? '-' }} / {{ $entry->examType?->name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Subject:</span> {{ $entry->subject?->name ?? 'General' }}</p>
                        </div>

                        <form method="POST" action="{{ route('examination.result-feedback.update', $entry) }}" class="mt-4 grid gap-3 lg:grid-cols-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Update Status</label>
                                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                                    <option value="open" @selected($status === 'open')>Open</option>
                                    <option value="in_review" @selected($status === 'in_review')>In Review</option>
                                    <option value="resolved" @selected($status === 'resolved')>Resolved</option>
                                    <option value="closed" @selected($status === 'closed')>Closed</option>
                                </select>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Admin Response</label>
                                <textarea name="admin_response" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Write a short response to student...">{{ old('admin_response') }}</textarea>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Save Update</button>
                            </div>
                        </form>

                        <div class="mt-3 flex justify-end">
                            <form method="POST" action="{{ route('examination.result-feedback.destroy', $entry) }}" onsubmit="return confirm('Delete this feedback message permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                    Delete Message
                                </button>
                            </form>
                        </div>

                        @if($entry->responder)
                            <p class="mt-2 text-[11px] text-slate-500">
                                Last updated by {{ $entry->responder->full_name }} on {{ $entry->responded_at?->format('d M Y, h:i A') ?? '-' }}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
