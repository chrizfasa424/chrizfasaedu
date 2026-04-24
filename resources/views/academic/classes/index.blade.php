@extends('layouts.app')
@section('title', 'Classes')
@section('header', 'Classes')

@php
    $gradeSections = [
        'kg' => 'KG',
        'primary' => 'Primary',
        'secondary' => 'Secondary',
    ];

    $gradeCatalog = [
        'kg' => [
            ['name' => 'KG 1', 'grade' => 'kg1'],
            ['name' => 'KG 2', 'grade' => 'kg2'],
            ['name' => 'KG 3', 'grade' => 'kg3'],
        ],
        'primary' => [
            ['name' => 'Primary 1', 'grade' => 'primary_1'],
            ['name' => 'Primary 2', 'grade' => 'primary_2'],
            ['name' => 'Primary 3', 'grade' => 'primary_3'],
            ['name' => 'Primary 4', 'grade' => 'primary_4'],
            ['name' => 'Primary 5', 'grade' => 'primary_5'],
            ['name' => 'Primary 6', 'grade' => 'primary_6'],
        ],
        'secondary' => [
            ['name' => 'JSS 1', 'grade' => 'jss_1'],
            ['name' => 'JSS 2', 'grade' => 'jss_2'],
            ['name' => 'JSS 3', 'grade' => 'jss_3'],
            ['name' => 'SS 1', 'grade' => 'sss_1'],
            ['name' => 'SS 2', 'grade' => 'sss_2'],
            ['name' => 'SS 3', 'grade' => 'sss_3'],
        ],
    ];
@endphp

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Classes</h1>
            <p class="mt-0.5 text-sm text-slate-500">Manage school classes and arms.</p>
        </div>
        <button onclick="document.getElementById('create-class-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Class
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($classes as $class)
            @php
                $sectionLabel = $class->section ?: ($class->grade_level?->section() ?? '—');
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800">{{ $class->name }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $sectionLabel }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('academic.classes.show', $class) }}" class="text-xs font-medium text-indigo-600 hover:underline">View</a>
                        <button type="button"
                            onclick="openEditModal({{ $class->id }}, '{{ addslashes($class->name) }}', '{{ $class->grade_level?->value }}', {{ $class->capacity ?? 40 }}, '{{ addslashes($class->arms->pluck('name')->join(', ')) }}')"
                            class="text-xs font-medium text-amber-600 hover:text-amber-800">
                            Edit
                        </button>
                        @if($classes->total() > 1)
                            <form method="POST" action="{{ route('academic.classes.destroy', $class) }}"
                                onsubmit="return confirm('Delete {{ addslashes($class->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="rounded-lg bg-slate-50 p-2">
                        <div class="font-semibold text-slate-800">{{ $class->students_count ?? $class->students->count() }}</div>
                        <div class="text-slate-400">Students</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-2">
                        <div class="font-semibold text-slate-800">{{ $class->arms->count() }}</div>
                        <div class="text-slate-400">Arms</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-2">
                        <div class="font-semibold text-slate-800">{{ $class->capacity ?? '—' }}</div>
                        <div class="text-slate-400">Capacity</div>
                    </div>
                </div>

                @if($class->arms->count())
                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach($class->arms as $arm)
                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $arm->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-3 rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-slate-400">
                No classes yet. Add one to get started.
            </div>
        @endforelse
    </div>

    <div>{{ $classes->links() }}</div>

</div>

{{-- Create Class Modal --}}
<div id="create-class-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Add Class</h2>
            <button onclick="document.getElementById('create-class-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="create-class-form" method="POST" action="{{ route('academic.classes.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="name" id="create_name_hidden">
            <input type="hidden" name="grade_level" id="create_grade_hidden">
            <input type="hidden" name="section" id="create_section_hidden">

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Grade Level <span class="text-red-500">*</span></label>
                <select id="create_section_selector" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select a Grade Level</option>
                    @foreach($gradeSections as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Class Name <span class="text-red-500">*</span></label>
                <input type="text" id="create_name_input" required placeholder="e.g. KG 1, Primary 2, JSS 1"
                    list="create_class_name_suggestions"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <datalist id="create_class_name_suggestions"></datalist>
                <p class="mt-1 text-xs text-slate-400">Type class name manually. Example: KG 1, Primary 5, JSS 1, SS 2.</p>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Capacity</label>
                <input type="number" name="capacity" min="1" value="40"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Arms (comma-separated)</label>
                <input type="text" name="arms" placeholder="e.g. A, B, C"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    onchange="this.value = this.value.split(',').map(s=>s.trim()).filter(Boolean).join(', ')">
                <p class="mt-1 text-xs text-slate-400">Leave empty for no arms.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-class-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Class Modal --}}
<div id="edit-class-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Edit Class</h2>
            <button onclick="document.getElementById('edit-class-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit-class-form" method="POST" action="" class="space-y-4">
            @csrf @method('PUT')

            <input type="hidden" name="name" id="edit_name_hidden">
            <input type="hidden" name="grade_level" id="edit_grade_hidden">
            <input type="hidden" name="section" id="edit_section_hidden">

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Grade Level <span class="text-red-500">*</span></label>
                <select id="edit_section_selector" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select a Grade Level</option>
                    @foreach($gradeSections as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Class Name <span class="text-red-500">*</span></label>
                <input type="text" id="edit_name_input" required placeholder="e.g. KG 1, Primary 2, JSS 1"
                    list="edit_class_name_suggestions"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <datalist id="edit_class_name_suggestions"></datalist>
                <p class="mt-1 text-xs text-slate-400">Type class name manually. Example: KG 1, Primary 5, JSS 1, SS 2.</p>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Capacity</label>
                <input type="number" name="capacity" min="1"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Arms (comma-separated)</label>
                <input type="text" name="arms" placeholder="e.g. A, B, C"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="mt-1 text-xs text-slate-400">Removing an arm here will delete it permanently.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-class-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const classCatalog = @json($gradeCatalog);
const sectionLabels = @json($gradeSections);

function normalizeToken(value) {
    return String(value || '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '');
}

function buildClassSuggestions(listElement, sectionKey) {
    listElement.innerHTML = '';
    const entries = classCatalog[sectionKey] || [];
    entries.forEach((entry) => {
        const option = document.createElement('option');
        option.value = entry.name;
        listElement.appendChild(option);
    });
}

function getSectionKeyFromGrade(gradeValue) {
    const value = String(gradeValue || '').toLowerCase();
    if (value.startsWith('kg')) return 'kg';
    if (value.startsWith('primary')) return 'primary';
    if (value.startsWith('jss') || value.startsWith('sss')) return 'secondary';
    return '';
}

function inferGradeFromName(sectionKey, className) {
    const entries = classCatalog[sectionKey] || [];
    const normalizedName = normalizeToken(className);

    // Secondary alias support: SS1, SSS1, JSS1 formats.
    const secondaryMatch = normalizedName.match(/^(jss|sss|ss)([123])/);
    if (sectionKey === 'secondary' && secondaryMatch) {
        const level = secondaryMatch[2];
        if (secondaryMatch[1] === 'jss') return 'jss_' + level;
        return 'sss_' + level;
    }

    for (const entry of entries) {
        const normalizedEntry = normalizeToken(entry.name);
        if (
            normalizedName === normalizedEntry ||
            normalizedName.startsWith(normalizedEntry) ||
            normalizedEntry.startsWith(normalizedName)
        ) {
            return entry.grade;
        }
    }

    return '';
}

function syncHiddenFields(sectionSelect, classInput, nameHidden, gradeHidden, sectionHidden) {
    const sectionKey = sectionSelect.value || '';
    const className = String(classInput.value || '').trim();
    const gradeValue = inferGradeFromName(sectionKey, className);

    nameHidden.value = className;
    gradeHidden.value = gradeValue;
    sectionHidden.value = sectionLabels[sectionKey] || '';
}

function getClassNameGuidance(sectionKey) {
    if (sectionKey === 'kg') return 'Use KG 1, KG 2, or KG 3.';
    if (sectionKey === 'primary') return 'Use Primary 1 to Primary 6.';
    if (sectionKey === 'secondary') return 'Use JSS 1-3 or SS 1-3.';
    return 'Choose Grade Level and enter a valid class name.';
}

const createForm = document.getElementById('create-class-form');
if (createForm) {
    const createSection = document.getElementById('create_section_selector');
    const createClass = document.getElementById('create_name_input');
    const createSuggestions = document.getElementById('create_class_name_suggestions');
    const createNameHidden = document.getElementById('create_name_hidden');
    const createGradeHidden = document.getElementById('create_grade_hidden');
    const createSectionHidden = document.getElementById('create_section_hidden');

    createSection.addEventListener('change', () => {
        buildClassSuggestions(createSuggestions, createSection.value);
        createClass.placeholder = createSection.value === 'secondary'
            ? 'e.g. JSS 1 or SS 1'
            : (createSection.value === 'primary' ? 'e.g. Primary 1' : 'e.g. KG 1');
        syncHiddenFields(createSection, createClass, createNameHidden, createGradeHidden, createSectionHidden);
    });

    createClass.addEventListener('input', () => {
        syncHiddenFields(createSection, createClass, createNameHidden, createGradeHidden, createSectionHidden);
    });

    createForm.addEventListener('submit', function (event) {
        syncHiddenFields(createSection, createClass, createNameHidden, createGradeHidden, createSectionHidden);
        if (!createNameHidden.value || !createGradeHidden.value) {
            event.preventDefault();
            alert('Please enter a valid Class Name. ' + getClassNameGuidance(createSection.value));
            return;
        }

        const armsInput = this.querySelector('[name="arms"]');
        const val = (armsInput?.value || '').trim();
        if (val) {
            const arms = val.split(',').map(s => s.trim()).filter(Boolean);
            armsInput.remove();
            arms.forEach(arm => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'arms[]';
                inp.value = arm;
                this.appendChild(inp);
            });
        }
    });
}

const editForm = document.getElementById('edit-class-form');
if (editForm) {
    const editSection = document.getElementById('edit_section_selector');
    const editClass = document.getElementById('edit_name_input');
    const editSuggestions = document.getElementById('edit_class_name_suggestions');
    const editNameHidden = document.getElementById('edit_name_hidden');
    const editGradeHidden = document.getElementById('edit_grade_hidden');
    const editSectionHidden = document.getElementById('edit_section_hidden');

    editSection.addEventListener('change', () => {
        buildClassSuggestions(editSuggestions, editSection.value);
        editClass.placeholder = editSection.value === 'secondary'
            ? 'e.g. JSS 1 or SS 1'
            : (editSection.value === 'primary' ? 'e.g. Primary 1' : 'e.g. KG 1');
        syncHiddenFields(editSection, editClass, editNameHidden, editGradeHidden, editSectionHidden);
    });

    editClass.addEventListener('input', () => {
        syncHiddenFields(editSection, editClass, editNameHidden, editGradeHidden, editSectionHidden);
    });

    editForm.addEventListener('submit', function (event) {
        syncHiddenFields(editSection, editClass, editNameHidden, editGradeHidden, editSectionHidden);
        if (!editNameHidden.value || !editGradeHidden.value) {
            event.preventDefault();
            alert('Please enter a valid Class Name. ' + getClassNameGuidance(editSection.value));
            return;
        }

        const armsInput = this.querySelector('[name="arms"]');
        const val = (armsInput?.value || '').trim();
        this.querySelectorAll('input[name="arms[]"]').forEach(el => el.remove());
        if (val) {
            const arms = val.split(',').map(s => s.trim()).filter(Boolean);
            armsInput.remove();
            arms.forEach(arm => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'arms[]';
                inp.value = arm;
                this.appendChild(inp);
            });
        } else if (armsInput) {
            armsInput.remove();
        }
    });
}

function openEditModal(id, name, gradeLevel, capacity, arms) {
    const modal = document.getElementById('edit-class-modal');
    const form = document.getElementById('edit-class-form');
    const sectionSelect = document.getElementById('edit_section_selector');
    const classInput = document.getElementById('edit_name_input');
    const suggestions = document.getElementById('edit_class_name_suggestions');
    const nameHidden = document.getElementById('edit_name_hidden');
    const gradeHidden = document.getElementById('edit_grade_hidden');
    const sectionHidden = document.getElementById('edit_section_hidden');

    form.action = '/academic/classes/' + id;
    form.querySelector('[name="capacity"]').value = capacity;
    form.querySelector('[name="arms"]').value = arms;

    const sectionKey = getSectionKeyFromGrade(gradeLevel);
    sectionSelect.value = sectionKey;
    buildClassSuggestions(suggestions, sectionKey);
    classInput.value = name;
    syncHiddenFields(sectionSelect, classInput, nameHidden, gradeHidden, sectionHidden);

    if (!nameHidden.value) {
        nameHidden.value = name;
    }

    if (!gradeHidden.value) {
        gradeHidden.value = gradeLevel;
    }

    modal.classList.remove('hidden');
}
</script>
@endpush
@endsection
