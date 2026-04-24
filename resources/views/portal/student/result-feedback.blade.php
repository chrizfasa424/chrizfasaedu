@extends('layouts.app')
@section('title', 'Result Feedback')
@section('header', 'Result Feedback')

@php
    $primaryColor = trim((string) data_get($school->settings ?? [], 'branding.primary_color', '#2D1D5C'));
    $replyParentOldId = (int) old('parent_feedback_id', 0);
@endphp

@section('content')
<div class="space-y-6">
    <section class="relative overflow-hidden rounded-3xl p-6 shadow-xl sm:p-8"
        style="background:linear-gradient(135deg, {{ $primaryColor }} 0%, #23134e 58%, #130a2a 100%);">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_22%,rgba(223,231,83,0.2),transparent_36%)]"></div>
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/65">Student Support</p>
                <h1 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">Result Feedback Center</h1>
                <p class="mt-2 text-sm text-white/80">
                    Submit feedback or raise a query for any published result.
                </p>
            </div>
            <a href="{{ route('portal.results.center') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Back To Results
            </a>
        </div>
    </section>

    @if(session('result_feedback_success'))
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-700 shadow-sm">
            {{ session('result_feedback_success') }}
        </section>
    @endif

    @if(($unreadResponses ?? collect())->isNotEmpty())
        <section class="rounded-3xl border border-amber-200 bg-amber-50 shadow-sm">
            <div class="border-b border-amber-200 px-6 py-4">
                <h2 class="text-base font-bold text-amber-900">New Admin Responses</h2>
                <p class="mt-0.5 text-xs text-amber-800">Select any response to read it. Unopened responses remain in your bell notification.</p>
            </div>
            <div class="divide-y divide-amber-200/70">
                @foreach($unreadResponses as $response)
                    <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $response->title }}</p>
                            <p class="text-xs text-slate-600">
                                {{ ucfirst($response->feedback_type) }} | {{ $response->term?->name ?? '-' }} | {{ $response->examType?->name ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-500">Responded {{ $response->responded_at?->format('d M Y, h:i A') ?? '-' }}</p>
                        </div>
                        <form method="POST" action="{{ route('portal.results.feedback.read', $response) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Read Response
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section id="result-feedback" class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Result Feedback</h2>
            <p class="mt-1 text-xs text-slate-500">Share positive notes or improvement feedback about your result.</p>

            <form method="POST" action="{{ route('portal.results.feedback.store') }}" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="feedback_type" value="feedback">
                <input type="hidden" name="student_result_id" value="{{ $activeSheet?->id }}">
                <input type="hidden" name="term_id" value="{{ $activeSheet?->term_id }}">
                <input type="hidden" name="exam_type_id" value="{{ $activeSheet?->exam_type_id }}">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Title</label>
                    <input type="text" name="title" value="{{ $replyParentOldId === 0 ? old('title') : '' }}"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        placeholder="Example: Great Progress In Mathematics">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Subject (Optional)</label>
                    <select name="subject_id"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="">General Feedback</option>
                        @foreach($feedbackSubjects as $subject)
                            <option value="{{ $subject->id }}" @selected((int) old('subject_id') === (int) $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Message</label>
                    <textarea name="message" rows="4"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        placeholder="Write your feedback...">{{ $replyParentOldId === 0 ? old('message') : '' }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Submit Feedback
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Raise Result Query</h2>
            <p class="mt-1 text-xs text-slate-500">Ask the academic team to review any concern in your result.</p>

            <form method="POST" action="{{ route('portal.results.feedback.store') }}" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="feedback_type" value="query">
                <input type="hidden" name="student_result_id" value="{{ $activeSheet?->id }}">
                <input type="hidden" name="term_id" value="{{ $activeSheet?->term_id }}">
                <input type="hidden" name="exam_type_id" value="{{ $activeSheet?->exam_type_id }}">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Query Title</label>
                    <input type="text" name="title" value="{{ $replyParentOldId === 0 ? old('title') : '' }}"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        placeholder="Example: Please Review English Studies Score">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Subject (Optional)</label>
                    <select name="subject_id"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="">General Query</option>
                        @foreach($feedbackSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Query Details</label>
                    <textarea name="message" rows="4"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        placeholder="State your concern clearly so it can be reviewed quickly.">{{ $replyParentOldId === 0 ? old('message') : '' }}</textarea>
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Raise Query
                </button>
            </form>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Feedback / Query History</h2>
            <p class="mt-0.5 text-xs text-slate-500">Track what you submitted and current review status.</p>
        </div>
        @if($feedbackHistory->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-500">
                No feedback or query submitted yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Term / Exam</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-left">Admin Response</th>
                            <th class="px-6 py-3 text-center">Action</th>
                            <th class="px-6 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($feedbackHistory as $entry)
                            @php
                                $status = strtolower((string) $entry->status);
                                $statusBadge = match ($status) {
                                    'resolved' => 'bg-emerald-100 text-emerald-700',
                                    'in_review' => 'bg-blue-100 text-blue-700',
                                    'closed' => 'bg-slate-200 text-slate-700',
                                    default => 'bg-amber-100 text-amber-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $entry->feedback_type === 'query' ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ ucfirst($entry->feedback_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">{{ $entry->title }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $entry->subject?->name ?? 'General' }}</td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    {{ $entry->term?->name ?? '-' }} / {{ $entry->examType?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusBadge }}">
                                        {{ str_replace('_', ' ', ucfirst($status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    @if($entry->admin_response)
                                        @if(is_null($entry->student_read_at) && $entry->feedback_type === 'query')
                                            <div class="mb-1">
                                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">
                                                    New Response
                                                </span>
                                            </div>
                                            <form method="POST" action="{{ route('portal.results.feedback.read', $entry) }}" class="mb-1">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900">
                                                    Open response
                                                </button>
                                            </form>
                                        @endif
                                        <p class="text-xs leading-5">{{ \Illuminate\Support\Str::limit($entry->admin_response, 140) }}</p>
                                    @else
                                        <span class="text-xs text-slate-400">No response yet</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <button type="button"
                                        class="reply-toggle inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:border-indigo-300 hover:bg-indigo-100"
                                        data-target="reply-row-{{ $entry->id }}">
                                        Reply
                                    </button>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-500">{{ $entry->created_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr id="reply-row-{{ $entry->id }}" class="{{ $replyParentOldId === (int) $entry->id ? '' : 'hidden' }} bg-slate-50/80">
                                <td colspan="8" class="px-6 py-4">
                                    <form method="POST" action="{{ route('portal.results.feedback.store') }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="feedback_type" value="{{ $entry->feedback_type }}">
                                        <input type="hidden" name="parent_feedback_id" value="{{ $entry->id }}">
                                        <input type="hidden" name="student_result_id" value="{{ $entry->student_result_id }}">
                                        <input type="hidden" name="term_id" value="{{ $entry->term_id }}">
                                        <input type="hidden" name="exam_type_id" value="{{ $entry->exam_type_id }}">
                                        @if($entry->subject_id)
                                            <input type="hidden" name="subject_id" value="{{ $entry->subject_id }}">
                                        @endif
                                        <input type="hidden" name="title" value="{{ \Illuminate\Support\Str::limit('Re: ' . $entry->title, 180, '') }}">

                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Reply To: {{ $entry->title }}</p>
                                                <p class="text-xs text-slate-500">Your reply will be sent as a new follow-up in this thread.</p>
                                            </div>
                                            <button type="button"
                                                class="reply-cancel inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100"
                                                data-target="reply-row-{{ $entry->id }}">
                                                Cancel
                                            </button>
                                        </div>

                                        <textarea name="message" rows="3"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                            placeholder="Write your reply...">{{ $replyParentOldId === (int) $entry->id ? old('message') : '' }}</textarea>

                                        <div class="flex justify-end">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                                Send Reply
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($feedbackHistory->hasPages())
                @php
                    $currentPage = (int) $feedbackHistory->currentPage();
                    $lastPage = (int) $feedbackHistory->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp
                <div class="border-t border-slate-100 px-6 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-medium text-slate-500">
                            Showing {{ $feedbackHistory->firstItem() }} - {{ $feedbackHistory->lastItem() }} of {{ $feedbackHistory->total() }} entries
                        </p>
                        <div class="flex items-center gap-2">
                            @if($feedbackHistory->onFirstPage())
                                <span class="inline-flex cursor-not-allowed items-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400">Prev</span>
                            @else
                                <a href="{{ $feedbackHistory->previousPageUrl() }}" class="inline-flex items-center rounded-xl border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">Prev</a>
                            @endif

                            @for($page = $startPage; $page <= $endPage; $page++)
                                @if($page === $currentPage)
                                    <span class="inline-flex min-w-[2rem] items-center justify-center rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $feedbackHistory->url($page) }}" class="inline-flex min-w-[2rem] items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">{{ $page }}</a>
                                @endif
                            @endfor

                            @if($feedbackHistory->hasMorePages())
                                <a href="{{ $feedbackHistory->nextPageUrl() }}" class="inline-flex items-center rounded-xl border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">Next</a>
                            @else
                                <span class="inline-flex cursor-not-allowed items-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400">Next</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const toggleButtons = document.querySelectorAll('.reply-toggle');
        const cancelButtons = document.querySelectorAll('.reply-cancel');
        const rows = document.querySelectorAll('[id^="reply-row-"]');

        const hideAll = () => {
            rows.forEach((row) => row.classList.add('hidden'));
        };

        toggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const target = targetId ? document.getElementById(targetId) : null;
                if (!target) return;
                const isHidden = target.classList.contains('hidden');
                hideAll();
                if (isHidden) {
                    target.classList.remove('hidden');
                }
            });
        });

        cancelButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const target = targetId ? document.getElementById(targetId) : null;
                if (target) {
                    target.classList.add('hidden');
                }
            });
        });
    })();
</script>
@endpush
