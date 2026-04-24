<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreTimetableRequest;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classId = (int) $request->integer('class_id');
        $armId = $request->filled('arm_id') ? (int) $request->integer('arm_id') : null;

        $classes = SchoolClass::query()
            ->with('arms')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $selectedClass = $classId > 0 ? $classes->firstWhere('id', $classId) : null;
        $arms = $selectedClass?->arms?->sortBy('name')->values() ?? collect();

        $subjects = collect();
        if ($selectedClass) {
            $subjects = $selectedClass->subjects()
                ->where('subjects.is_active', true)
                ->orderBy('subjects.name')
                ->get(['subjects.id', 'subjects.name']);
        }

        $teachers = Staff::query()
            ->with('user')
            ->active()
            ->get()
            ->sortBy(function (Staff $staff) {
                $designation = strtolower(trim((string) $staff->designation));
                $name = strtolower(trim((string) $staff->full_name));

                return ($designation !== '' ? $designation : 'zzzz') . '|' . $name;
            })
            ->values();

        $session = auth()->user()->school?->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        $timetable = collect();
        if ($selectedClass) {
            $timetable = Timetable::query()
                ->with(['subject', 'teacher.user', 'arm'])
                ->where('class_id', $selectedClass->id)
                ->when(
                    $armId,
                    fn ($q) => $q->where('arm_id', $armId),
                    fn ($q) => $q->whereNull('arm_id')
                )
                ->when($session?->id, fn ($q) => $q->where('session_id', (int) $session->id))
                ->when($term?->id, fn ($q) => $q->where('term_id', (int) $term->id))
                ->orderByRaw("FIELD(LOWER(day_of_week), 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
                ->orderBy('start_time')
                ->get()
                ->groupBy(fn (Timetable $slot) => ucfirst(strtolower((string) $slot->day_of_week)));
        }

        return view('academic.timetable.index', [
            'classes' => $classes,
            'classId' => $selectedClass?->id,
            'armId' => $armId,
            'arms' => $arms,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'timetable' => $timetable,
            'currentSession' => $session,
            'currentTerm' => $term,
        ]);
    }

    public function store(StoreTimetableRequest $request)
    {
        $validated = $request->validated();
        $session = $request->user()->school?->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();

        if (!$session || !$term) {
            return back()->withErrors([
                'timetable' => 'Please set the current academic session and term before adding timetable entries.',
            ])->withInput();
        }

        Timetable::query()->create([
            'school_id' => (int) $request->user()->school_id,
            'class_id' => (int) $validated['class_id'],
            'arm_id' => !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null,
            'subject_id' => (int) $validated['subject_id'],
            'teacher_id' => !empty($validated['teacher_id']) ? (int) $validated['teacher_id'] : null,
            'session_id' => (int) $session->id,
            'term_id' => (int) $term->id,
            'day_of_week' => strtolower((string) $validated['day_of_week']),
            'start_time' => (string) $validated['start_time'],
            'end_time' => (string) $validated['end_time'],
            'room' => $request->filled('room') ? trim((string) $validated['room']) : null,
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : true,
        ]);

        return redirect()
            ->route('academic.timetable.index', [
                'class_id' => (int) $validated['class_id'],
                'arm_id' => !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null,
            ])
            ->with('success', 'Timetable entry added successfully.');
    }

    public function update(Request $request, Timetable $timetable)
    {
        abort_unless((int) $timetable->school_id === (int) $request->user()->school_id, 404);

        $validated = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'arm_id' => ['nullable', 'integer', 'exists:class_arms,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:staff,id'],
            'day_of_week' => ['required', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:80'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $class = SchoolClass::query()->with('subjects:id')->find((int) $validated['class_id']);
        if (!$class) {
            throw ValidationException::withMessages(['class_id' => 'Selected class is invalid.']);
        }

        $armId = !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null;
        if ($armId && !$class->arms()->where('id', $armId)->exists()) {
            throw ValidationException::withMessages(['arm_id' => 'Selected arm does not belong to the selected class.']);
        }

        $subjectId = (int) $validated['subject_id'];
        if (!$class->subjects->pluck('id')->map(fn ($id) => (int) $id)->contains($subjectId)) {
            throw ValidationException::withMessages(['subject_id' => 'Selected subject is not assigned to the selected class.']);
        }

        $day = strtolower(trim((string) $validated['day_of_week']));
        $startTime = (string) $validated['start_time'];
        $endTime = (string) $validated['end_time'];

        $overlap = Timetable::query()
            ->where('id', '!=', (int) $timetable->id)
            ->where('class_id', (int) $validated['class_id'])
            ->where('session_id', (int) $timetable->session_id)
            ->where('term_id', (int) $timetable->term_id)
            ->whereRaw('LOWER(day_of_week) = ?', [$day])
            ->when(
                $armId,
                fn ($q) => $q->where('arm_id', $armId),
                fn ($q) => $q->whereNull('arm_id')
            )
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_time' => 'This class timetable has an overlapping period in the selected day and scope.',
            ]);
        }

        $timetable->update([
            'class_id' => (int) $validated['class_id'],
            'arm_id' => $armId,
            'subject_id' => (int) $validated['subject_id'],
            'teacher_id' => !empty($validated['teacher_id']) ? (int) $validated['teacher_id'] : null,
            'day_of_week' => $day,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => $request->filled('room') ? trim((string) $validated['room']) : null,
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : (bool) $timetable->is_active,
        ]);

        return redirect()
            ->route('academic.timetable.index', [
                'class_id' => (int) $validated['class_id'],
                'arm_id' => $armId,
            ])
            ->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(Request $request, Timetable $timetable)
    {
        abort_unless((int) $timetable->school_id === (int) $request->user()->school_id, 404);

        $classId = (int) ($request->integer('class_id') ?: $timetable->class_id);
        $armId = $request->filled('arm_id') ? (int) $request->integer('arm_id') : ($timetable->arm_id ? (int) $timetable->arm_id : null);

        $timetable->delete();

        return redirect()
            ->route('academic.timetable.index', [
                'class_id' => $classId,
                'arm_id' => $armId,
            ])
            ->with('success', 'Timetable entry deleted.');
    }

    public function generateSample(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'arm_id' => ['nullable', 'integer', 'exists:class_arms,id'],
            'replace_existing' => ['nullable', 'boolean'],
        ]);

        $class = SchoolClass::query()->with(['subjects' => function ($query) {
            $query->where('subjects.is_active', true)->orderBy('subjects.name');
        }, 'arms'])->findOrFail((int) $validated['class_id']);

        $armId = !empty($validated['arm_id']) ? (int) $validated['arm_id'] : null;
        if ($armId && !$class->arms->pluck('id')->map(fn ($id) => (int) $id)->contains($armId)) {
            return back()->withErrors(['arm_id' => 'Selected arm does not belong to the selected class.'])->withInput();
        }

        $subjects = $class->subjects->values();
        if ($subjects->isEmpty()) {
            return back()->withErrors(['class_id' => 'No active subjects are assigned to this class. Assign subjects first.'])->withInput();
        }

        $session = $request->user()->school?->currentSession();
        $term = $session?->terms()->where('is_current', true)->first();
        if (!$session || !$term) {
            return back()->withErrors([
                'class_id' => 'Please set the current academic session and term before generating timetable.',
            ])->withInput();
        }

        $replaceExisting = (bool) ($validated['replace_existing'] ?? true);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $slots = [
            ['08:00', '08:40'],
            ['08:40', '09:20'],
            ['09:20', '10:00'],
            ['10:20', '11:00'],
            ['11:00', '11:40'],
            ['11:40', '12:20'],
            ['12:40', '13:20'],
            ['13:20', '14:00'],
        ];

        $created = 0;
        $cursor = 0;

        DB::transaction(function () use (
            $replaceExisting,
            $class,
            $armId,
            $session,
            $term,
            $days,
            $slots,
            $subjects,
            &$created,
            &$cursor
        ) {
            $scope = Timetable::query()
                ->where('class_id', (int) $class->id)
                ->where('session_id', (int) $session->id)
                ->where('term_id', (int) $term->id)
                ->when(
                    $armId,
                    fn ($q) => $q->where('arm_id', $armId),
                    fn ($q) => $q->whereNull('arm_id')
                );

            if ($replaceExisting) {
                $scope->delete();
            }

            foreach ($days as $day) {
                foreach ($slots as [$start, $end]) {
                    $subject = $subjects[$cursor % $subjects->count()];
                    $teacherId = $subject->pivot->teacher_id ?: $class->class_teacher_id;

                    Timetable::query()->create([
                        'school_id' => (int) $class->school_id,
                        'class_id' => (int) $class->id,
                        'arm_id' => $armId,
                        'subject_id' => (int) $subject->id,
                        'teacher_id' => $teacherId ? (int) $teacherId : null,
                        'session_id' => (int) $session->id,
                        'term_id' => (int) $term->id,
                        'day_of_week' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                        'room' => null,
                        'is_active' => true,
                    ]);

                    $created++;
                    $cursor++;
                }
            }
        });

        return redirect()
            ->route('academic.timetable.index', [
                'class_id' => (int) $class->id,
                'arm_id' => $armId,
            ])
            ->with('success', "Sample timetable generated successfully ({$created} periods).");
    }
}
