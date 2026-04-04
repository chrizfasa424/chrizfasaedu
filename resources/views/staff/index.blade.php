@extends('layouts.app')
@section('title', 'Staff')
@section('header', 'Staff')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Staff</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage all school staff members.</p>
        </div>
        <a href="{{ route('staff.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Staff
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('staff.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name..."
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-48">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Department</label>
            <input type="text" name="department" value="{{ request('department') }}" placeholder="Department"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-40">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        @if(request()->hasAny(['search','department']))
        <a href="{{ route('staff.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Department</th>
                    <th class="px-5 py-3 text-left">Designation</th>
                    <th class="px-5 py-3 text-left">Gender</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($staff as $member)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                {{ strtoupper(substr($member->user?->first_name ?? 'S', 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $member->user?->first_name }} {{ $member->user?->last_name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 capitalize">{{ $member->user?->role }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ $member->department ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $member->designation ?? '—' }}</td>
                    <td class="px-5 py-3 capitalize text-slate-600">{{ $member->gender }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }} capitalize">
                            {{ $member->status ?? 'active' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('staff.show', $member) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No staff found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $staff->links() }}</div>

</div>
@endsection
