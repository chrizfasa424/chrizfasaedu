@extends('layouts.app')
@section('title', 'Student Attendance')
@section('header', 'Attendance')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Student Attendance</h1>
            <p class="text-sm text-slate-500 mt-0.5">Record daily attendance and track trends.</p>
        </div>
        <a href="{{ route('academic.attendance.history') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            Attendance Report
        </a>
    </div>

    @if(session('attendance_import_summary'))
        @php $importSummary = session('attendance_import_summary'); @endphp
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
            Attendance import summary for {{ $importSummary['month_label'] ?? 'selected month' }}:
            rows read {{ $importSummary['rows_read'] ?? 0 }},
            students matched {{ $importSummary['students_matched'] ?? 0 }},
            records written {{ $importSummary['records_written'] ?? 0 }},
            skipped weekends/public holidays {{ $importSummary['excluded_marks'] ?? 0 }},
            errors {{ $importSummary['error_count'] ?? 0 }}.
        </div>
    @endif

    @if($errors->has('attendance_import'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first('attendance_import') }}
        </div>
    @endif

    @if(session('attendance_import_errors'))
        @php $importErrors = session('attendance_import_errors'); @endphp
        @if(is_array($importErrors) && count($importErrors) > 0)
            <div class="rounded-2xl border border-red-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-red-100 bg-red-50">
                    <h2 class="text-sm font-semibold text-red-800">Attendance Import Errors</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Row</th>
                                <th class="px-4 py-2 text-left">Column</th>
                                <th class="px-4 py-2 text-left">Message</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($importErrors as $error)
                                <tr>
                                    <td class="px-4 py-2 text-slate-700">{{ $error['row'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-slate-600">{{ $error['column'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-slate-700">{{ $error['message'] ?? 'Import error' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- Filter form --}}
    <form method="GET" action="{{ route('academic.attendance.index') }}"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                    {{ $c->grade_level?->label() ?? $c->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Load</button>
    </form>

    <form method="POST" action="{{ route('academic.attendance.import') }}" enctype="multipart/form-data"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-800">Import Monthly Attendance (P/A)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Use your attendance sheet format: Student Number, Full Name, days 1-31, with P or A.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="import_class_id" id="import_class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ (string) old('import_class_id', $classId) === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->grade_level?->label() ?? $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Arm (Optional)</label>
                <select name="import_arm_id" id="import_arm_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All arms / no arm</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Month <span class="text-red-500">*</span></label>
                <input type="month" name="attendance_month" required value="{{ old('attendance_month', now()->format('Y-m')) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Session <span class="text-red-500">*</span></label>
                <select name="import_session_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ (string) old('import_session_id', $session->is_current ? $session->id : null) === (string) $session->id ? 'selected' : '' }}>
                            {{ $session->name }}{{ $session->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term <span class="text-red-500">*</span></label>
                <select name="import_term_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ (string) old('import_term_id', $term->is_current ? $term->id : null) === (string) $term->id ? 'selected' : '' }}>
                            {{ $term->name }} - {{ $term->session->name ?? '' }}{{ $term->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Attendance File <span class="text-red-500">*</span></label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-xs font-medium text-slate-600 mb-1">Public Holiday Dates (Optional)</label>
                <input type="text" name="public_holiday_dates" value="{{ old('public_holiday_dates') }}"
                    placeholder="e.g. 18, 21 or 2026-04-18, 2026-04-21"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">
                    These dates are saved for this school term and will be auto-ignored during attendance import. Weekends are ignored automatically.
                </p>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                Import Attendance Sheet
            </button>
        </div>
    </form>

    @if($classId && $students->count())

    {{-- Absence warnings --}}
    @if($warnings->count())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <span class="text-sm font-semibold text-red-700">Absence Warnings — {{ $warnings->count() }} student(s) absent 10+ times this term</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($warnings as $w)
            <span class="rounded-full bg-red-100 border border-red-200 px-3 py-1 text-xs font-medium text-red-700">
                {{ $w['student']->full_name }} ({{ $w['count'] }}x)
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stats cards --}}
    @if($stats)
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $statCards = [
                ['label' => 'Total',   'value' => $stats['total'],   'color' => 'bg-slate-50  text-slate-700',  'dot' => 'bg-slate-400'],
                ['label' => 'Present', 'value' => $stats['present'], 'color' => 'bg-green-50  text-green-700',  'dot' => 'bg-green-500'],
                ['label' => 'Absent',  'value' => $stats['absent'],  'color' => 'bg-red-50    text-red-700',    'dot' => 'bg-red-500'],
                ['label' => 'Late',    'value' => $stats['late'],    'color' => 'bg-amber-50  text-amber-700',  'dot' => 'bg-amber-500'],
                ['label' => 'Male',    'value' => $stats['male'],    'color' => 'bg-blue-50   text-blue-700',   'dot' => 'bg-blue-400'],
                ['label' => 'Female',  'value' => $stats['female'],  'color' => 'bg-pink-50   text-pink-700',   'dot' => 'bg-pink-400'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <div class="flex items-center justify-center gap-1.5 mb-1">
                <span class="h-2 w-2 rounded-full {{ $card['dot'] }}"></span>
                <span class="text-xs font-medium text-slate-500">{{ $card['label'] }}</span>
            </div>
            <div class="text-2xl font-extrabold {{ explode(' ', $card['color'])[1] }}">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Live auto-sum bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-sm flex flex-wrap gap-6 items-center text-sm">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Live Count</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-green-500"></span><span id="live-present" class="font-bold text-green-700">0</span> <span class="text-slate-400">Present</span></span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-red-500"></span><span id="live-absent" class="font-bold text-red-600">0</span> <span class="text-slate-400">Absent</span></span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span><span id="live-late" class="font-bold text-amber-600">0</span> <span class="text-slate-400">Late</span></span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span><span id="live-excused" class="font-bold text-slate-600">0</span> <span class="text-slate-400">Excused</span></span>
        <div class="ml-auto text-xs text-slate-400">Updates as you mark attendance</div>
    </div>
    @endif

    {{-- Charts row --}}
    @if($stats && $trendData)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Doughnut chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">Today's Breakdown</h3>
            <p class="text-xs text-slate-400 mb-4">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
            <div class="flex-1 flex items-center justify-center" style="min-height:200px">
                <canvas id="donutChart"></canvas>
            </div>
        </div>

        {{-- 30-day trend --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">30-Day Attendance Trend</h3>
            <p class="text-xs text-slate-400 mb-4">Present + Late vs Absent</p>
            <canvas id="trendChart" style="height:200px"></canvas>
        </div>
    </div>
    @endif

    {{-- Attendance form --}}
    <form method="POST" action="{{ route('academic.attendance.store') }}" id="attendance-form">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">
                        {{ $classes->firstWhere('id', $classId)?->grade_level?->label() ?? $classes->firstWhere('id', $classId)?->name }}
                        — {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $students->count() }} student(s)</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" onclick="markAll('present')"
                        class="rounded-lg border border-green-300 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100">All Present</button>
                    <button type="button" onclick="markAll('absent')"
                        class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">All Absent</button>
                    <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Save Attendance</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left w-8">#</th>
                            <th class="px-5 py-3 text-left">Student</th>
                            <th class="px-4 py-3 text-left">Adm. No.</th>
                            <th class="px-4 py-3 text-center">Gender</th>
                            <th class="px-4 py-3 text-center">Present</th>
                            <th class="px-4 py-3 text-center">Absent</th>
                            <th class="px-4 py-3 text-center">Late</th>
                            <th class="px-4 py-3 text-center">Excused</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="attendance-tbody">
                        @foreach($students as $i => $student)
                        @php $currentStatus = $attendances[$student->id] ?? 'present'; @endphp
                        <tr class="hover:bg-slate-50 attendance-row {{ $warnings->firstWhere('student.id', $student->id) ? 'bg-red-50/50' : '' }}">
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ $student->full_name }}</div>
                                @if($warnings->firstWhere('student.id', $student->id))
                                    @php $w = $warnings->firstWhere('student.id', $student->id); @endphp
                                    <div class="text-xs text-red-500 mt-0.5">⚠ {{ $w['count'] }} absences this term</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $student->admission_number }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }} font-medium capitalize">
                                    {{ $student->gender }}
                                </span>
                            </td>
                            <input type="hidden" name="attendance[{{ $i }}][student_id]" value="{{ $student->id }}">
                            @foreach(['present' => 'green', 'absent' => 'red', 'late' => 'amber', 'excused' => 'slate'] as $status => $color)
                            <td class="px-4 py-3 text-center">
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendance[{{ $i }}][status]" value="{{ $status }}"
                                        class="att-radio sr-only"
                                        data-status="{{ $status }}"
                                        {{ $currentStatus === $status ? 'checked' : '' }}
                                        onchange="updateLiveCount()">
                                    <span class="att-btn inline-flex h-7 w-7 items-center justify-center rounded-full border-2 transition-all
                                        {{ $currentStatus === $status
                                            ? "border-{$color}-500 bg-{$color}-100 text-{$color}-600"
                                            : 'border-slate-200 bg-white text-slate-300' }}">
                                        @if($status === 'present')
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @elseif($status === 'absent')
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @elseif($status === 'late')
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                        @endif
                                    </span>
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="rounded-lg bg-green-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Save Attendance
                </button>
            </div>
        </div>
    </form>

    @elseif($classId && $students->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No active students in this class.
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        Select a class and date to record attendance.
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@php
    $classArms = $classes
        ->mapWithKeys(fn ($class) => [
            (string) $class->id => $class->arms
                ->map(fn ($arm) => ['id' => $arm->id, 'name' => $arm->name])
                ->values()
                ->all(),
        ])
        ->all();
@endphp
<script>
// ── Attendance radio button toggle UI ─────────────────────────────────────────
const classArms = @json($classArms);
const initialImportArmId = @json((string) old('import_arm_id', ''));
let importArmInitialized = false;

document.addEventListener('DOMContentLoaded', function () {
    const importClassSelect = document.getElementById('import_class_id');
    const importArmSelect = document.getElementById('import_arm_id');

    const refreshImportArms = () => {
        if (!importClassSelect || !importArmSelect) return;

        const selectedClassId = String(importClassSelect.value || '');
        const arms = classArms[selectedClassId] || [];
        const previousArmValue = String(importArmSelect.value || '');
        const preferredArmValue = importArmInitialized ? previousArmValue : initialImportArmId;

        importArmSelect.innerHTML = '';

        const blankOption = document.createElement('option');
        blankOption.value = '';
        blankOption.textContent = 'All arms / no arm';
        importArmSelect.appendChild(blankOption);

        arms.forEach((arm) => {
            const option = document.createElement('option');
            option.value = String(arm.id);
            option.textContent = arm.name;
            if (String(arm.id) === preferredArmValue) {
                option.selected = true;
            }
            importArmSelect.appendChild(option);
        });

        importArmInitialized = true;
    };

    if (importClassSelect && importArmSelect) {
        importClassSelect.addEventListener('change', refreshImportArms);
        refreshImportArms();
    }

    document.querySelectorAll('.att-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const row    = this.closest('tr');
            const radios = row.querySelectorAll('.att-radio');
            const btns   = row.querySelectorAll('.att-btn');

            const colorMap = {
                present: 'border-green-500 bg-green-100 text-green-600',
                absent:  'border-red-500 bg-red-100 text-red-600',
                late:    'border-amber-500 bg-amber-100 text-amber-600',
                excused: 'border-slate-500 bg-slate-100 text-slate-600',
            };
            const inactive = 'border-slate-200 bg-white text-slate-300';

            radios.forEach((r, idx) => {
                btns[idx].className = btns[idx].className
                    .replace(/border-\S+|bg-\S+|text-\S+/g, '').trim();
                const classes = 'att-btn inline-flex h-7 w-7 items-center justify-center rounded-full border-2 transition-all ';
                btns[idx].className = classes + (r.checked ? colorMap[r.dataset.status] : inactive);
            });

            updateLiveCount();
        });
    });

    updateLiveCount();
});

function updateLiveCount() {
    const counts = { present: 0, absent: 0, late: 0, excused: 0 };
    document.querySelectorAll('.att-radio:checked').forEach(r => {
        if (counts[r.dataset.status] !== undefined) counts[r.dataset.status]++;
    });
    const livePresent = document.getElementById('live-present');
    const liveAbsent = document.getElementById('live-absent');
    const liveLate = document.getElementById('live-late');
    const liveExcused = document.getElementById('live-excused');

    if (!livePresent || !liveAbsent || !liveLate || !liveExcused) {
        return;
    }

    livePresent.textContent = counts.present;
    liveAbsent.textContent = counts.absent;
    liveLate.textContent = counts.late;
    liveExcused.textContent = counts.excused;
}

function markAll(status) {
    document.querySelectorAll(`.att-radio[data-status="${status}"]`).forEach(r => {
        r.checked = true;
        r.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// ── Charts ────────────────────────────────────────────────────────────────────
@if($stats && $trendData)
const chartDefaults = {
    font: { family: "'Inter', system-ui, sans-serif" },
};
Chart.defaults.font.family = chartDefaults.font.family;

// Doughnut
const donutCtx = document.getElementById('donutChart');
if (donutCtx) {
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late', 'Excused'],
            datasets: [{
                data: [{{ $stats['present'] }}, {{ $stats['absent'] }}, {{ $stats['late'] }}, {{ $stats['excused'] }}],
                backgroundColor: ['#22c55e','#ef4444','#f59e0b','#94a3b8'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, boxWidth: 10, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} student(s)`,
                    },
                },
            },
        },
    });
}

// 30-day trend
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const labels  = @json($trendData['trendLabels']);
    const present = @json($trendData['trendPresent']);
    const absent  = @json($trendData['trendAbsent']);

    const gradientGreen = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
    gradientGreen.addColorStop(0, 'rgba(34,197,94,0.35)');
    gradientGreen.addColorStop(1, 'rgba(34,197,94,0)');

    const gradientRed = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
    gradientRed.addColorStop(0, 'rgba(239,68,68,0.25)');
    gradientRed.addColorStop(1, 'rgba(239,68,68,0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Present',
                    data: present,
                    borderColor: '#22c55e',
                    backgroundColor: gradientGreen,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#22c55e',
                },
                {
                    label: 'Absent',
                    data: absent,
                    borderColor: '#ef4444',
                    backgroundColor: gradientRed,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#ef4444',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { boxWidth: 10, font: { size: 11 } } },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        title: items => items[0].label,
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 10, font: { size: 10 }, color: '#94a3b8',
                        maxRotation: 0 },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 10 }, color: '#94a3b8', precision: 0 },
                },
            },
        },
    });
}
@endif
</script>
@endpush
