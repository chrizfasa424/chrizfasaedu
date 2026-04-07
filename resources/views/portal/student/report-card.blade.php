@extends('layouts.app')
@section('title', 'Report Card')
@section('header', 'Report Card')

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Nav --}}
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('student.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">← Dashboard</a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Print
            </button>
            <a href="{{ route('portal.results.report-card.download', $term->id) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download PDF
            </a>
            <a href="{{ route('portal.results.download-excel', $term->id) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download Excel
            </a>
        </div>
    </div>

    {{-- School Header --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
        @if($school->logo)
        <img src="{{ asset('storage/'.$school->logo) }}" class="h-16 mx-auto mb-3" alt="">
        @endif
        <h1 class="text-2xl font-bold text-slate-900">{{ $school->name }}</h1>
        @if($school->address)
        <p class="text-sm text-slate-500 mt-1">{{ $school->address }}</p>
        @endif
        <div class="mt-3 inline-block rounded-full bg-indigo-50 px-5 py-1 text-sm font-bold tracking-wide text-indigo-700 uppercase">
            Terminal Report Card
        </div>
    </div>

    {{-- Student Info + Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3">Student Information</h3>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <div>
                    <dt class="text-xs text-slate-400">Full Name</dt>
                    <dd class="font-semibold text-slate-800">{{ $student->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Reg No</dt>
                    <dd class="font-semibold text-slate-800">{{ $student->registration_number ?? $student->admission_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Class</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ $student->schoolClass?->name }}{{ $student->arm ? ' '.$student->arm->name : '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Gender</dt>
                    <dd class="font-semibold text-slate-800 capitalize">{{ $student->gender }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Academic Year</dt>
                    <dd class="font-semibold text-slate-800">{{ $term?->session?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Exam</dt>
                    <dd class="font-semibold text-slate-800">{{ $term?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Position in Class</dt>
                    <dd class="font-semibold text-slate-800">
                        @if($reportCard->position_in_class)
                            @php
                                $pos = $reportCard->position_in_class;
                                $suffix = match(true) {
                                    $pos % 100 >= 11 && $pos % 100 <= 13 => 'th',
                                    $pos % 10 === 1 => 'st',
                                    $pos % 10 === 2 => 'nd',
                                    $pos % 10 === 3 => 'rd',
                                    default => 'th',
                                };
                            @endphp
                            {{ $pos }}{{ $suffix }}
                        @else —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Attendance</dt>
                    <dd class="font-semibold text-slate-800">{{ $reportCard->attendance_present ?? 0 }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3">Term Summary</h3>
            <div class="text-center mb-4">
                <div class="text-4xl font-extrabold text-indigo-600">{{ number_format($reportCard->average_score, 2) }}</div>
                <p class="text-xs text-slate-500 mt-1">Mark Average</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs text-center">
                <div class="rounded-lg bg-slate-50 p-3">
                    <div class="font-bold text-slate-800 text-lg">{{ $reportCard->position_in_class ?? '—' }}</div>
                    <div class="text-slate-400">Position / {{ $reportCard->class_size }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <div class="font-bold text-slate-800 text-lg">{{ $reportCard->total_subjects }}</div>
                    <div class="text-slate-400">Subjects</div>
                </div>
                <div class="rounded-lg bg-green-50 p-3">
                    <div class="font-bold text-green-700 text-lg">{{ $reportCard->subjects_passed }}</div>
                    <div class="text-green-500">Passed</div>
                </div>
                <div class="rounded-lg bg-red-50 p-3">
                    <div class="font-bold text-red-600 text-lg">{{ $reportCard->subjects_failed }}</div>
                    <div class="text-red-400">Failed</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subject Results --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Subject Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Subject</th>
                        <th class="px-4 py-3 text-center">Exam</th>
                        <th class="px-4 py-3 text-center">First Test</th>
                        <th class="px-4 py-3 text-center">Second Test</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Position</th>
                        <th class="px-4 py-3 text-center">Grade</th>
                        <th class="px-4 py-3 text-center">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($results->sortBy('subject.name') as $result)
                    @php
                        $gradeColor = match($result->grade) {
                            'A' => 'bg-emerald-100 text-emerald-700',
                            'B' => 'bg-blue-100 text-blue-700',
                            'C' => 'bg-indigo-100 text-indigo-700',
                            'D' => 'bg-amber-100 text-amber-700',
                            default => 'bg-red-100 text-red-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-2.5 font-medium text-slate-800">{{ $result->subject?->name }}</td>
                        <td class="px-4 py-2.5 text-center text-slate-600">{{ $result->exam_score ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center text-slate-600">{{ $result->ca1_score ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center text-slate-600">{{ $result->ca2_score ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-slate-800">{{ $result->total_score }}</td>
                        <td class="px-4 py-2.5 text-center text-slate-500">{{ $result->position_in_subject ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $gradeColor }}">
                                {{ $result->grade ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center text-slate-500 text-xs">{{ $result->teacher_remark ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 text-sm font-semibold">
                    <tr>
                        <td class="px-5 py-2.5 text-slate-700" colspan="4">Total</td>
                        <td class="px-4 py-2.5 text-center text-slate-800">{{ $reportCard->total_score }}</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="px-5 py-2 text-slate-500 text-xs font-normal" colspan="4">Mark Average</td>
                        <td class="px-4 py-2 text-center font-semibold text-indigo-700 text-xs">{{ number_format($reportCard->average_score, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Remarks --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-2">Class Teacher Remarks</h3>
            <p class="text-sm text-slate-700 min-h-[3rem]">{{ $reportCard->class_teacher_remark ?? '—' }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-2">Principal Remarks</h3>
            <p class="text-sm text-slate-700 min-h-[3rem]">{{ $reportCard->principal_remark ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- Grade Key --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs text-slate-500">
        <span class="font-semibold text-slate-600">Interpretation of Grades: </span>
        70–100 = 5.0 [A] &nbsp;|&nbsp; 60–69 = 4.5 [B] &nbsp;|&nbsp; 50–59 = 4.0 [C] &nbsp;|&nbsp; 40–49 = 3.5 [D] &nbsp;|&nbsp; 0–39 = 3.0 [E]
    </div>

</div>
@push('styles')
<style>
@media print {
    /* Hide nav, sidebar, header, footer — only show the report card content */
    .no-print,
    nav, aside, header, footer,
    [class*="sidebar"], [class*="topbar"],
    [id*="sidebar"], [id*="topbar"] {
        display: none !important;
    }

    body { background: #fff !important; font-size: 11pt; }

    /* Remove card shadows and borders for print */
    .rounded-2xl, .shadow-sm { box-shadow: none !important; }

    /* Ensure full width */
    .max-w-4xl { max-width: 100% !important; }

    /* Grade badges need background for print */
    .rounded-full { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* Grade key and remarks keep backgrounds */
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* Page break control */
    table { page-break-inside: avoid; }

    @page {
        size: A4;
        margin: 12mm 14mm;
    }
}
</style>
@endpush
@endsection
