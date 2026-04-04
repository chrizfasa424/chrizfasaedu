@extends('layouts.app')
@section('title', 'Report Card – '.$student->full_name)
@section('header', 'Report Card')

@section('content')
<div class="space-y-6 max-w-4xl">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('examination.results.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Results</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-medium text-slate-800">{{ $student->full_name }}</span>
        </div>
        <a href="{{ route('examination.results.report-card.download', [$student, $reportCard->term_id]) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Download PDF
        </a>
    </div>

    {{-- Header --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
        @if($school->logo)
        <img src="{{ asset('storage/'.$school->logo) }}" class="h-16 mx-auto mb-3" alt="">
        @endif
        <h1 class="text-xl font-bold text-slate-900">{{ $school->name }}</h1>
        <p class="text-sm text-slate-500">{{ $school->address ?? '' }}</p>
        <div class="mt-3 inline-block rounded-full bg-indigo-50 px-4 py-1 text-sm font-semibold text-indigo-700">
            STUDENT REPORT CARD
        </div>
    </div>

    {{-- Student Info + Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3">Student Information</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div><dt class="text-xs text-slate-500">Name</dt><dd class="font-semibold text-slate-800">{{ $student->full_name }}</dd></div>
                <div><dt class="text-xs text-slate-500">Admission No.</dt><dd class="font-semibold text-slate-800">{{ $student->admission_number }}</dd></div>
                <div><dt class="text-xs text-slate-500">Class</dt><dd class="font-semibold text-slate-800">{{ $student->schoolClass?->name }}</dd></div>
                <div><dt class="text-xs text-slate-500">Gender</dt><dd class="font-semibold text-slate-800 capitalize">{{ $student->gender }}</dd></div>
            </dl>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3">Term Summary</h3>
            <div class="text-3xl font-bold text-indigo-600">{{ number_format($reportCard->average_score, 1) }}<span class="text-lg text-slate-400">/100</span></div>
            <p class="text-xs text-slate-500 mt-1">Average Score</p>
            <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-lg bg-slate-50 p-2"><span class="text-slate-500 block">Position</span><span class="font-bold text-slate-800">{{ $reportCard->position_in_class }}/{{ $reportCard->class_size }}</span></div>
                <div class="rounded-lg bg-slate-50 p-2"><span class="text-slate-500 block">Subjects</span><span class="font-bold text-slate-800">{{ $reportCard->total_subjects }}</span></div>
                <div class="rounded-lg bg-green-50 p-2"><span class="text-green-600 block">Passed</span><span class="font-bold text-green-700">{{ $reportCard->subjects_passed }}</span></div>
                <div class="rounded-lg bg-red-50 p-2"><span class="text-red-500 block">Failed</span><span class="font-bold text-red-600">{{ $reportCard->subjects_failed }}</span></div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Subject Results</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Subject</th>
                    <th class="px-4 py-2 text-center">CA1</th>
                    <th class="px-4 py-2 text-center">CA2</th>
                    <th class="px-4 py-2 text-center">CA3</th>
                    <th class="px-4 py-2 text-center">Exam</th>
                    <th class="px-4 py-2 text-center">Total</th>
                    <th class="px-4 py-2 text-center">Grade</th>
                    <th class="px-4 py-2 text-center">Position</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($results->sortBy('subject.name') as $result)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-2 font-medium text-slate-800">{{ $result->subject?->name }}</td>
                    <td class="px-4 py-2 text-center text-slate-600">{{ $result->ca1_score ?? '—' }}</td>
                    <td class="px-4 py-2 text-center text-slate-600">{{ $result->ca2_score ?? '—' }}</td>
                    <td class="px-4 py-2 text-center text-slate-600">{{ $result->ca3_score ?? '—' }}</td>
                    <td class="px-4 py-2 text-center text-slate-600">{{ $result->exam_score ?? '—' }}</td>
                    <td class="px-4 py-2 text-center font-semibold text-slate-800">{{ $result->total_score }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold
                            @if(in_array($result->grade, ['A1','B2','B3'])) bg-green-100 text-green-700
                            @elseif(in_array($result->grade, ['C4','C5','C6'])) bg-blue-100 text-blue-700
                            @elseif(in_array($result->grade, ['D7','E8'])) bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $result->grade ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center text-slate-600">{{ $result->position_in_subject ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 text-sm font-semibold text-slate-700">
                <tr>
                    <td class="px-5 py-2" colspan="5">Total Score</td>
                    <td class="px-4 py-2 text-center">{{ $reportCard->total_score }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
