@extends('layouts.app')
@section('title', 'Add Staff')
@section('header', 'Add Staff Member')

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('staff.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Staff</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">New Staff Member</span>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @php
            $selectedRole = (string) old('role', '');
            $isTeacherRoleSelected = $selectedRole === 'teacher';
            $selectedClassTeacherClassIds = collect(old('class_teacher_class_ids', $selectedClassTeacherClassIds ?? []))
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values()
                ->all();
            $selectedSubjectAssignmentIds = collect(old('subject_assignment_ids', $selectedSubjectAssignmentIds ?? []))
                ->map(fn ($value) => trim((string) $value))
                ->filter(fn (string $value) => $value !== '')
                ->values()
                ->all();
        @endphp
        <form method="POST" action="{{ route('staff.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select role</option>
                        @foreach(['teacher','principal','vice_principal','accountant','librarian','nurse','driver','staff'] as $r)
                        <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div id="teacher-assignment-section" class="space-y-4 rounded-xl border border-slate-200 bg-slate-50 p-4 {{ $isTeacherRoleSelected ? '' : 'hidden' }}">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Teacher Assignment</h3>
                    <p class="mt-1 text-xs text-slate-500">Assign class teacher and subject-teacher responsibilities while creating this staff record.</p>
                </div>

                @if($assignmentClasses->isEmpty())
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        No classes are available yet for assignment.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($assignmentClasses as $schoolClass)
                            @php
                                $classLabel = $schoolClass->grade_level?->label() ?? $schoolClass->name;
                            @endphp
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-800">
                                    <input
                                        type="checkbox"
                                        name="class_teacher_class_ids[]"
                                        value="{{ $schoolClass->id }}"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        data-teacher-assignment-input
                                        {{ in_array((int) $schoolClass->id, $selectedClassTeacherClassIds, true) ? 'checked' : '' }}
                                    >
                                    Class Teacher: {{ $classLabel }}
                                </label>

                                @if($schoolClass->subjects->isNotEmpty())
                                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($schoolClass->subjects as $subject)
                                            @php
                                                $subjectAssignmentValue = (int) $schoolClass->id . ':' . (int) $subject->id;
                                            @endphp
                                            <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                                                <input
                                                    type="checkbox"
                                                    name="subject_assignment_ids[]"
                                                    value="{{ $subjectAssignmentValue }}"
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                    data-teacher-assignment-input
                                                    {{ in_array($subjectAssignmentValue, $selectedSubjectAssignmentIds, true) ? 'checked' : '' }}
                                                >
                                                Subject: {{ $subject->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-2 text-xs text-slate-500">No subjects assigned to this class yet.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Date of Employment</label>
                    <input type="date" name="date_of_employment" value="{{ old('date_of_employment') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Basic Salary (NGN)</label>
                <input type="number" name="basic_salary" value="{{ old('basic_salary') }}" min="0" step="0.01"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <p class="text-xs text-slate-400">A secure temporary password will be auto-generated. The staff member will be required to change it on first login.</p>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-2">
                <a href="{{ route('staff.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Add Staff</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
(() => {
    const roleSelect = document.querySelector('select[name="role"]');
    const teacherAssignmentSection = document.getElementById('teacher-assignment-section');

    if (!roleSelect || !teacherAssignmentSection) {
        return;
    }

    const assignmentInputs = teacherAssignmentSection.querySelectorAll('[data-teacher-assignment-input]');

    const toggleTeacherAssignment = () => {
        const isTeacher = roleSelect.value === 'teacher';
        teacherAssignmentSection.classList.toggle('hidden', !isTeacher);
        assignmentInputs.forEach((input) => {
            input.disabled = !isTeacher;
        });
    };

    roleSelect.addEventListener('change', toggleTeacherAssignment);
    toggleTeacherAssignment();
})();
</script>
@endpush
