@extends('layouts.app')
@section('title', 'Import Results')
@section('header', 'Import Results')

@section('content')
<div class="space-y-6 max-w-2xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('examination.results.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Results</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">Import Results</span>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- Instructions --}}
    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">How to import results</h3>
        <ol class="text-sm text-blue-700 space-y-1.5 list-decimal list-inside">
            <li>Download the CSV template — it already contains real admission numbers from your school.</li>
            <li>
                In the <strong>registration_number</strong> column, use the student's
                <strong>Admission Number</strong> exactly as shown in the system
                (e.g. <code class="bg-blue-100 px-1 rounded font-mono">ADM/2026/0001</code>).
            </li>
            <li>In the <strong>subject</strong> column, use the exact subject name as entered in the system (case-insensitive).</li>
            <li>Save as <code class="bg-blue-100 px-1 rounded">.csv</code> or <code class="bg-blue-100 px-1 rounded">.xlsx</code> — do <strong>not</strong> change the column headers.</li>
            <li>Select the class and term, then upload the file.</li>
        </ol>
        <div class="mt-3 flex items-center gap-3">
            <a href="{{ route('examination.results.template') }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-700 hover:text-blue-900">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download CSV Template (with real admission numbers)
            </a>
        </div>
    </div>

    {{-- Quick admission number lookup --}}
    @php
        $sampleStudents = \App\Models\Student::where('school_id', auth()->user()->school_id)
            ->whereNotNull('admission_number')->take(5)
            ->get(['first_name','last_name','admission_number']);
    @endphp
    @if($sampleStudents->isNotEmpty())
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h3 class="text-sm font-semibold text-amber-800 mb-2">Sample Admission Numbers (use these in your CSV)</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($sampleStudents as $s)
            <span class="rounded-lg bg-white border border-amber-200 px-3 py-1 text-xs font-mono text-amber-800">
                {{ $s->admission_number }} — {{ $s->first_name }} {{ $s->last_name }}
            </span>
            @endforeach
        </div>
        <p class="text-xs text-amber-600 mt-2">Go to <strong>Students</strong> list to see all admission numbers.</p>
    </div>
    @endif

    {{-- CSV Column Guide --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-3">CSV Columns</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Column</th>
                        <th class="px-3 py-2 text-left">Required</th>
                        <th class="px-3 py-2 text-left">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr><td class="px-3 py-2 font-mono font-medium">registration_number</td><td class="px-3 py-2 text-green-600">Yes</td><td class="px-3 py-2">Student's registration or admission number</td></tr>
                    <tr><td class="px-3 py-2 font-mono font-medium">subject</td><td class="px-3 py-2 text-green-600">Yes</td><td class="px-3 py-2">Exact subject name (case insensitive)</td></tr>
                    <tr><td class="px-3 py-2 font-mono font-medium">exam</td><td class="px-3 py-2 text-slate-400">No</td><td class="px-3 py-2">Exam score</td></tr>
                    <tr><td class="px-3 py-2 font-mono font-medium">first_test</td><td class="px-3 py-2 text-slate-400">No</td><td class="px-3 py-2">First CA / test score</td></tr>
                    <tr><td class="px-3 py-2 font-mono font-medium">second_test</td><td class="px-3 py-2 text-slate-400">No</td><td class="px-3 py-2">Second CA / test score</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upload Form --}}
    <form method="POST" action="{{ route('examination.results.import.store') }}" enctype="multipart/form-data"
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
            <select name="class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->grade_level?->label() ?? $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Term <span class="text-red-500">*</span></label>
            <select name="term_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select term</option>
                @foreach($terms as $t)
                <option value="{{ $t->id }}" {{ old('term_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->name }} — {{ $t->session->name ?? '' }}{{ $t->is_current ? ' (Current)' : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">File (CSV, Excel) <span class="text-red-500">*</span></label>
            <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <p class="mt-1 text-xs text-slate-400">Accepted: .csv, .xlsx, .xls — max 5MB</p>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('examination.results.index') }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit"
                class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Import Results
            </button>
        </div>
    </form>

</div>
@endsection
