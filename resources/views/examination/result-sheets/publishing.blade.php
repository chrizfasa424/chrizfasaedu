@extends('layouts.app')
@section('title', 'Result Publishing')
@section('header', 'Result Publishing')

@section('content')
<div class="space-y-6 max-w-6xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Result Publishing Control</h1>
            <p class="mt-1 text-sm text-slate-500">Publish or unpublish First Test, Second Test, Exam, or Full Terminal Result per class scope.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.import') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Import Result</a>
            <a href="{{ route('examination.result-sheets.class-sheet', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Class Result Sheet</a>
            <a href="{{ route('examination.result-sheets.history') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Import History</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('examination.result-sheets.publishing') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm grid grid-cols-1 md:grid-cols-7 gap-3">
        <select name="assessment_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
            @foreach($assessmentTypes as $type => $label)
                <option value="{{ $type }}" {{ (string)($filters['assessment_type'] ?? 'full_result') === (string)$type ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="section" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All sections</option>
            @foreach($sections as $section)
                <option value="{{ $section }}" {{ (string)($filters['section'] ?? '') === (string)$section ? 'selected' : '' }}>{{ $section }}</option>
            @endforeach
        </select>

        <select name="class_id" id="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
            <option value="">Select class</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$class->id ? 'selected' : '' }}>
                    {{ $class->grade_level?->label() ?? $class->name }}
                </option>
            @endforeach
        </select>

        <select name="arm_id" id="arm_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All arms / no arm</option>
        </select>

        <select name="session_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
            <option value="">Select session</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$session->id ? 'selected' : '' }}>{{ $session->name }}</option>
            @endforeach
        </select>

        <select name="term_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
            <option value="">Select term</option>
            @foreach($terms as $term)
                <option value="{{ $term->id }}" {{ (string)($filters['term_id'] ?? '') === (string)$term->id ? 'selected' : '' }}>{{ $term->name }}</option>
            @endforeach
        </select>

        <select name="exam_type_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
            <option value="">Select exam type</option>
            @foreach($examTypes as $examType)
                <option value="{{ $examType->id }}" {{ (string)($filters['exam_type_id'] ?? '') === (string)$examType->id ? 'selected' : '' }}>{{ $examType->name }}</option>
            @endforeach
        </select>

        <div class="md:col-span-7 flex flex-wrap justify-end gap-2">
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Apply Scope</button>
            <a href="{{ route('examination.result-sheets.publishing') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Assessment Stage</p>
            <p class="mt-2 text-xl font-bold text-slate-900">{{ $assessmentTypes[$filters['assessment_type'] ?? 'full_result'] ?? 'Full Terminal Result' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Scoped Students</p>
            <p class="mt-2 text-xl font-bold text-slate-900">{{ number_format((int) $scopedCount) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Already Published</p>
            <p class="mt-2 text-xl font-bold text-emerald-700">{{ number_format((int) $publishedCount) }}</p>
        </div>
    </div>

    @php
        $publishPayload = [
            'assessment_type' => $filters['assessment_type'] ?? 'full_result',
            'section' => $filters['section'] ?? null,
            'class_id' => $filters['class_id'] ?? null,
            'arm_id' => $filters['arm_id'] ?? null,
            'session_id' => $filters['session_id'] ?? null,
            'term_id' => $filters['term_id'] ?? null,
            'exam_type_id' => $filters['exam_type_id'] ?? null,
        ];
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900">Publishing Action</h2>
        <p class="mt-1 text-sm text-slate-500">Students can only view stages that are published. Unpublish hides that stage from the student portal immediately.</p>

        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('examination.result-sheets.publish') }}">
                @csrf
                @foreach($publishPayload as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" onclick="return confirm('Publish this stage for all students in the selected scope?')" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Publish Stage
                </button>
            </form>

            <form method="POST" action="{{ route('examination.result-sheets.unpublish') }}">
                @csrf
                @foreach($publishPayload as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" onclick="return confirm('Unpublish this stage for all students in the selected scope?')" class="rounded-lg border border-red-300 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                    Unpublish Stage
                </button>
            </form>
        </div>
    </div>
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
