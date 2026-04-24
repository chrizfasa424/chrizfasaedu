<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'arm', 'user', 'admission'])->active();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(25);
        $classes = SchoolClass::orderBy('order')->get();

        return view('academic.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::with('arms')->orderBy('order')->get();
        return view('academic.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'other_names' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'class_id' => 'required|exists:classes,id',
            'arm_id' => 'nullable|exists:class_arms,id',
            'state_of_origin' => 'nullable|string',
            'lga' => 'nullable|string',
            'religion' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'genotype' => 'nullable|string',
            'previous_school' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $schoolId = auth()->user()->school_id;
        $generatedNumber = Student::generateAdmissionNumber($schoolId);
        $validated['school_id'] = $schoolId;
        $validated['admission_number'] = $generatedNumber;
        $validated['registration_number'] = $generatedNumber;
        $validated['session_admitted'] = auth()->user()->school->currentSession()?->name;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create($validated);

        return redirect()->route('academic.students.show', $student)
            ->with('success', 'Student registered successfully.');
    }

    public function show(Student $student)
    {
        $student->load([
            'schoolClass', 'arm', 'parents', 'attendances', 'admission',
            'results.subject', 'invoices.payments', 'behaviourRecords',
            'user', 'hostelRoom', 'transportRoute',
        ]);
        return view('academic.students.show', compact('student'));
    }

    public function toggleActive(Student $student)
    {
        $user = $student->user;
        abort_if(!$user, 404, 'No portal account linked to this student.');

        $user->is_active = !$user->is_active;
        $user->save();

        $state = $user->is_active ? 'activated' : 'blocked';
        return back()->with('success', "Portal account {$state} for {$student->full_name}.");
    }

    public function resetPassword(Student $student)
    {
        $user = $student->user;
        abort_if(!$user, 404, 'No portal account linked to this student.');

        $plain = Str::random(10);
        $user->password = Hash::make($plain);
        $user->save();

        return back()
            ->with('success', "Password reset for {$student->full_name}.")
            ->with('reset_credentials', [
                'name'     => $student->full_name,
                'email'    => $user->email,
                'password' => $plain,
            ]);
    }

    public function changePassword(Request $request, Student $student)
    {
        $user = $student->user;
        abort_if(!$user, 404, 'No portal account linked to this student.');

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', "Password changed successfully for {$student->full_name}.");
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::with('arms')->orderBy('order')->get();
        return view('academic.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'class_id' => 'required|exists:classes,id',
            'arm_id' => 'nullable|exists:class_arms,id',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,graduated,transferred,expelled,withdrawn',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($validated);

        return redirect()->route('academic.students.show', $student)
            ->with('success', 'Student updated.');
    }

    public function promote(Request $request)
    {
        $validated = $request->validate([
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        Student::whereIn('id', $validated['student_ids'])
            ->update(['class_id' => $validated['to_class_id']]);

        return back()->with('success', count($validated['student_ids']) . ' students promoted.');
    }

    public function destroy(Student $student)
    {
        $user = auth()->user();

        if (!$user || (!$user->isSuperAdmin() && (int) $student->school_id !== (int) $user->school_id)) {
            abort(403, 'Unauthorized access to student record.');
        }

        $studentName = $student->full_name;

        DB::transaction(function () use ($student): void {
            $this->deleteStudentRecord($student);
        });

        return redirect()->route('academic.students.index')
            ->with('success', "{$studentName} deleted successfully.");
    }

    public function bulkDestroy(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user, 403, 'Unauthorized.');

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
        ]);

        $ids = collect($validated['student_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', 'Please select at least one student to delete.');
        }

        $query = Student::with('user')->whereIn('id', $ids->all());
        if (!$user->isSuperAdmin()) {
            $query->where('school_id', (int) $user->school_id);
        }

        $students = $query->get();
        if ($students->count() !== $ids->count()) {
            return back()->with('error', 'Some selected students could not be deleted because they are invalid or outside your school.');
        }

        DB::transaction(function () use ($students): void {
            foreach ($students as $student) {
                $this->deleteStudentRecord($student);
            }
        });

        $count = $students->count();
        return back()->with('success', $count . ' student' . ($count === 1 ? '' : 's') . ' deleted successfully.');
    }

    private function deleteStudentRecord(Student $student): void
    {
        $linkedUser = $student->user;
        $student->delete();

        if (!$linkedUser) {
            return;
        }

        $hasOtherProfiles = $linkedUser->staffProfile()->exists() || $linkedUser->parentProfile()->exists();
        if ($hasOtherProfiles) {
            $linkedUser->update(['is_active' => false]);
            return;
        }

        $linkedUser->delete();
    }
}
