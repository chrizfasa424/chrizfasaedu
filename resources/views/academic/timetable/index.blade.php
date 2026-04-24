@extends('layouts.app')
@section('title', 'Timetable')
@section('header', 'Timetable')

@php
    $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $selectedClass = $classes->firstWhere('id', (int) ($classId ?? 0));
    $selectedArm = $arms->firstWhere('id', (int) ($armId ?? 0));
    $teacherOptionLabel = static function ($teacher): string {
        $name = trim((string) ($teacher?->full_name ?? 'Unassigned'));
        $designation = trim((string) ($teacher?->designation ?? ''));
        return $designation !== '' ? ($name . ' — ' . $designation) : $name;
    };
    $scopeLabel = $selectedArm
        ? (($selectedClass?->name ?? 'Class') . ' - Arm ' . $selectedArm->name)
        : (($selectedClass?->name ?? 'Class') . ' - Class Wide');
@endphp

@section('content')
<div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Timetable Planner</h1>
                <p class="mt-1 text-sm text-slate-500">Complete class timetable CRUD with class/arm-specific periods.</p>
                <p class="mt-1 text-xs text-slate-400">
                    Session: {{ $currentSession?->name ?? 'Not Set' }} |
                    Term: {{ $currentTerm?->name ?? 'Not Set' }}
                </p>
            </div>
            @if($selectedClass)
                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('academic.timetable.generate-sample') }}">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                        @if($selectedArm)
                            <input type="hidden" name="arm_id" value="{{ $selectedArm->id }}">
                        @endif
                        <input type="hidden" name="replace_existing" value="1">
                        <button type="submit" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                            Generate Sample
                        </button>
                    </form>
                    <button type="button" id="open-add-modal" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Add Entry
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('academic.timetable.index') }}" class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Class</label>
                <select name="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected((int) ($classId ?? 0) === (int) $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Arm Scope</label>
                <select name="arm_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800" @disabled(!$selectedClass)>
                    <option value="">Class Wide</option>
                    @foreach($arms as $arm)
                        <option value="{{ $arm->id }}" @selected((int) ($armId ?? 0) === (int) $arm->id)>
                            Arm {{ $arm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Apply Scope
                </button>
                <a href="{{ route('academic.timetable.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </section>

    @if(!$selectedClass)
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
            <p class="text-lg font-bold text-slate-800">Select a class to manage timetable</p>
            <p class="mt-2 text-sm text-slate-500">Each class can have a different timetable plan and periods.</p>
        </section>
    @else
        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">{{ $scopeLabel }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">Edit, activate/deactivate, or remove timetable periods.</p>
            </div>

            <div class="space-y-4 p-4">
                @foreach($dayOrder as $day)
                    @php $slots = $timetable->get($day, collect()); @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="border-b border-slate-100 bg-slate-50 px-5 py-3">
                            <h3 class="text-sm font-semibold text-slate-700">{{ $day }}</h3>
                        </div>

                        @if($slots->isEmpty())
                            <div class="px-5 py-5 text-sm text-slate-400">No periods set for {{ strtolower($day) }}.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="bg-white text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                            <th class="px-5 py-3 text-left">Time</th>
                                            <th class="px-5 py-3 text-left">Subject</th>
                                            <th class="px-5 py-3 text-left">Teacher</th>
                                            <th class="px-5 py-3 text-left">Room</th>
                                            <th class="px-5 py-3 text-center">Status</th>
                                            <th class="px-5 py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($slots as $slot)
                                            <tr class="hover:bg-slate-50/70">
                                                <td class="px-5 py-3.5 font-semibold text-slate-800">
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                                </td>
                                                <td class="px-5 py-3.5 text-slate-700">{{ $slot->subject?->name ?? '-' }}</td>
                                                <td class="px-5 py-3.5 text-slate-600">
                                                    @if($slot->teacher)
                                                        <div class="font-medium text-slate-700">{{ $slot->teacher->full_name }}</div>
                                                        @if(!empty($slot->teacher->designation))
                                                            <div class="text-xs text-slate-400">{{ $slot->teacher->designation }}</div>
                                                        @endif
                                                    @else
                                                        <span>Unassigned</span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-3.5 text-slate-600">{{ $slot->room ?: '—' }}</td>
                                                <td class="px-5 py-3.5 text-center">
                                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $slot->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                                        {{ $slot->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button"
                                                            class="open-edit-modal rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                                                            data-id="{{ $slot->id }}"
                                                            data-class-id="{{ $slot->class_id }}"
                                                            data-arm-id="{{ $slot->arm_id }}"
                                                            data-subject-id="{{ $slot->subject_id }}"
                                                            data-teacher-id="{{ $slot->teacher_id }}"
                                                            data-day="{{ strtolower($slot->day_of_week) }}"
                                                            data-start-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}"
                                                            data-end-time="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}"
                                                            data-room="{{ $slot->room }}"
                                                            data-is-active="{{ $slot->is_active ? '1' : '0' }}">
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('academic.timetable.destroy', $slot) }}" onsubmit="return confirm('Delete this timetable entry?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                                                            @if($selectedArm)
                                                                <input type="hidden" name="arm_id" value="{{ $selectedArm->id }}">
                                                            @endif
                                                            <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>

@if($selectedClass)
    <div id="add-entry-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 p-4">
        <div class="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Add Timetable Entry</h3>
                <button type="button" class="close-add-modal rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100">Close</button>
            </div>

            <form method="POST" action="{{ route('academic.timetable.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                @if($selectedArm)
                    <input type="hidden" name="arm_id" value="{{ $selectedArm->id }}">
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</label>
                        <select name="subject_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Teacher</label>
                        <select name="teacher_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            <option value="">Unassigned</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacherOptionLabel($teacher) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Day</label>
                        <select name="day_of_week" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Start Time</label>
                        <input type="time" name="start_time" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">End Time</label>
                        <input type="time" name="end_time" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Room</label>
                        <input type="text" name="room" maxlength="80" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Optional room">
                    </div>
                    <div class="flex items-center gap-2 pt-7">
                        <input type="hidden" name="is_active" value="0">
                        <input id="add_is_active" type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                        <label for="add_is_active" class="text-sm font-medium text-slate-700">Set as active</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="close-add-modal rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Entry</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-entry-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 p-4">
        <div class="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Edit Timetable Entry</h3>
                <button type="button" class="close-edit-modal rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100">Close</button>
            </div>

            <form id="edit-entry-form" method="POST" action="" data-update-url-template="{{ route('academic.timetable.update', ['timetable' => '__TIMETABLE__']) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                @if($selectedArm)
                    <input type="hidden" name="arm_id" value="{{ $selectedArm->id }}">
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</label>
                        <select id="edit_subject_id" name="subject_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Teacher</label>
                        <select id="edit_teacher_id" name="teacher_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            <option value="">Unassigned</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacherOptionLabel($teacher) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Day</label>
                        <select id="edit_day_of_week" name="day_of_week" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Start Time</label>
                        <input id="edit_start_time" type="time" name="start_time" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">End Time</label>
                        <input id="edit_end_time" type="time" name="end_time" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Room</label>
                        <input id="edit_room" type="text" name="room" maxlength="80" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="Optional room">
                    </div>
                    <div class="flex items-center gap-2 pt-7">
                        <input type="hidden" name="is_active" value="0">
                        <input id="edit_is_active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                        <label for="edit_is_active" class="text-sm font-medium text-slate-700">Set as active</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="close-edit-modal rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update Entry</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const addModal = document.getElementById('add-entry-modal');
    const openAddButton = document.getElementById('open-add-modal');
    const closeAddButtons = document.querySelectorAll('.close-add-modal');

    if (addModal && openAddButton) {
        openAddButton.addEventListener('click', () => {
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
        });
        closeAddButtons.forEach((button) => {
            button.addEventListener('click', () => {
                addModal.classList.add('hidden');
                addModal.classList.remove('flex');
            });
        });
    }

    const editModal = document.getElementById('edit-entry-modal');
    const editForm = document.getElementById('edit-entry-form');
    const openEditButtons = document.querySelectorAll('.open-edit-modal');
    const closeEditButtons = document.querySelectorAll('.close-edit-modal');

    if (editModal && editForm && openEditButtons.length) {
        const updateTemplate = editForm.getAttribute('data-update-url-template') || '';
        const editSubject = document.getElementById('edit_subject_id');
        const editTeacher = document.getElementById('edit_teacher_id');
        const editDay = document.getElementById('edit_day_of_week');
        const editStart = document.getElementById('edit_start_time');
        const editEnd = document.getElementById('edit_end_time');
        const editRoom = document.getElementById('edit_room');
        const editActive = document.getElementById('edit_is_active');

        openEditButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                if (!id || !updateTemplate) {
                    return;
                }

                editForm.setAttribute('action', updateTemplate.replace('__TIMETABLE__', id));
                if (editSubject) editSubject.value = button.getAttribute('data-subject-id') || '';
                if (editTeacher) editTeacher.value = button.getAttribute('data-teacher-id') || '';
                if (editDay) editDay.value = button.getAttribute('data-day') || 'monday';
                if (editStart) editStart.value = button.getAttribute('data-start-time') || '';
                if (editEnd) editEnd.value = button.getAttribute('data-end-time') || '';
                if (editRoom) editRoom.value = button.getAttribute('data-room') || '';
                if (editActive) editActive.checked = (button.getAttribute('data-is-active') === '1');

                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            });
        });

        closeEditButtons.forEach((button) => {
            button.addEventListener('click', () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            });
        });
    }
})();
</script>
@endpush
