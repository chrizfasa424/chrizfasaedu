@extends('layouts.app')
@section('title', 'Result Sheet Import')
@section('header', 'Result Sheet Import')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Result Sheet Import</h1>
            <p class="text-sm text-slate-500 mt-1">Upload and validate First Test, Second Test, Exam, or Full Terminal results before saving.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Class Result Sheet</a>
            <a href="{{ route('examination.result-sheets.publishing') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Publishing</a>
            <a href="{{ route('examination.result-sheets.history') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Import History</a>
        </div>
    </div>

    @php
        $assessmentType = $selectedAssessmentType ?? old('assessment_type', 'full_result');
        $assessmentLabels = $assessmentTypes ?? [
            'first_test' => 'First Test',
            'second_test' => 'Second Test',
            'exam' => 'Exam',
            'full_result' => 'Full Terminal Result',
        ];
        $assessmentHelp = match($assessmentType) {
            'first_test' => 'Expected format: Student Number | Full Name | Subject | First Test (or Score).',
            'second_test' => 'Expected format: Student Number | Full Name | Subject | Second Test (or Score).',
            'exam' => 'Expected format: Student Number | Full Name | Subject | Examination (or Score).',
            default => 'Expected format: Student Number | Full Name | Subject | First Test | Second Test | Examination. Extra summary columns are allowed.',
        };
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Step 0: Choose Import Stage</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($assessmentLabels as $type => $label)
                <a href="{{ route('examination.result-sheets.import', ['assessmentType' => $type]) }}"
                    class="rounded-xl border px-4 py-3 text-sm font-semibold transition {{ $assessmentType === $type ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-slate-500">{{ $assessmentHelp }}</p>
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

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Step 1: Select Scope and Upload File</h2>
        <form method="POST" action="{{ route('examination.result-sheets.preview') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <input type="hidden" name="assessment_type" id="assessment_type" value="{{ $assessmentType }}">

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Section</label>
                <select name="section" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Auto from class</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ old('section') === $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="class_id" id="class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->grade_level?->label() ?? $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Arm</label>
                <select name="arm_id" id="arm_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All arms / no arm</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Academic Session <span class="text-red-500">*</span></label>
                <select name="session_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('session_id', $session->is_current ? $session->id : null) == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}{{ $session->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term <span class="text-red-500">*</span></label>
                <select name="term_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ old('term_id', $term->is_current ? $term->id : null) == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} - {{ $term->session->name ?? '' }}{{ $term->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Exam Type <span class="text-red-500">*</span></label>
                <select name="exam_type_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select exam type</option>
                    @foreach($examTypes as $examType)
                        <option value="{{ $examType->id }}" {{ old('exam_type_id') == $examType->id ? 'selected' : '' }}>
                            {{ $examType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Import Mode <span class="text-red-500">*</span></label>
                <select name="import_mode" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="create_only" {{ old('import_mode', 'create_only') === 'create_only' ? 'selected' : '' }}>Create Only</option>
                    <option value="update_existing" {{ old('import_mode') === 'update_existing' ? 'selected' : '' }}>Update Existing</option>
                    <option value="replace_existing" {{ old('import_mode') === 'replace_existing' ? 'selected' : '' }}>Replace Existing</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Result File (CSV/XLSX/XLS) <span class="text-red-500">*</span></label>
                <input type="file" name="file" required accept=".csv,.xlsx,.xls" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="flex items-end justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Validate and Preview
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-slate-700">Dynamic Template Download</h2>
                <p class="text-xs text-slate-500 mt-1">Template includes only subjects assigned to the selected class and selected import stage.</p>
            </div>
            <a id="template-link" href="{{ route('examination.result-sheets.template') }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Download Template</a>
        </div>
    </div>
</div>

@php
    $classArms = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->arms->map(fn($arm) => ['id' => $arm->id, 'name' => $arm->name])->values()
    ]);
@endphp

@push('scripts')
<script>
const classArms = @json($classArms);
const classSelect = document.getElementById('class_id');
const armSelect = document.getElementById('arm_id');
const templateLink = document.getElementById('template-link');

function refreshArms() {
    const classId = classSelect.value;
    const arms = classArms[classId] || [];
    const selected = "{{ old('arm_id') }}";
    armSelect.innerHTML = '<option value="">All arms / no arm</option>';
    arms.forEach((arm) => {
        const option = document.createElement('option');
        option.value = arm.id;
        option.textContent = arm.name;
        if (String(arm.id) === String(selected)) option.selected = true;
        armSelect.appendChild(option);
    });
}

function refreshTemplateLink() {
    const classId = classSelect.value;
    const armId = armSelect.value;
    const assessmentType = document.getElementById('assessment_type').value;
    const url = new URL("{{ route('examination.result-sheets.template') }}", window.location.origin);
    if (classId) url.searchParams.set('class_id', classId);
    if (armId) url.searchParams.set('arm_id', armId);
    if (assessmentType) url.searchParams.set('assessment_type', assessmentType);
    templateLink.href = url.toString();
}

classSelect.addEventListener('change', () => {
    refreshArms();
    refreshTemplateLink();
});
armSelect.addEventListener('change', refreshTemplateLink);

refreshArms();
refreshTemplateLink();
</script>
@endpush
@endsection
