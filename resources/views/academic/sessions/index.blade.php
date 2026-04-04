@extends('layouts.app')
@section('title', 'Academic Sessions')
@section('header', 'Academic Sessions')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Academic Sessions</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage school sessions and their terms.</p>
        </div>
        <button onclick="document.getElementById('create-session-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Session
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Sessions Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Session</th>
                    <th class="px-5 py-3 text-left">Start</th>
                    <th class="px-5 py-3 text-left">End</th>
                    <th class="px-5 py-3 text-left">Terms</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sessions as $session)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $session->name }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ \Carbon\Carbon::parse($session->start_date)->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ \Carbon\Carbon::parse($session->end_date)->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $session->terms->count() }} term(s)</td>
                    <td class="px-5 py-3">
                        @if($session->is_current)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Current</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Past</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @unless($session->is_current)
                        <form method="POST" action="{{ route('academic.sessions.set-current', $session) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-indigo-600 hover:underline font-medium">Set Current</button>
                        </form>
                        @endunless
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-400">No sessions yet. Create one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $sessions->links() }}</div>

</div>

{{-- Create Session Modal --}}
<div id="create-session-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Create Academic Session</h2>
            <button onclick="document.getElementById('create-session-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.sessions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Session Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="e.g. 2024/2025" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <p class="text-xs text-slate-400">3 terms (First, Second, Third) will be created automatically.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-session-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
