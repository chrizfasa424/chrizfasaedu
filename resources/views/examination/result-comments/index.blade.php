@extends('layouts.app')
@section('title', 'Result Comments')
@section('header', 'Result Comments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Result Comments</h1>
            <p class="mt-1 text-sm text-slate-500">Teachers, principal, and vice principal can upload student result comments. Admin can activate or deactivate comment visibility.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">View Class Result Sheet</a>
        </div>
    </div>

    @if(!empty($editableRoles))
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
            You can edit: <span class="font-semibold">{{ implode(', ', $editableRoles) }}</span>.
        </div>
    @endif

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('examination.result-comments.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm grid grid-cols-1 md:grid-cols-7 gap-3">
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
            <a href="{{ route('examination.result-comments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    @if($canEditTeacherRemark && $results->count() > 0)
        <form method="POST" action="{{ route('examination.result-comments.generate-teacher-ai.bulk') }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3">
            @csrf
            @foreach($results as $listedResult)
                <input type="hidden" name="result_ids[]" value="{{ $listedResult->id }}">
            @endforeach
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="text-sm text-indigo-700">OpenAI can auto-generate teacher comments for students listed on this page based on performance and follow-up advice.</p>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                    Generate AI Teacher Comments (This Page)
                </button>
            </div>
        </form>
    @endif

    <div class="space-y-4">
        @forelse($results as $result)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $result->student?->full_name ?? 'Unknown Student' }}</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $result->student?->admission_number ?: $result->student?->registration_number }}
                            | {{ $result->schoolClass?->grade_level?->label() ?? $result->schoolClass?->name }}@if($result->arm) - {{ $result->arm->name }} @endif
                            | {{ $result->term?->name }} | {{ $result->session?->name }} | {{ $result->examType?->name }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('examination.result-sheets.student', $result) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Open Result Sheet</a>
                        @if($canEditTeacherRemark)
                            <form method="POST" action="{{ route('examination.result-comments.generate-teacher-ai', $result) }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-indigo-300 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                    Generate AI Teacher Comment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($canManageVisibility)
                    <form method="POST" action="{{ route('examination.result-comments.visibility', $result) }}" class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        @csrf
                        @method('PATCH')
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Admin Visibility Controls</p>
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="class_teacher_remark_active" value="0">
                                <input type="checkbox" name="class_teacher_remark_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $result->class_teacher_remark_active ? 'checked' : '' }}>
                                Show Teacher Comment
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="principal_remark_active" value="0">
                                <input type="checkbox" name="principal_remark_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $result->principal_remark_active ? 'checked' : '' }}>
                                Show Principal Comment
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="vice_principal_remark_active" value="0">
                                <input type="checkbox" name="vice_principal_remark_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $result->vice_principal_remark_active ? 'checked' : '' }}>
                                Show Vice Principal Comment
                            </label>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Save Visibility</button>
                        </div>
                    </form>
                @endif

                @php
                    $canSaveComment = $canEditTeacherRemark || $canEditPrincipalRemark || $canEditVicePrincipalRemark;
                @endphp
                <form method="POST" action="{{ route('examination.result-comments.update', $result) }}" class="mt-4 grid gap-4 md:grid-cols-3">
                    @csrf
                    @method('PATCH')

                    @if($canEditTeacherRemark)
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Teacher Comment</label>
                            <textarea name="class_teacher_remark" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('class_teacher_remark', $result->class_teacher_remark) }}</textarea>
                            <p class="mt-1 text-xs {{ $result->class_teacher_remark_active ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $result->class_teacher_remark_active ? 'Visible to student' : 'Hidden from student' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">OpenAI-generated comments include performance summary and next-term follow-up advice.</p>
                        </div>
                    @endif

                    @if($canEditPrincipalRemark)
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Principal Comment</label>
                            <textarea name="principal_remark" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('principal_remark', $result->principal_remark) }}</textarea>
                            <p class="mt-1 text-xs {{ $result->principal_remark_active ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $result->principal_remark_active ? 'Visible to student' : 'Hidden from student' }}
                            </p>
                        </div>
                    @endif

                    @if($canEditVicePrincipalRemark)
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Vice Principal Comment</label>
                            <textarea name="vice_principal_remark" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('vice_principal_remark', $result->vice_principal_remark) }}</textarea>
                            <p class="mt-1 text-xs {{ $result->vice_principal_remark_active ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $result->vice_principal_remark_active ? 'Visible to student' : 'Hidden from student' }}
                            </p>
                        </div>
                    @endif

                    @if($canSaveComment)
                        <div class="md:col-span-3">
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Comments</button>
                        </div>
                    @endif
                </form>
            </div>
        @empty
            @if(!$hasAnyResultRecords)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-10 text-center shadow-sm">
                    <p class="text-sm font-semibold text-amber-800">No result sheets available yet.</p>
                    <p class="mt-2 text-sm text-amber-700">Comments are attached to each student's result sheet. Import or compute results first, then return to this page to add role-based comments.</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <a href="{{ route('examination.result-sheets.import') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import Result</a>
                        <a href="{{ route('examination.result-sheets.class-sheet') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Open Class Result Sheet</a>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-10 text-center text-sm text-slate-500 shadow-sm">
                    No result records found for the selected filter.
                </div>
            @endif
        @endforelse
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
