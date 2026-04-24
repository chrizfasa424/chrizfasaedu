@extends('layouts.app')
@section('title', 'Edit Staff')
@section('header', 'Edit Staff Member')

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('staff.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Staff</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">Edit Staff Member</span>
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
            $selectedRole = old('role', (string) ($staff->user?->role?->value ?? $staff->user?->role));
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
        <form method="POST" action="{{ route('staff.update', $staff) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $staff->user?->first_name) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $staff->user?->last_name) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $staff->user?->email) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $staff->user?->phone) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @php
                            $roles = ['teacher','principal','vice_principal','accountant','librarian','nurse','driver','staff'];
                            $selectedRole = old('role', (string) ($staff->user?->role?->value ?? $staff->user?->role));
                        @endphp
                        @foreach($roles as $r)
                        <option value="{{ $r }}" {{ $selectedRole === $r ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $r)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="male" {{ old('gender', $staff->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $staff->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Department</label>
                    <input type="text" name="department" value="{{ old('department', $staff->department) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $staff->designation) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div id="teacher-assignment-section" class="space-y-4 rounded-xl border border-slate-200 bg-slate-50 p-4 {{ $isTeacherRoleSelected ? '' : 'hidden' }}">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Teacher Assignment</h3>
                    <p class="mt-1 text-xs text-slate-500">Assign class teacher and subject-teacher responsibilities for this staff record.</p>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification', $staff->qualification) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Date of Employment</label>
                    <input type="date" name="date_of_employment"
                        value="{{ old('date_of_employment', optional($staff->date_of_employment)->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Basic Salary</label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary', (float) $staff->basic_salary) }}" min="0" step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['active','on_leave','terminated','retired'] as $status)
                        <option value="{{ $status }}" {{ old('status', $staff->status ?? 'active') === $status ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('staff.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
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
