@extends('layouts.app')
@section('title', 'Results')
@section('header', 'Examination Results')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Examination Results</h1>
            <p class="text-sm text-slate-500 mt-0.5">View and manage student results.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('examination.results.enter-scores') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Enter Scores
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('examination.results.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Term ID</label>
            <input type="number" name="term_id" value="{{ request('term_id') }}" placeholder="Term ID"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-28">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">View Results</button>
        @if(request()->hasAny(['class_id','term_id']))
        <a href="{{ route('examination.results.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    {{-- Compute & Approve Actions --}}
    @if(request('class_id') && request('term_id') && $results->count())
    <form method="POST" action="{{ route('examination.results.compute') }}" class="flex gap-2">
        @csrf
        <input type="hidden" name="class_id" value="{{ request('class_id') }}">
        <input type="hidden" name="term_id" value="{{ request('term_id') }}">
        <button type="submit" onclick="return confirm('Compute positions and generate report cards?')"
            class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
            Compute Positions & Report Cards
        </button>
    </form>
    @endif

    @if($results->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">{{ $results->count() }} Student(s)</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Student</th>
                    <th class="px-5 py-3 text-left">Subjects</th>
                    <th class="px-5 py-3 text-left">Total</th>
                    <th class="px-5 py-3 text-left">Average</th>
                    <th class="px-5 py-3 text-left">Report Card</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($results as $studentId => $studentResults)
                @php $student = $studentResults->first()->student; $avg = $studentResults->avg('total_score'); @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $student?->full_name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $studentResults->count() }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ $studentResults->sum('total_score') }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($avg, 1) }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('examination.results.report-card', [$studentId, request('term_id')]) }}"
                            class="text-xs text-indigo-600 hover:underline font-medium">View Card</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(request('class_id') && request('term_id'))
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No results found for the selected class and term.
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        Select a class and term to view results.
    </div>
    @endif

</div>
@endsection
