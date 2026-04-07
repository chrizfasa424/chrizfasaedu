@extends('layouts.app')
@section('title', 'Student Portal')
@section('header', 'Student Portal')

@php
    // ── Public-page labels ──────────────────────────────────────────────────
    $testimonialsFormTitle           = trim((string) ($publicPage['testimonials_form_title']           ?? 'Share Your Testimonial'));
    $testimonialsFormRoleLabel       = trim((string) ($publicPage['testimonials_form_role_label']       ?? 'Role or Context'));
    $testimonialsFormRolePlaceholder = trim((string) ($publicPage['testimonials_form_role_placeholder'] ?? 'Student'));
    $testimonialsFormRatingLabel     = trim((string) ($publicPage['testimonials_form_rating_label']     ?? 'Rating'));
    $testimonialsFormMessageLabel    = trim((string) ($publicPage['testimonials_form_message_label']    ?? 'Your Testimonial'));
    $testimonialsFormMessagePlaceholder = trim((string) ($publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...'));
    $testimonialsFormSubmitText      = trim((string) ($publicPage['testimonials_form_submit_text']      ?? 'Submit Testimonial'));
    $testimonialFormStartedAt        = now()->timestamp;
    $primaryColor   = trim((string) ($publicPage['primary_color']   ?? '#2D1D5C'));
    $secondaryColor = trim((string) ($publicPage['secondary_color'] ?? '#DFE753'));

    // ── Attendance stats ────────────────────────────────────────────────────
    $totalDays      = $attendance->count();
    $presentDays    = $attendance->whereIn('status', ['present', 'late'])->count();
    $absentDays     = $attendance->where('status', 'absent')->count();
    $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

    // ── Fees stats ──────────────────────────────────────────────────────────
    $outstandingBalance = $invoices
        ->filter(fn ($inv) => ! in_array($inv->status?->value ?? '', ['paid', 'waived', 'refunded']))
        ->sum('balance');

    // ── Results stats ───────────────────────────────────────────────────────
    $avgScore    = $results->count() > 0 ? round($results->avg('total_score'), 1) : null;
    $subjectCount = $results->pluck('subject_id')->unique()->count();

    // ── Grade badge helper ──────────────────────────────────────────────────
    $gradeBadge = function (string $grade): string {
        return match ($grade) {
            'A'     => 'bg-emerald-100 text-emerald-700',
            'B'     => 'bg-blue-100 text-blue-700',
            'C'     => 'bg-indigo-100 text-indigo-700',
            'D'     => 'bg-amber-100 text-amber-700',
            'E'     => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    };

    // ── Day order for timetable ─────────────────────────────────────────────
    $dayOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $timetableSorted = collect($dayOrder)
        ->mapWithKeys(fn ($day) => [$day => $timetable->get($day, collect())])
        ->filter(fn ($slots) => $slots->count() > 0);
@endphp

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════
         WELCOME HERO BANNER
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl"
         style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, #3a2872 60%, #1a0f3a 100%);">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 80% at 80% 50%, rgba(223,231,83,0.10) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.05) 1px,transparent 1px);background-size:24px 24px;pointer-events:none;"></div>

        <div class="relative z-10 flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8 lg:p-10">
            <div class="flex items-center gap-5">
                @if($student->photo)
                    <img src="{{ asset('storage/' . ltrim($student->photo, '/')) }}"
                         alt="{{ $student->full_name }}"
                         class="h-20 w-20 rounded-2xl border-2 border-white/20 object-cover shadow-xl">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-2 border-white/15 text-3xl font-extrabold shadow-xl"
                         style="background:{{ $secondaryColor }};color:{{ $primaryColor }}">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student->first_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/50">Student Portal</p>
                    <h1 class="mt-1 text-2xl font-extrabold leading-tight text-white sm:text-3xl">
                        {{ $student->full_name }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold text-white/80 backdrop-blur">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h11.25v16.5H6.75A2.25 2.25 0 0 0 4.5 21V5.25Z"/></svg>
                            {{ $student->schoolClass?->name ?? 'Not Assigned' }}
                            @if($student->arm?->name) &mdash; {{ $student->arm->name }} @endif
                        </span>
                        @if($student->admission_number)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/8 px-3 py-1 text-xs text-white/60">
                            ID: {{ $student->admission_number }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick stat pills --}}
            <div class="flex flex-wrap gap-3 sm:flex-col sm:items-end">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Attendance</p>
                    <p class="mt-0.5 text-2xl font-extrabold" style="color:{{ $secondaryColor }}">{{ $attendanceRate }}%</p>
                </div>
                @if($outstandingBalance > 0)
                <div class="rounded-2xl border border-red-300/30 bg-red-500/20 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Outstanding</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-red-300">₦{{ number_format($outstandingBalance, 2) }}</p>
                </div>
                @else
                <div class="rounded-2xl border border-emerald-300/30 bg-emerald-500/20 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Fees</p>
                    <p class="mt-0.5 text-lg font-extrabold text-emerald-300">All Clear</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         STAT CARDS
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {{-- Attendance --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                </div>
                @php
                    $attColor = $attendanceRate >= 90 ? 'text-emerald-600' : ($attendanceRate >= 75 ? 'text-amber-600' : 'text-red-600');
                @endphp
                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">{{ $totalDays }}d</span>
            </div>
            <p class="mt-4 text-3xl font-extrabold {{ $attColor }}">{{ $attendanceRate }}%</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Attendance Rate</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ $presentDays }} present &bull; {{ $absentDays }} absent</p>
            <a href="{{ route('portal.attendance') }}" class="mt-3 block text-xs text-indigo-600 hover:underline font-medium">View full attendance →</a>
        </div>

        {{-- Results --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M7.5 16.5v-4.5M12 16.5V8.25M16.5 16.5v-6.75"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $results->count() }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Approved Results</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ $subjectCount }} subject{{ $subjectCount !== 1 ? 's' : '' }}</p>
        </div>

        {{-- Average Score --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-violet-600"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $avgScore ?? '—' }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Average Score</p>
            <p class="mt-0.5 text-xs text-slate-400">Approved results only</p>
        </div>

        {{-- Invoices --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-orange-600"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5L6 18.75V3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25h7.5M8.25 12h7.5"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $invoices->count() }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Recent Invoices</p>
            @if($outstandingBalance > 0)
                <p class="mt-0.5 text-xs font-semibold text-red-500">₦{{ number_format($outstandingBalance, 0) }} due</p>
            @else
                <p class="mt-0.5 text-xs text-emerald-600">No outstanding balance</p>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         RESULTS TABLE
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($results->count())
    <section id="results-section" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    {{ $term ? $term->name.' Results' : 'Current Term Results' }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $results->count() }} subject{{ $results->count() !== 1 ? 's' : '' }} — approved &amp; verified</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Verified
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Exam</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">First Test</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Second Test</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Position</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Grade</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($results->sortBy('subject.name') as $result)
                    @php
                        $grade = $result->grade ?? '';
                        $badgeClass = $gradeBadge($grade);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $result->subject?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center text-slate-600">{{ $result->exam_score ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center text-slate-600">{{ $result->ca1_score ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center text-slate-600">{{ $result->ca2_score ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-base font-bold text-slate-900">{{ $result->total_score ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center text-slate-500">{{ $result->position_in_subject ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($grade)
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $badgeClass }}">{{ $grade }}</span>
                            @else
                            <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-slate-500">{{ $result->teacher_remark ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @php
                    $termTotal = $results->sum('total_score');
                    $termAvg   = $results->count() > 0 ? round($results->avg('total_score'), 2) : 0;
                @endphp
                <tfoot class="bg-slate-50 text-sm font-semibold text-slate-700 border-t border-slate-200">
                    <tr>
                        <td class="px-6 py-2.5" colspan="4">Total Score</td>
                        <td class="px-4 py-2.5 text-center">{{ number_format($termTotal, 1) }}</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2 text-slate-500 text-xs font-normal" colspan="4">Mark Average</td>
                        <td class="px-4 py-2 text-center font-semibold text-indigo-700">{{ $termAvg }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{-- Grade Key --}}
        <div class="px-6 py-3 border-t border-slate-100 text-xs text-slate-400 flex flex-wrap gap-4">
            <span class="font-semibold text-slate-500">Grade Key:</span>
            <span><span class="font-bold text-emerald-600">A</span> 70–100 Excellent</span>
            <span><span class="font-bold text-blue-600">B</span> 60–69 Very Good</span>
            <span><span class="font-bold text-indigo-600">C</span> 50–59 Good</span>
            <span><span class="font-bold text-amber-600">D</span> 40–49 Pass</span>
            <span><span class="font-bold text-red-600">E</span> 0–39 Fail</span>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         REPORT CARDS
    ══════════════════════════════════════════════════════════════════════ --}}
    @if(isset($reportCards) && $reportCards->count())
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Report Cards</h2>
            <p class="text-xs text-slate-400 mt-0.5">Your published terminal report cards</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($reportCards as $rc)
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <div class="text-sm font-semibold text-slate-800">
                        {{ $rc->term?->name ?? 'Term' }} — {{ $rc->term?->session?->name ?? '' }}
                    </div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        Average: <strong>{{ number_format($rc->average_score, 2) }}</strong> &nbsp;·&nbsp;
                        Position: <strong>{{ $rc->position_in_class }}/{{ $rc->class_size }}</strong> &nbsp;·&nbsp;
                        {{ $rc->total_subjects }} subjects
                    </div>
                </div>
                <a href="{{ route('portal.results.report-card', $rc->term_id) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                    View Report Card
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         TIMETABLE
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($timetable->count())
    <section id="timetable-section" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Class Timetable</h2>
                <p class="text-xs text-slate-400 mt-0.5">Your weekly class schedule</p>
            </div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5 text-slate-400"><circle cx="12" cy="12" r="8.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v4.5l3 1.5"/></svg>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($timetableSorted as $day => $slots)
            <div class="px-6 py-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em]" style="color:{{ $primaryColor }}">{{ $day }}</p>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($slots->sortBy('start_time') as $slot)
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold"
                             style="background:{{ $primaryColor }}1a;color:{{ $primaryColor }}">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($slot->subject?->name ?? '?', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $slot->subject?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                            </p>
                            @if($slot->teacher?->user)
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $slot->teacher->user->full_name }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         INVOICES / FEES
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($invoices->count())
    <section id="invoices-section" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Fee Invoices</h2>
                <p class="text-xs text-slate-400 mt-0.5">Recent {{ $invoices->count() }} invoice(s)</p>
            </div>
            @if($outstandingBalance > 0)
                <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-bold text-red-700">
                    ₦{{ number_format($outstandingBalance, 2) }} outstanding
                </span>
            @else
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                    Fees cleared
                </span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Invoice</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">Paid</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">Balance</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $invoice)
                    @php
                        $invStatus = strtolower($invoice->status?->value ?? 'pending');
                        $invBadge = match($invStatus) {
                            'paid'     => 'bg-emerald-100 text-emerald-700',
                            'partial'  => 'bg-blue-100 text-blue-700',
                            'waived'   => 'bg-violet-100 text-violet-700',
                            'refunded' => 'bg-teal-100 text-teal-700',
                            'overdue'  => 'bg-red-100 text-red-700',
                            default    => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="font-semibold text-slate-900">{{ $invoice->reference ?? ('INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT)) }}</p>
                            @if($invoice->description)
                                <p class="mt-0.5 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($invoice->description, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right font-semibold text-slate-900">₦{{ number_format($invoice->net_amount ?? 0, 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-slate-600">₦{{ number_format($invoice->amount_paid ?? 0, 2) }}</td>
                        <td class="px-4 py-3.5 text-right {{ ($invoice->balance ?? 0) > 0 ? 'font-bold text-red-600' : 'text-emerald-600' }}">
                            ₦{{ number_format($invoice->balance ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $invBadge }}">{{ ucfirst($invStatus) }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-slate-400 text-xs">{{ $invoice->created_at?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         ATTENDANCE BREAKDOWN
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($attendance->count())
    <section id="attendance-section" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900">Attendance Breakdown</h2>
        <p class="mt-0.5 text-xs text-slate-400">{{ $totalDays }} total records this term</p>

        <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach([
                ['label' => 'Present', 'count' => $attendance->where('status', 'present')->count(), 'color' => 'emerald'],
                ['label' => 'Late',    'count' => $attendance->where('status', 'late')->count(),    'color' => 'amber'],
                ['label' => 'Absent',  'count' => $absentDays,                                      'color' => 'red'],
                ['label' => 'Excused', 'count' => $attendance->where('status', 'excused')->count(), 'color' => 'blue'],
            ] as $att)
            <div class="rounded-2xl border border-{{ $att['color'] }}-200 bg-{{ $att['color'] }}-50 px-4 py-3 text-center">
                <p class="text-2xl font-extrabold text-{{ $att['color'] }}-700">{{ $att['count'] }}</p>
                <p class="mt-0.5 text-xs font-semibold text-{{ $att['color'] }}-600">{{ $att['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Progress bar --}}
        <div class="mt-5">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1.5">
                <span>Attendance Rate</span>
                <span>{{ $attendanceRate }}%</span>
            </div>
            <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100">
                @php
                    $barColor = $attendanceRate >= 90 ? '#10b981' : ($attendanceRate >= 75 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="h-3 rounded-full transition-all duration-700"
                     style="width:{{ $attendanceRate }}%;background:{{ $barColor }}"></div>
            </div>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         TESTIMONIAL FORM
    ══════════════════════════════════════════════════════════════════════ --}}
    <section id="student-testimonial-form" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $testimonialsFormTitle ?: 'Share Your Testimonial' }}</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Your feedback helps improve the school. All submissions are reviewed before publishing.</p>
                </div>
                <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Admin Approval Required</span>
            </div>
        </div>

        <div class="p-6">
            @if(session('testimonial_success'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-sm font-medium text-emerald-700">{{ session('testimonial_success') }}</p>
                </div>
            @endif

            @if($errors->has('testimonial_form'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 h-4 w-4 shrink-0 text-red-500"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/></svg>
                    <p class="text-sm font-medium text-red-700">{{ $errors->first('testimonial_form') }}</p>
                </div>
            @endif

            <form action="{{ route('student.testimonials.submit') }}" method="POST" class="grid gap-4 md:grid-cols-2">
                @csrf
                <input type="hidden" name="started_at" value="{{ old('started_at', $testimonialFormStartedAt) }}">
                <div class="hidden" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Student Name</label>
                    <input type="text" value="{{ $student->full_name }}" readonly
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>

                <div>
                    <label for="testimonial-role-title" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $testimonialsFormRoleLabel ?: 'Role or Context' }}</label>
                    <input id="testimonial-role-title" type="text" name="role_title" maxlength="140"
                           value="{{ old('role_title', 'Student') }}"
                           placeholder="{{ $testimonialsFormRolePlaceholder ?: 'Student' }}"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-[{{ $primaryColor }}] focus:outline-none focus:ring-2 focus:ring-[{{ $primaryColor }}]/10">
                    @error('role_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="testimonial-rating" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $testimonialsFormRatingLabel ?: 'Rating' }}</label>
                    <select id="testimonial-rating" name="rating" required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-[{{ $primaryColor }}] focus:outline-none focus:ring-2 focus:ring-[{{ $primaryColor }}]/10">
                        @foreach([5,4,3,2,1] as $rating)
                            <option value="{{ $rating }}" {{ (int) old('rating', 5) === $rating ? 'selected' : '' }}>
                                {{ $rating }} / 5
                                {{ match($rating) { 5 => '— Excellent', 4 => '— Very Good', 3 => '— Good', 2 => '— Fair', 1 => '— Poor' } }}
                            </option>
                        @endforeach
                    </select>
                    @error('rating')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="testimonial-message" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $testimonialsFormMessageLabel ?: 'Your Testimonial' }}</label>
                    <textarea id="testimonial-message" name="message" rows="5" required minlength="20" maxlength="1200"
                              placeholder="{{ $testimonialsFormMessagePlaceholder ?: 'Write your experience with the school...' }}"
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[{{ $primaryColor }}] focus:outline-none focus:ring-2 focus:ring-[{{ $primaryColor }}]/10">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-slate-400">Links are not allowed. All testimonials are reviewed before publication.</p>
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5"
                            style="background:{{ $primaryColor }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        {{ $testimonialsFormSubmitText ?: 'Submit Testimonial' }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         TESTIMONIAL SUBMISSION HISTORY
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($studentTestimonials->count())
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Your Testimonial Submissions</h2>
            <p class="mt-0.5 text-xs text-slate-400">Track the review status of your submissions</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Rating</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Message</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($studentTestimonials as $testimonial)
                    @php
                        $tStatus = strtolower((string) $testimonial->status);
                        $tBadge = $tStatus === 'approved'
                            ? 'bg-emerald-100 text-emerald-700'
                            : ($tStatus === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5 text-slate-500 text-xs">{{ $testimonial->created_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ max(1, min(5, (int) $testimonial->rating)) }}</span>
                            <span class="text-xs text-slate-400">/5</span>
                        </td>
                        <td class="px-6 py-3.5 text-slate-600">{{ \Illuminate\Support\Str::limit($testimonial->message, 110) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $tBadge }}">{{ ucfirst($tStatus) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

</div>
@endsection
