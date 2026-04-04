@extends('layouts.app')
@section('title', 'Health Records')
@section('header', 'Health')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Health Records</h1>
            <p class="text-sm text-slate-500 mt-0.5">Student medical records and clinic visits.</p>
        </div>
        <button onclick="document.getElementById('add-record-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Record
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Student</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-left">Title</th>
                    <th class="px-5 py-3 text-left">Doctor</th>
                    <th class="px-5 py-3 text-left">Visit Date</th>
                    <th class="px-5 py-3 text-left">Follow-up</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($records as $record)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $record->student?->full_name }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 capitalize">{{ str_replace('_',' ',$record->type) }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ $record->title }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $record->doctor_name ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600 text-xs">{{ \Carbon\Carbon::parse($record->visit_date)->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $record->follow_up_date ? \Carbon\Carbon::parse($record->follow_up_date)->format('d M Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No medical records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $records->links() }}</div>

</div>

{{-- Add Record Modal --}}
<div id="add-record-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Medical Record</h2>
            <button onclick="document.getElementById('add-record-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('health.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Student ID <span class="text-red-500">*</span></label>
                    <input type="number" name="student_id" required placeholder="ID"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['checkup','clinic_visit','emergency','vaccination'] as $t)
                        <option value="{{ $t }}">{{ ucwords(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Visit Date <span class="text-red-500">*</span></label>
                    <input type="date" name="visit_date" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Doctor Name</label>
                <input type="text" name="doctor_name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Description / Notes</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-record-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
