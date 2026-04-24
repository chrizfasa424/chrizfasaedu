@extends('layouts.app')
@section('title', 'Assignments')
@section('header', 'Assignments')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Assignments</h1>
        <p class="text-sm text-slate-500 mt-1">Create and bulk-assign coursework to one or many classes.</p>
        @if(!$isAdmin && empty($authorizedClassIds))
            <p class="text-xs text-amber-700 mt-1">No explicit class assignment was found for your profile. You can assign to classes in your school, and class-subject compatibility is validated on submit.</p>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Create Assignment</h2>
        <form id="create-assignment-form" method="POST" action="{{ route('academic.assignments.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Session</label>
                    <select name="session_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">No session scope</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ (string) old('session_id') === (string) $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Term</label>
                    <select name="term_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">No term scope</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ (string) old('term_id') === (string) $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="unpublished" {{ old('status') === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Description / Instruction</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Attachment (pdf, jpg, jpeg, png, doc, docx) <span class="text-red-500">*</span></label>
                    <input type="file" name="attachment" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700">Class Targets <span class="text-red-500">*</span></h3>
                    <button type="button" id="add-target" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Add Target</button>
                </div>
                <div id="targets-wrap" class="mt-3 space-y-2"></div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Assignment</button>
            </div>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($assignments as $assignment)
            @php
                $targetSubjects = $assignment->targets
                    ->pluck('subject.name')
                    ->filter()
                    ->unique()
                    ->values();
                $subjectSummary = $targetSubjects->isNotEmpty() ? $targetSubjects->join(', ') : 'General';
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $assignment->title }}</h3>
                        <p class="text-xs text-slate-500">
                            {{ $subjectSummary }}
                            @if($assignment->due_date)
                                | Due: {{ $assignment->due_date->format('d M Y') }}
                            @endif
                            | Created by {{ $assignment->teacher?->full_name ?? 'N/A' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Submissions: {{ (int) ($assignment->submissions_count ?? 0) }}
                            @if((int) ($assignment->pending_submissions_count ?? 0) > 0)
                                | Pending review: {{ (int) $assignment->pending_submissions_count }}
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                        {{ $assignment->status === 'published' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $assignment->status === 'draft' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $assignment->status === 'unpublished' ? 'bg-slate-100 text-slate-700' : '' }}
                    ">{{ ucfirst($assignment->status) }}</span>
                </div>

                <p class="text-sm text-slate-700 mt-3">{{ $assignment->description ?: 'No description provided.' }}</p>

                <div class="mt-3 text-xs text-slate-600">
                    <span class="font-semibold">Targets:</span>
                    @foreach($assignment->targets as $target)
                        <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 mr-1 mt-1">
                            {{ $target->schoolClass?->grade_level?->label() ?? $target->schoolClass?->name }}
                            @if($target->arm)
                                - {{ $target->arm->name }}
                            @endif
                            @if($target->subject)
                                | {{ $target->subject->name }}
                            @endif
                        </span>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('academic.assignments.download', $assignment) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Download Attachment</a>
                    <a href="{{ route('academic.assignments.submissions.index', $assignment) }}" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Submissions ({{ (int) ($assignment->submissions_count ?? 0) }})</a>

                    @if($isAdmin || (int) $assignment->teacher_id === (int) auth()->id())
                        @if($assignment->status !== 'published')
                            <form method="POST" action="{{ route('academic.assignments.publish', $assignment) }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">Publish</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('academic.assignments.unpublish', $assignment) }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700">Unpublish</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('academic.assignments.destroy', $assignment) }}" onsubmit="return confirm('Delete this assignment permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-400">No assignments found.</div>
        @endforelse
    </div>

    {{ $assignments->links() }}
</div>

@php
    $classArms = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->arms->map(fn($arm) => ['id' => $arm->id, 'name' => $arm->name])->values(),
    ]);
    $classSubjects = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->subjects->map(fn($subject) => ['id' => (int) $subject->id, 'name' => (string) $subject->name])->values(),
    ]);
    $allowedClassIds = collect($authorizedClassIds)->map(fn($id) => (int) $id)->values();
@endphp

@push('scripts')
<script>
const classArms = @json($classArms);
const classSubjects = @json($classSubjects);
const allowedClassIds = @json($allowedClassIds);
const classes = @json($classes->map(fn($class) => [
    'id' => (int) $class->id,
    'label' => $class->grade_level?->label() ?? $class->name,
]));
const isAdmin = @json((bool) $isAdmin);
const shouldRestrictClassOptions = !isAdmin && allowedClassIds.length > 0;
const oldTargets = @json((array) old('targets', []));

const targetsWrap = document.getElementById('targets-wrap');
const addTargetBtn = document.getElementById('add-target');
const createAssignmentForm = document.getElementById('create-assignment-form');

function populateArmsAndSubjects(classSelect, armSelect, subjectSelect, selectedArmId = '', selectedSubjectId = '') {
    const classId = classSelect.value;
    const arms = classArms[classId] || [];
    const subjects = classSubjects[classId] || [];

    armSelect.innerHTML = '<option value="">All arms / no arm</option>';
    arms.forEach((arm) => {
        const option = document.createElement('option');
        option.value = String(arm.id);
        option.textContent = arm.name;
        if (String(arm.id) === String(selectedArmId)) {
            option.selected = true;
        }
        armSelect.appendChild(option);
    });

    subjectSelect.innerHTML = '';
    if (!classId) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Select class first';
        subjectSelect.appendChild(option);
        subjectSelect.value = '';
        return;
    }

    if (subjects.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No subject assigned to selected class';
        subjectSelect.appendChild(option);
        subjectSelect.value = '';
        return;
    }

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select subject';
    subjectSelect.appendChild(defaultOption);

    subjects.forEach((subject) => {
        const option = document.createElement('option');
        option.value = String(subject.id);
        option.textContent = subject.name;
        if (String(subject.id) === String(selectedSubjectId)) {
            option.selected = true;
        }
        subjectSelect.appendChild(option);
    });

    if (!subjects.some((subject) => String(subject.id) === String(subjectSelect.value))) {
        subjectSelect.value = '';
    }
}

function targetRow(index, initialTarget = {}) {
    const row = document.createElement('div');
    row.dataset.targetRow = '1';
    row.className = 'grid grid-cols-1 md:grid-cols-3 gap-2 rounded-lg border border-slate-200 p-3';

    const classSelect = document.createElement('select');
    classSelect.dataset.field = 'class_id';
    classSelect.className = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';

    const classDefault = document.createElement('option');
    classDefault.value = '';
    classDefault.textContent = 'Select class';
    classSelect.appendChild(classDefault);

    classes.forEach((classItem) => {
        if (shouldRestrictClassOptions && !allowedClassIds.includes(Number(classItem.id))) {
            return;
        }

        const option = document.createElement('option');
        option.value = String(classItem.id);
        option.textContent = classItem.label;
        classSelect.appendChild(option);
    });

    const armSelect = document.createElement('select');
    armSelect.dataset.field = 'arm_id';
    armSelect.className = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
    armSelect.innerHTML = '<option value="">All arms / no arm</option>';

    const subjectSelect = document.createElement('select');
    subjectSelect.dataset.field = 'subject_id';
    subjectSelect.className = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
    subjectSelect.required = true;

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'md:col-span-3 justify-self-end rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50';
    removeButton.textContent = 'Remove';
    removeButton.addEventListener('click', () => {
        row.remove();
        reindexTargetRows();
    });

    row.appendChild(classSelect);
    row.appendChild(armSelect);
    row.appendChild(subjectSelect);
    row.appendChild(removeButton);

    const selectedClassId = String(initialTarget.class_id || '');
    const selectedArmId = String(initialTarget.arm_id || '');
    const selectedSubjectId = String(initialTarget.subject_id || '');

    if (selectedClassId !== '') {
        classSelect.value = selectedClassId;
    }

    populateArmsAndSubjects(classSelect, armSelect, subjectSelect, selectedArmId, selectedSubjectId);
    classSelect.addEventListener('change', () => populateArmsAndSubjects(classSelect, armSelect, subjectSelect));

    return row;
}

function reindexTargetRows() {
    const rows = Array.from(targetsWrap.querySelectorAll('[data-target-row="1"]'));
    rows.forEach((row, index) => {
        row.querySelectorAll('select[data-field]').forEach((select) => {
            const field = select.dataset.field;
            select.name = `targets[${index}][${field}]`;
        });
    });
}

function addTarget(initialTarget = {}) {
    const index = targetsWrap.children.length;
    targetsWrap.appendChild(targetRow(index, initialTarget));
    reindexTargetRows();
}

addTargetBtn.addEventListener('click', addTarget);

const initialTargets = Array.isArray(oldTargets)
    ? oldTargets
    : Object.values(oldTargets || {});

if (initialTargets.length > 0) {
    initialTargets.forEach((target) => addTarget(target || {}));
} else if (targetsWrap.children.length === 0) {
    addTarget();
}

if (createAssignmentForm) {
    let csrfRefreshing = false;

    createAssignmentForm.addEventListener('submit', async (event) => {
        if (csrfRefreshing) {
            return;
        }

        event.preventDefault();
        csrfRefreshing = true;

        try {
            const response = await fetch(@json(route('csrf.token')), {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                const payload = await response.json();
                if (payload && payload.token) {
                    const tokenInput = createAssignmentForm.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = payload.token;
                    }

                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.setAttribute('content', payload.token);
                    }
                }
            }
        } catch (error) {
            // If token refresh fails, submit with the current token.
        }

        createAssignmentForm.submit();
    });
}
</script>
@endpush
@endsection
