@extends('layouts.app')
@section('title', 'Result Import History')
@section('header', 'Result Import History')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Result Import History</h1>
            <p class="text-sm text-slate-500 mt-1">Track import batches, status, and validation outcomes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.import') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">New Import</a>
            <a href="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Class Result Sheet</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('examination.result-sheets.history') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm grid grid-cols-1 md:grid-cols-6 gap-3">
        <select name="assessment_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All stages</option>
            @foreach($assessmentTypes as $type => $label)
                <option value="{{ $type }}" {{ (string)($filters['assessment_type'] ?? '') === (string)$type ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$class->id ? 'selected' : '' }}>
                    {{ $class->grade_level?->label() ?? $class->name }}
                </option>
            @endforeach
        </select>
        <select name="session_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All sessions</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$session->id ? 'selected' : '' }}>
                    {{ $session->name }}
                </option>
            @endforeach
        </select>
        <select name="term_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All terms</option>
            @foreach($terms as $term)
                <option value="{{ $term->id }}" {{ (string)($filters['term_id'] ?? '') === (string)$term->id ? 'selected' : '' }}>
                    {{ $term->name }}
                </option>
            @endforeach
        </select>
        <select name="exam_type_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All exam types</option>
            @foreach($examTypes as $examType)
                <option value="{{ $examType->id }}" {{ (string)($filters['exam_type_id'] ?? '') === (string)$examType->id ? 'selected' : '' }}>
                    {{ $examType->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Filter</button>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-2 text-left">Batch</th>
                    <th class="px-4 py-2 text-left">Scope</th>
                    <th class="px-4 py-2 text-left">Mode</th>
                    <th class="px-4 py-2 text-left">Rows</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Imported By</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($batches as $batch)
                    <tr>
                        <td class="px-4 py-2">
                            <div class="font-medium text-slate-800">#{{ $batch->id }}</div>
                            <div class="text-xs text-slate-400">{{ $batch->file_name }}</div>
                        </td>
                        <td class="px-4 py-2 text-slate-700">
                            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                {{ $assessmentTypes[$batch->assessment_type] ?? ucfirst(str_replace('_', ' ', (string) $batch->assessment_type)) }}
                            </div>
                            {{ $batch->schoolClass?->grade_level?->label() ?? $batch->schoolClass?->name }}
                            @if($batch->arm) - {{ $batch->arm->name }} @endif
                            <div class="text-xs text-slate-400">{{ $batch->term?->name }} | {{ $batch->session?->name }} | {{ $batch->examType?->name }}</div>
                        </td>
                        <td class="px-4 py-2 text-slate-700">{{ str_replace('_', ' ', $batch->import_mode) }}</td>
                        <td class="px-4 py-2 text-slate-700">
                            <div>Total: {{ $batch->total_rows }}</div>
                            <div class="text-xs text-green-600">Success: {{ $batch->success_rows }}</div>
                            <div class="text-xs text-red-600">Failed: {{ $batch->failed_rows }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $batch->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $batch->status === 'validated' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $batch->status === 'validation_failed' ? 'bg-red-100 text-red-700' : '' }}
                                {{ !in_array($batch->status, ['completed', 'validated', 'validation_failed']) ? 'bg-slate-100 text-slate-700' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($batch->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-slate-700">
                            {{ $batch->importer?->full_name ?? 'System' }}
                            <div class="text-xs text-slate-400">{{ $batch->created_at?->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('examination.result-sheets.preview.show', $batch) }}" class="text-xs text-indigo-600 hover:underline">Preview</a>
                                @if($batch->errors()->count() > 0)
                                    <a href="{{ route('examination.result-sheets.errors', $batch) }}" class="text-xs text-red-600 hover:underline">Errors</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">No import history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $batches->links() }}
</div>
@endsection
