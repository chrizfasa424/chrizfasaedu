@extends('layouts.app')
@section('title', 'Class Result Sheet')
@section('header', 'Class Result Sheet')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Class Result Sheet</h1>
            <p class="mt-1 text-sm text-slate-500">Filter, preview, print, and monitor publication for First Test, Second Test, Exam, and Full Terminal Result.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.import') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import Result</a>
            <a href="{{ route('examination.result-sheets.publishing', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Publishing</a>
            <a href="{{ route('examination.result-comments.index', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Comments</a>
            <a href="{{ route('examination.result-sheets.history') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Import History</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->has('bulk_print'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first('bulk_print') }}</div>
    @endif

    <form method="GET" action="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm grid grid-cols-1 md:grid-cols-7 gap-3">
        <select name="section" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All sections</option>
            @foreach($sections as $section)
                <option value="{{ $section }}" {{ (string)($filters['section'] ?? '') === (string)$section ? 'selected' : '' }}>{{ $section }}</option>
            @endforeach
        </select>

        <select name="class_id" id="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$class->id ? 'selected' : '' }}>
                    {{ $class->grade_level?->label() ?? $class->name }}
                </option>
            @endforeach
        </select>

        <select name="arm_id" id="arm_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All arms / no arm</option>
        </select>

        <select name="session_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All sessions</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$session->id ? 'selected' : '' }}>{{ $session->name }}</option>
            @endforeach
        </select>

        <select name="term_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All terms</option>
            @foreach($terms as $term)
                <option value="{{ $term->id }}" {{ (string)($filters['term_id'] ?? '') === (string)$term->id ? 'selected' : '' }}>{{ $term->name }}</option>
            @endforeach
        </select>

        <select name="exam_type_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All exam types</option>
            @foreach($examTypes as $examType)
                <option value="{{ $examType->id }}" {{ (string)($filters['exam_type_id'] ?? '') === (string)$examType->id ? 'selected' : '' }}>{{ $examType->name }}</option>
            @endforeach
        </select>

        <select name="student_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All students</option>
            @foreach($studentsForFilter as $student)
                <option value="{{ $student->id }}" {{ (string)($filters['student_id'] ?? '') === (string)$student->id ? 'selected' : '' }}>
                    {{ $student->full_name }} ({{ $student->admission_number ?: $student->registration_number }})
                </option>
            @endforeach
        </select>

        <div class="md:col-span-7 flex flex-wrap justify-end gap-2">
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Apply Filter</button>
            <a href="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            <a href="{{ route('examination.result-sheets.bulk-print', request()->query()) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Bulk Print PDF</a>
        </div>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-2 text-left">Student</th>
                    <th class="px-4 py-2 text-left">Scope</th>
                    <th class="px-4 py-2 text-left">Summary</th>
                    <th class="px-4 py-2 text-left">Import Stages</th>
                    <th class="px-4 py-2 text-left">Published Stages</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($results as $result)
                    @php
                        $stageImported = [
                            'first_test' => !is_null($result->first_test_imported_at),
                            'second_test' => !is_null($result->second_test_imported_at),
                            'exam' => !is_null($result->exam_imported_at),
                            'full_result' => !is_null($result->full_result_imported_at),
                        ];
                        $stagePublished = [
                            'first_test' => !is_null($result->first_test_published_at),
                            'second_test' => !is_null($result->second_test_published_at),
                            'exam' => !is_null($result->exam_published_at),
                            'full_result' => !is_null($result->full_result_published_at) || (bool) $result->is_published || !is_null($result->published_at),
                        ];
                    @endphp
                    <tr>
                        <td class="px-4 py-2">
                            <div class="font-medium text-slate-800">{{ $result->student?->full_name ?? 'Unknown Student' }}</div>
                            <div class="text-xs text-slate-400">{{ $result->student?->admission_number ?: $result->student?->registration_number }}</div>
                        </td>
                        <td class="px-4 py-2 text-slate-700">
                            <div>{{ $result->schoolClass?->grade_level?->label() ?? $result->schoolClass?->name }}@if($result->arm) - {{ $result->arm->name }} @endif</div>
                            <div class="text-xs text-slate-400">{{ $result->term?->name }} | {{ $result->session?->name }} | {{ $result->examType?->name }}</div>
                        </td>
                        <td class="px-4 py-2 text-slate-700">
                            <div>Total: <span class="font-semibold">{{ number_format((float) $result->total_score, 2) }}</span></div>
                            <div>Average: <span class="font-semibold">{{ number_format((float) $result->average_score, 2) }}</span></div>
                            <div class="text-xs text-slate-500">
                                Position: {{ $result->class_position ?: '-' }}
                                | Class Avg: {{ $result->class_average !== null ? number_format((float) $result->class_average, 2) : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($assessmentTypes as $type => $label)
                                    <span class="inline-flex rounded-full px-2 py-1 text-[11px] font-semibold {{ $stageImported[$type] ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $label }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($assessmentTypes as $type => $label)
                                    <span class="inline-flex rounded-full px-2 py-1 text-[11px] font-semibold {{ $stagePublished[$type] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $label }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('examination.result-sheets.student', $result) }}" class="text-xs font-medium text-indigo-600 hover:underline">Open Sheet</a>
                                <a href="{{ route('examination.result-sheets.student.pdf', $result) }}" class="text-xs font-medium text-emerald-600 hover:underline">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No student result sheet found for the selected filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $results->links() }}
</div>

@php
    $classArms = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->arms->map(fn($arm) => ['id' => $arm->id, 'name' => $arm->name])->values()
    ]);
    $selectedArmId = (string) ($filters['arm_id'] ?? '');
@endphp

@push('scripts')
<script>
const classArms = @json($classArms);
const classSelect = document.getElementById('class_id');
const armSelect = document.getElementById('arm_id');
const selectedArmId = @json($selectedArmId);

function refreshArms() {
    const classId = classSelect ? classSelect.value : '';
    const arms = classArms[classId] || [];
    armSelect.innerHTML = '<option value="">All arms / no arm</option>';

    arms.forEach((arm) => {
        const option = document.createElement('option');
        option.value = arm.id;
        option.textContent = arm.name;
        if (String(arm.id) === String(selectedArmId)) {
            option.selected = true;
        }
        armSelect.appendChild(option);
    });
}

if (classSelect && armSelect) {
    classSelect.addEventListener('change', refreshArms);
    refreshArms();
}
</script>
@endpush
@endsection
