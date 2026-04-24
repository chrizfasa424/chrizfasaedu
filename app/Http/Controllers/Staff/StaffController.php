<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    private const STAFF_MANAGER_ROLES = [
        'super_admin',
        'school_admin',
        'principal',
        'vice_principal',
    ];

    private const STAFF_ROLE_OPTIONS = [
        'teacher',
        'principal',
        'vice_principal',
        'accountant',
        'librarian',
        'nurse',
        'driver',
        'staff',
    ];

    public function index(Request $request)
    {
        $this->authorizeStaffManagement();

        $query = Staff::with('user');
        $this->applySchoolScope($query);

        if ($request->filled('department')) $query->where('department', $request->department);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
        }
        $staff = $query->latest()->paginate(25);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $this->authorizeStaffManagement();

        return view('staff.create', [
            'assignmentClasses' => $this->teacherAssignmentClassesForForm(),
            'selectedClassTeacherClassIds' => [],
            'selectedSubjectAssignmentIds' => [],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeStaffManagement();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'role' => ['required', Rule::in(self::STAFF_ROLE_OPTIONS)],
            'department' => 'nullable|string',
            'designation' => 'nullable|string',
            'qualification' => 'nullable|string',
            'date_of_employment' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'gender' => 'required|in:male,female',
            'class_teacher_class_ids' => 'nullable|array',
            'class_teacher_class_ids.*' => 'nullable|integer',
            'subject_assignment_ids' => 'nullable|array',
            'subject_assignment_ids.*' => 'nullable|string',
        ]);

        $temporaryPassword = Str::password(12, true, true, true, false);

        $staff = DB::transaction(function () use ($validated, $temporaryPassword) {
            $user = User::create([
                'school_id' => auth()->user()->school_id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($temporaryPassword),
                'must_change_password' => true,
                'role' => $validated['role'],
            ]);

            $staff = Staff::create([
                'school_id' => auth()->user()->school_id,
                'user_id' => $user->id,
                'department' => $validated['department'],
                'designation' => $validated['designation'],
                'qualification' => $validated['qualification'],
                'date_of_employment' => $validated['date_of_employment'],
                'basic_salary' => $validated['basic_salary'] ?? 0,
                'gender' => $validated['gender'],
            ]);

            $this->applyTeacherAssignmentsForStaff($staff, (string) $validated['role'], $validated);

            return $staff;
        });

        return redirect()
            ->route('staff.show', $staff)
            ->with('success', 'Staff member added successfully.')
            ->with('staff_login_details', [
                'name' => trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')),
                'email' => $validated['email'],
                'password' => $temporaryPassword,
                'login_url' => route('staff.login'),
            ]);
    }

    public function edit(Staff $staff)
    {
        $this->authorizeStaffManagement();
        $this->ensureCanManageStaff($staff);

        $selectedClassTeacherClassIds = SchoolClass::query()
            ->where('school_id', (int) $staff->school_id)
            ->where('class_teacher_id', (int) $staff->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $selectedSubjectAssignmentIds = DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', (int) $staff->school_id)
            ->where('class_subject.teacher_id', (int) $staff->id)
            ->get(['class_subject.class_id', 'class_subject.subject_id'])
            ->map(fn ($row) => ((int) $row->class_id) . ':' . ((int) $row->subject_id))
            ->values()
            ->all();

        return view('staff.edit', [
            'staff' => $staff,
            'assignmentClasses' => $this->teacherAssignmentClassesForForm(),
            'selectedClassTeacherClassIds' => $selectedClassTeacherClassIds,
            'selectedSubjectAssignmentIds' => $selectedSubjectAssignmentIds,
        ]);
    }

    public function show(Staff $staff)
    {
        $this->authorizeStaffManagement();
        $this->ensureCanManageStaff($staff);

        $staff->load(['user', 'classTeaching', 'subjectsTeaching', 'attendances', 'salary']);
        return view('staff.show', compact('staff'));
    }

    public function resetPassword(Staff $staff)
    {
        $this->authorizeStaffManagement();
        $this->ensureCanManageStaff($staff);

        $staff->loadMissing('user');

        if (!$staff->user) {
            return back()->with('error', 'Staff login account was not found.');
        }

        $temporaryPassword = Str::password(12, true, true, true, false);

        $staff->user->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('staff.show', $staff)
            ->with('success', 'Staff password reset successfully. Share the new temporary password below.')
            ->with('staff_login_details', [
                'name' => trim(($staff->user->first_name ?? '') . ' ' . ($staff->user->last_name ?? '')),
                'email' => $staff->user->email,
                'password' => $temporaryPassword,
                'login_url' => route('staff.login'),
            ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $this->authorizeStaffManagement();
        $this->ensureCanManageStaff($staff);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($staff->user_id)],
            'phone' => 'required|string|max:30',
            'role' => ['required', Rule::in(self::STAFF_ROLE_OPTIONS)],
            'gender' => 'required|in:male,female',
            'department' => 'nullable|string',
            'designation' => 'nullable|string',
            'qualification' => 'nullable|string',
            'date_of_employment' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,on_leave,terminated,retired',
            'class_teacher_class_ids' => 'nullable|array',
            'class_teacher_class_ids.*' => 'nullable|integer',
            'subject_assignment_ids' => 'nullable|array',
            'subject_assignment_ids.*' => 'nullable|string',
        ]);

        DB::transaction(function () use ($staff, $validated): void {
            $staff->user()->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => $validated['role'],
            ]);

            $staff->update([
                'department' => $validated['department'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'date_of_employment' => $validated['date_of_employment'] ?? null,
                'basic_salary' => $validated['basic_salary'] ?? 0,
                'gender' => $validated['gender'],
                'status' => $validated['status'],
            ]);

            $this->applyTeacherAssignmentsForStaff($staff, (string) $validated['role'], $validated);
        });

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $this->authorizeStaffManagement();
        $this->ensureCanManageStaff($staff);

        if ((int) auth()->id() === (int) $staff->user_id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($staff): void {
            $this->deleteStaffRecord($staff);
        });

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorizeStaffManagement();

        $validated = $request->validate([
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'integer',
        ]);

        $ids = collect($validated['staff_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', 'Please select at least one staff member to delete.');
        }

        $query = Staff::with('user')->whereIn('id', $ids->all());
        $this->applySchoolScope($query);
        $staffMembers = $query->get();

        if ($staffMembers->isEmpty()) {
            return back()->with('error', 'No valid staff members were found for deletion.');
        }

        $deletedCount = 0;
        $skippedSelfCount = 0;

        DB::transaction(function () use ($staffMembers, &$deletedCount, &$skippedSelfCount): void {
            foreach ($staffMembers as $staff) {
                if ((int) auth()->id() === (int) $staff->user_id) {
                    $skippedSelfCount++;
                    continue;
                }

                $this->deleteStaffRecord($staff);
                $deletedCount++;
            }
        });

        if ($deletedCount === 0 && $skippedSelfCount > 0) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $parts = [];
        if ($deletedCount > 0) {
            $parts[] = $deletedCount . ' staff member' . ($deletedCount === 1 ? '' : 's') . ' deleted successfully';
        }

        $missingCount = $ids->count() - $staffMembers->count();
        if ($missingCount > 0) {
            $parts[] = $missingCount . ' selection' . ($missingCount === 1 ? '' : 's') . ' ignored (not found or not accessible)';
        }

        if ($skippedSelfCount > 0) {
            $parts[] = $skippedSelfCount . ' skipped (your own account)';
        }

        return back()->with('success', implode('. ', $parts) . '.');
    }

    private function ensureCanManageStaff(Staff $staff): void
    {
        $user = auth()->user();

        if ($user?->isSuperAdmin()) {
            return;
        }

        abort_if((int) $staff->school_id !== (int) ($user?->school_id ?? 0), 403, 'Unauthorized access to staff record.');
    }

    private function authorizeStaffManagement(): void
    {
        $user = auth()->user();
        $roleValue = (string) ($user?->role?->value ?? $user?->role ?? '');

        abort_unless(in_array($roleValue, self::STAFF_MANAGER_ROLES, true), 403, 'You are not authorized to manage staff records.');
    }

    private function applySchoolScope($query): void
    {
        $user = auth()->user();

        if (!$user || $user->isSuperAdmin()) {
            return;
        }

        $query->where('school_id', (int) $user->school_id);
    }

    private function deleteStaffRecord(Staff $staff): void
    {
        $staff->delete();

        if ($staff->user) {
            $staff->user->delete();
        }
    }

    private function teacherAssignmentClassesForForm()
    {
        $schoolId = (int) (auth()->user()->school_id ?? 0);

        return SchoolClass::query()
            ->where('school_id', $schoolId)
            ->with([
                'subjects' => fn ($query) => $query
                    ->where('subjects.is_active', true)
                    ->orderBy('subjects.name')
                    ->select('subjects.id', 'subjects.name'),
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'school_id', 'name', 'grade_level', 'section', 'class_teacher_id']);
    }

    private function applyTeacherAssignmentsForStaff(Staff $staff, string $role, array $payload): void
    {
        $schoolId = (int) $staff->school_id;
        $staffId = (int) $staff->id;
        if ($schoolId <= 0 || $staffId <= 0) {
            return;
        }

        $classes = SchoolClass::query()
            ->where('school_id', $schoolId)
            ->with(['subjects:id'])
            ->get(['id']);

        $validClassIds = $classes->pluck('id')->map(fn ($id) => (int) $id)->all();
        $validClassIdMap = array_fill_keys($validClassIds, true);

        $validSubjectsPerClass = $classes->mapWithKeys(function (SchoolClass $class) {
            return [(int) $class->id => $class->subjects->pluck('id')->map(fn ($id) => (int) $id)->all()];
        })->all();

        $selectedClassTeacherClassIds = collect((array) ($payload['class_teacher_class_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        foreach ($selectedClassTeacherClassIds as $classId) {
            if (!isset($validClassIdMap[$classId])) {
                throw ValidationException::withMessages([
                    'class_teacher_class_ids' => 'One or more selected class teacher assignments are invalid.',
                ]);
            }
        }

        $selectedSubjectAssignments = collect((array) ($payload['subject_assignment_ids'] ?? []))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn (string $value) => $value !== '')
            ->unique()
            ->values()
            ->all();

        $subjectPairs = [];
        foreach ($selectedSubjectAssignments as $value) {
            if (!preg_match('/^(?<class>\d+):(?<subject>\d+)$/', $value, $matches)) {
                throw ValidationException::withMessages([
                    'subject_assignment_ids' => 'One or more subject assignments are invalid.',
                ]);
            }

            $classId = (int) $matches['class'];
            $subjectId = (int) $matches['subject'];

            if (!isset($validClassIdMap[$classId])) {
                throw ValidationException::withMessages([
                    'subject_assignment_ids' => 'One or more selected subject assignments are invalid.',
                ]);
            }

            $allowedSubjects = array_fill_keys($validSubjectsPerClass[$classId] ?? [], true);
            if (!isset($allowedSubjects[$subjectId])) {
                throw ValidationException::withMessages([
                    'subject_assignment_ids' => 'One or more selected class-subject assignments are invalid.',
                ]);
            }

            $subjectPairs[] = [
                'class_id' => $classId,
                'subject_id' => $subjectId,
            ];
        }

        $schoolClassIds = array_keys($validClassIdMap);

        SchoolClass::query()
            ->where('school_id', $schoolId)
            ->where('class_teacher_id', $staffId)
            ->update(['class_teacher_id' => null]);

        if ($role !== 'teacher') {
            if (!empty($schoolClassIds)) {
                DB::table('class_subject')
                    ->whereIn('class_id', $schoolClassIds)
                    ->where('teacher_id', $staffId)
                    ->update([
                        'teacher_id' => null,
                        'updated_at' => now(),
                    ]);
            }

            return;
        }

        if (!empty($selectedClassTeacherClassIds)) {
            SchoolClass::query()
                ->where('school_id', $schoolId)
                ->whereIn('id', $selectedClassTeacherClassIds)
                ->update(['class_teacher_id' => $staffId]);
        }

        if (!empty($schoolClassIds)) {
            DB::table('class_subject')
                ->whereIn('class_id', $schoolClassIds)
                ->where('teacher_id', $staffId)
                ->update([
                    'teacher_id' => null,
                    'updated_at' => now(),
                ]);
        }

        foreach ($subjectPairs as $pair) {
            DB::table('class_subject')
                ->where('class_id', $pair['class_id'])
                ->where('subject_id', $pair['subject_id'])
                ->update([
                    'teacher_id' => $staffId,
                    'updated_at' => now(),
                ]);
        }
    }
}
