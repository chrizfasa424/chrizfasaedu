@extends('layouts.app')
@section('title', 'Hostel')
@section('header', 'Hostel Management')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hostel</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage boarding houses and room allocations.</p>
        </div>
        <button onclick="document.getElementById('add-hostel-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Hostel
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($hostels as $hostel)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">{{ $hostel->name }}</h3>
                    <span class="text-xs {{ $hostel->type === 'male' ? 'text-blue-600' : 'text-pink-600' }} font-medium capitalize">{{ $hostel->type }}</span>
                </div>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                    {{ $hostel->rooms?->count() ?? 0 }} rooms
                </span>
            </div>
            <dl class="mt-4 grid grid-cols-2 gap-3 text-xs">
                <div><dt class="text-slate-500">Warden</dt><dd class="font-medium text-slate-700">{{ $hostel->warden?->user?->first_name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Capacity</dt><dd class="font-medium text-slate-700">{{ $hostel->capacity ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Fee/Term</dt><dd class="font-medium text-slate-700">{{ $hostel->fee_amount ? '₦'.number_format($hostel->fee_amount,2) : '—' }}</dd></div>
            </dl>
        </div>
        @empty
        <div class="col-span-2 rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No hostels set up yet.
        </div>
        @endforelse
    </div>

    <div>{{ $hostels->links() }}</div>

</div>

{{-- Add Hostel Modal --}}
<div id="add-hostel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Hostel</h2>
            <button onclick="document.getElementById('add-hostel-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('hostel.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Hostel Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Type <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Capacity</label>
                    <input type="number" name="capacity" min="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Fee (₦)</label>
                    <input type="number" name="fee_amount" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-hostel-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
