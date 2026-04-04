@extends('layouts.app')
@section('title', 'Teacher Portal')
@section('header', 'Teacher Portal')

@php
    $primaryColor   = trim((string) ($publicPage['primary_color']   ?? '#2D1D5C'));
    $secondaryColor = trim((string) ($publicPage['secondary_color'] ?? '#DFE753'));

    $dayOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $timetableSorted = collect($dayOrder)
        ->mapWithKeys(fn ($day) => [$day => $timetable->get($day, collect())])
        ->filter(fn ($slots) => $slots->count() > 0);

    $subjectCount = $timetable->collapse()->pluck('subject_id')->unique()->count();
    $classCount   = $timetable->collapse()->pluck('class_id')->unique()->count();
@endphp

@section('content')
<div class="space-y-6">

    {{-- ── WELCOME HERO ─────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl"
         style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, #3a2872 60%, #1a0f3a 100%);">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 80% at 80% 50%,rgba(223,231,83,0.10) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.05) 1px,transparent 1px);background-size:24px 24px;pointer-events:none;"></div>

        <div class="relative z-10 flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8 lg:p-10">
            <div class="flex items-center gap-5">
                @if($staff?->photo)
                    <img src="{{ asset('storage/' . ltrim($staff->photo, '/')) }}"
                         alt="{{ $staff->full_name }}"
                         class="h-20 w-20 rounded-2xl border-2 border-white/20 object-cover shadow-xl">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-2 border-white/15 text-3xl font-extrabold shadow-xl"
                         style="background:{{ $secondaryColor }};color:{{ $primaryColor }}">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->first_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/50">Teacher Portal</p>
                    <h1 class="mt-1 text-2xl font-extrabold leading-tight text-white sm:text-3xl">
                        {{ auth()->user()->full_name }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        @if($staff?->designation)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold text-white/80 backdrop-blur">
                            {{ $staff->designation }}
                        </span>
                        @endif
                        @if($staff?->department)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/8 px-3 py-1 text-xs text-white/60">
                            {{ $staff->department }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 sm:flex-col sm:items-end">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">My Classes</p>
                    <p class="mt-0.5 text-2xl font-extrabold" style="color:{{ $secondaryColor }}">{{ $classCount }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Students</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-white">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h11.25v16.5H6.75A2.25 2.25 0 0 0 4.5 21V5.25Z"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $subjectCount }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Subjects</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-violet-600"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $totalStudents }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Students</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M7.5 16.5v-4.5M12 16.5V8.25M16.5 16.5v-6.75"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $recentResults->count() }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Results Entered</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $pendingApproval > 0 ? 'bg-amber-50' : 'bg-slate-50' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 {{ $pendingApproval > 0 ? 'text-amber-600' : 'text-slate-400' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold {{ $pendingApproval > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingApproval }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Pending Approval</p>
        </div>
    </div>

    {{-- ── QUICK ACTIONS ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('examination.results.enter-scores') }}"
           class="group flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[{{ $primaryColor }}] hover:shadow-md">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition group-hover:opacity-80"
                  style="background:{{ $primaryColor }}1a;color:{{ $primaryColor }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.25 19.04 4.5 19.5l.46-3.75L16.862 4.487Z"/></svg>
            </span>
            <div>
                <p class="text-sm font-bold text-slate-900">Enter Scores</p>
                <p class="text-xs text-slate-400">Add or update student results</p>
            </div>
        </a>

        <a href="{{ route('examination.results.index') }}"
           class="group flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[{{ $primaryColor }}] hover:shadow-md">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition group-hover:opacity-80"
                  style="background:{{ $primaryColor }}1a;color:{{ $primaryColor }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M7.5 16.5v-4.5M12 16.5V8.25M16.5 16.5v-6.75"/></svg>
            </span>
            <div>
                <p class="text-sm font-bold text-slate-900">View Results</p>
                <p class="text-xs text-slate-400">Review submitted results</p>
            </div>
        </a>

        <a href="{{ route('academic.attendance.index') }}"
           class="group flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[{{ $primaryColor }}] hover:shadow-md">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition group-hover:opacity-80"
                  style="background:{{ $primaryColor }}1a;color:{{ $primaryColor }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h10.5M9 12h10.5M9 17.25h10.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 6.75 1.5 1.5 3-3M4.5 12l1.5 1.5 3-3M4.5 17.25l1.5 1.5 3-3"/></svg>
            </span>
            <div>
                <p class="text-sm font-bold text-slate-900">Attendance</p>
                <p class="text-xs text-slate-400">Mark and view records</p>
            </div>
        </a>
    </div>

    {{-- ── MY TIMETABLE ─────────────────────────────────────────────────── --}}
    @if($timetableSorted->count())
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">My Teaching Timetable</h2>
                <p class="mt-0.5 text-xs text-slate-400">{{ $subjectCount }} subject(s) across {{ $classCount }} class(es)</p>
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
                            @if($slot->schoolClass)
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $slot->schoolClass->name }}</p>
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

    {{-- ── RECENT RESULTS ───────────────────────────────────────────────── --}}
    @if($recentResults->count())
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Recent Results in My Classes</h2>
                <p class="mt-0.5 text-xs text-slate-400">Latest {{ $recentResults->count() }} results</p>
            </div>
            @if($pendingApproval > 0)
            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                {{ $pendingApproval }} pending approval
            </span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Student</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Grade</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentResults as $result)
                    @php
                        $grade = $result->grade ?? '';
                        $gradeBadge = match ($grade) {
                            'A1' => 'bg-emerald-100 text-emerald-700',
                            'B2' => 'bg-green-100 text-green-700',
                            'B3' => 'bg-teal-100 text-teal-700',
                            'C4' => 'bg-blue-100 text-blue-700',
                            'C5' => 'bg-indigo-100 text-indigo-700',
                            'C6' => 'bg-violet-100 text-violet-700',
                            'D7' => 'bg-amber-100 text-amber-700',
                            'E8' => 'bg-orange-100 text-orange-700',
                            'F9' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $result->student?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-slate-600">{{ $result->subject?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-slate-900">{{ $result->total_score ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($grade)
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $gradeBadge }}">{{ $grade }}</span>
                            @else
                            <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($result->is_approved)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span> Pending
                                </span>
                            @endif
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
