@extends('layouts.app')
@section('title', 'My Timetable')
@section('header', 'My Timetable')

@php
    $studentClassLabel = $student->schoolClass?->grade_level?->label()
        ?? $student->schoolClass?->name
        ?? 'Not Assigned';
    $studentArmLabel = $student->arm?->name ? ('Arm ' . $student->arm->name) : null;
@endphp

@section('content')
<div class="w-full space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('student.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Dashboard</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">My Timetable</span>
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Weekly Timetable</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $studentClassLabel }}@if($studentArmLabel) - {{ $studentArmLabel }}@endif
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    Session: {{ $session?->name ?? 'Not Set' }} |
                    Term: {{ $term?->name ?? 'Not Set' }}
                </p>
            </div>
            <form method="GET" action="{{ route('portal.timetable') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Day</label>
                    <select name="day" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800">
                        <option value="all" @selected($selectedDay === 'all')>All Days</option>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <option value="{{ $day }}" @selected($selectedDay === $day)>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('portal.timetable') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </form>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Periods</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $periodCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active Days</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $activeDaysCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Class</p>
            <p class="mt-2 text-base font-bold text-slate-900">{{ $studentClassLabel }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Arm</p>
            <p class="mt-2 text-base font-bold text-slate-900">{{ $studentArmLabel ?? 'Class Wide' }}</p>
        </div>
    </section>

    @if($timetable->isEmpty())
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
            <p class="text-lg font-bold text-slate-800">No timetable published yet</p>
            <p class="mt-2 text-sm text-slate-500">
                Your academic team has not published timetable entries for the selected scope.
            </p>
        </section>
    @else
        <section class="space-y-4">
            @foreach($timetable as $day => $slots)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <h2 class="text-base font-bold text-slate-900">{{ $day }}</h2>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $slots->count() }} period(s)</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-6 py-3 text-left">Time</th>
                                    <th class="px-6 py-3 text-left">Subject</th>
                                    <th class="px-6 py-3 text-left">Teacher</th>
                                    <th class="px-6 py-3 text-left">Room</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($slots as $slot)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-6 py-3.5 font-semibold text-slate-800">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                        </td>
                                        <td class="px-6 py-3.5 text-slate-700">{{ $slot->subject?->name ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-slate-600">
                                            @if($slot->teacher)
                                                <div class="font-medium text-slate-700">{{ $slot->teacher->full_name }}</div>
                                                @if(!empty($slot->teacher->designation))
                                                    <div class="text-xs text-slate-400">{{ $slot->teacher->designation }}</div>
                                                @endif
                                            @else
                                                <span>Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-slate-600">{{ $slot->room ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</div>
@endsection
