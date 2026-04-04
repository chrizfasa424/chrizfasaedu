@extends('layouts.app')
@section('title', 'Attendance')
@section('header', 'Attendance')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Student Attendance</h1>
        <p class="text-sm text-slate-500 mt-0.5">Record and view daily student attendance.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('academic.attendance.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Load</button>
    </form>

    @if($classId && $students->count())
    <form method="POST" action="{{ route('academic.attendance.store') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">
                    {{ $classes->firstWhere('id', $classId)?->name }} — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </h2>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Save Attendance</button>
            </div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Student</th>
                        <th class="px-5 py-3 text-left">Admission No.</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $i => $student)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $student->full_name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $student->admission_number }}</td>
                        <td class="px-5 py-3">
                            <input type="hidden" name="attendance[{{ $i }}][student_id]" value="{{ $student->id }}">
                            <select name="attendance[{{ $i }}][status]"
                                class="rounded-lg border border-slate-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'excused' => 'Excused'] as $val => $label)
                                <option value="{{ $val }}" {{ ($attendances[$student->id] ?? 'present') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
    @elseif($classId && $students->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No active students in this class.
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            Select a class and date to record attendance.
        </div>
    @endif

</div>
@endsection
