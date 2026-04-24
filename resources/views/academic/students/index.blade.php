@extends('layouts.app')
@section('title', 'Students')
@section('header', 'Students')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Students</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage all enrolled students.</p>
        </div>
        <a href="{{ route('academic.students.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Register Student
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('academic.students.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or admission no."
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-56">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All classes</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        @if(request()->hasAny(['search','class_id']))
        <a href="{{ route('academic.students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    @if($students->count())
    <div class="flex items-center justify-end">
        <form id="bulk-delete-form" action="{{ route('academic.students.bulk-destroy') }}" method="POST"
              onsubmit="return confirm('Delete selected students? This action cannot be undone.');">
            @csrf
            <button id="bulk-delete-btn" type="submit" disabled
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-600 hover:text-white hover:border-red-600 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-red-50 disabled:hover:text-red-700 disabled:hover:border-red-200">
                Delete Selected
            </button>
        </form>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left w-10">
                        <input id="select-all-students" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Admission No.</th>
                    <th class="px-5 py-3 text-left">Class</th>
                    <th class="px-5 py-3 text-left">Arm</th>
                    <th class="px-5 py-3 text-left">Gender</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($students as $student)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <input type="checkbox" class="student-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                               name="student_ids[]" value="{{ $student->id }}" form="bulk-delete-form">
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($student->photo)
                                <img src="{{ asset('storage/'.$student->photo) }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            @else
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">{{ strtoupper(substr($student->first_name,0,1)) }}</div>
                            @endif
                            <span class="font-medium text-slate-800">{{ $student->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-600 break-all">{{ $student->user?->email ?? $student->admission?->email ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $student->admission_number }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $student->schoolClass?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $student->arm?->name ?? '—' }}</td>
                    <td class="px-5 py-3 capitalize text-slate-600">{{ $student->gender }}</td>
                    <td class="px-5 py-3 flex items-center gap-3">
                        <a href="{{ route('academic.students.show', $student) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                        <a href="{{ route('academic.students.edit', $student) }}" class="text-xs text-slate-500 hover:underline font-medium">Edit</a>
                        <form action="{{ route('academic.students.destroy', $student) }}" method="POST"
                              onsubmit="return confirm('Delete this student record? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:underline font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $students->links() }}</div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-students');
    const checkboxes = Array.from(document.querySelectorAll('.student-checkbox'));
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    if (!bulkDeleteBtn || checkboxes.length === 0) {
        return;
    }

    const syncSelectionState = function () {
        const selectedCount = checkboxes.filter(function (checkbox) { return checkbox.checked; }).length;
        bulkDeleteBtn.disabled = selectedCount === 0;
        bulkDeleteBtn.textContent = selectedCount > 0 ? 'Delete Selected (' + selectedCount + ')' : 'Delete Selected';

        if (selectAll) {
            selectAll.checked = selectedCount > 0 && selectedCount === checkboxes.length;
            selectAll.indeterminate = selectedCount > 0 && selectedCount < checkboxes.length;
        }
    };

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
            syncSelectionState();
        });
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', syncSelectionState);
    });

    syncSelectionState();
});
</script>
@endpush
