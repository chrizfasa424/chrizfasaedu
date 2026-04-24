@extends('layouts.app')
@section('title', 'Teacher Assignment')
@section('header', 'Teacher Assignment')

@section('content')
@php
    $disableTeacherSelectors = $teachers->isEmpty();
@endphp
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Teacher Assignment</h1>
            <p class="mt-0.5 text-sm text-slate-500">Assign class teachers and subject teachers for each class.</p>
        </div>
    </div>

    @if($teachers->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            No active teacher account was found. Add staff with the <span class="font-semibold">Teacher</span> role before assigning classes/subjects.
        </div>
    @endif

    <form method="POST" action="{{ route('academic.teaching-assignments.update') }}" class="space-y-5">
        @csrf

        @foreach($classes as $class)
            @php
                $classLabel = $class->grade_level?->label() ?? $class->name;
                $selectedClassTeacher = old('class_teacher_ids.' . $class->id, $class->class_teacher_id);
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $classLabel }}</h2>
                        <p class="text-xs text-slate-500">Section: {{ $class->section ?: ($class->grade_level?->section() ?? 'N/A') }}</p>
                    </div>
                    <div class="w-full lg:w-96">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Class Teacher</label>
                        <select name="class_teacher_ids[{{ $class->id }}]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $disableTeacherSelectors ? 'disabled' : '' }}>
                            <option value="">Unassigned</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ (string) $selectedClassTeacher === (string) $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-slate-700">Subject Teachers</h3>

                    @if($class->subjects->isEmpty())
                        <p class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500">
                            No subjects assigned to this class yet. Add subjects to this class first, then return here.
                        </p>
                    @else
                        <div class="mt-2 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Subject</th>
                                        <th class="px-3 py-2 text-left">Assigned Teacher</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($class->subjects as $subject)
                                        @php
                                            $selectedSubjectTeacher = old(
                                                'subject_teacher_ids.' . $class->id . '.' . $subject->id,
                                                $subject->pivot?->teacher_id
                                            );
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 font-medium text-slate-800">{{ $subject->name }}</td>
                                            <td class="px-3 py-2">
                                                <select name="subject_teacher_ids[{{ $class->id }}][{{ $subject->id }}]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $disableTeacherSelectors ? 'disabled' : '' }}>
                                                    <option value="">Unassigned</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ (string) $selectedSubjectTeacher === (string) $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow {{ $disableTeacherSelectors ? 'cursor-not-allowed bg-slate-400' : 'bg-indigo-600 hover:bg-indigo-700' }}" {{ $disableTeacherSelectors ? 'disabled' : '' }}>
                Save Teacher Assignments
            </button>
        </div>
    </form>
</div>
@endsection
