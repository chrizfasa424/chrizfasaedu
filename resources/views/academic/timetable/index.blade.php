@extends('layouts.app')
@section('title', 'Timetable')
@section('header', 'Timetable')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Timetable</h1>
            <p class="text-sm text-slate-500 mt-0.5">View and manage class timetables.</p>
        </div>
        @if($classId)
        <button onclick="document.getElementById('add-entry-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Entry
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Class Filter --}}
    <form method="GET" action="{{ route('academic.timetable.index') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Select Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" onchange="this.form.submit()">
                <option value="">Choose class...</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($classId && $timetable->count())
    @php $days = ['monday','tuesday','wednesday','thursday','friday']; @endphp
    <div class="space-y-4">
        @foreach($days as $day)
        @if(isset($timetable[$day]) && $timetable[$day]->count())
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700 capitalize">{{ $day }}</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-2 text-left">Time</th>
                        <th class="px-5 py-2 text-left">Subject</th>
                        <th class="px-5 py-2 text-left">Teacher</th>
                        <th class="px-5 py-2 text-left">Room</th>
                        <th class="px-5 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($timetable[$day] as $entry)
                    <tr>
                        <td class="px-5 py-2 text-slate-600">{{ \Carbon\Carbon::parse($entry->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($entry->end_time)->format('g:i A') }}</td>
                        <td class="px-5 py-2 font-medium text-slate-800">{{ $entry->subject?->name }}</td>
                        <td class="px-5 py-2 text-slate-600">{{ $entry->teacher?->user?->name ?? '—' }}</td>
                        <td class="px-5 py-2 text-slate-600">{{ $entry->room ?? '—' }}</td>
                        <td class="px-5 py-2">
                            <form method="POST" action="{{ route('academic.timetable.destroy', $entry) }}" onsubmit="return confirm('Remove this entry?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="class_id" value="{{ $classId }}">
                                <button type="submit" class="text-xs text-red-500 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endforeach
    </div>
    @elseif($classId)
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No timetable entries yet for this class.
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            Select a class to view its timetable.
        </div>
    @endif

</div>

{{-- Add Entry Modal --}}
<div id="add-entry-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Timetable Entry</h2>
            <button onclick="document.getElementById('add-entry-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.timetable.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject <span class="text-red-500">*</span></label>
                <select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select subject</option>
                    @foreach(\App\Models\Subject::orderBy('name')->get() as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Day <span class="text-red-500">*</span></label>
                <select name="day_of_week" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach(['monday','tuesday','wednesday','thursday','friday'] as $d)
                    <option value="{{ $d }}" class="capitalize">{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Room</label>
                <input type="text" name="room" placeholder="e.g. Room 12A" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-entry-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Add</button>
            </div>
        </form>
    </div>
</div>
@endsection
