@extends('layouts.app')
@section('title', 'Transport')
@section('header', 'Transport')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Transport</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage bus routes and student transport.</p>
        </div>
        <button onclick="document.getElementById('add-route-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Route
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($routes as $route)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ $route->name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $route->description ?? 'No description' }}</p>
                </div>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">
                    {{ $route->students?->count() ?? 0 }} students
                </span>
            </div>
            <dl class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div><dt class="text-slate-500">Vehicle</dt><dd class="font-medium text-slate-700">{{ $route->vehicle_number ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Capacity</dt><dd class="font-medium text-slate-700">{{ $route->capacity ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Driver</dt><dd class="font-medium text-slate-700">{{ $route->driver?->user?->first_name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Fee</dt><dd class="font-medium text-slate-700">{{ $route->fee_amount ? '₦'.number_format($route->fee_amount,2) : '—' }}</dd></div>
            </dl>
        </div>
        @empty
        <div class="col-span-3 rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No transport routes created yet.
        </div>
        @endforelse
    </div>

    <div>{{ $routes->links() }}</div>

</div>

{{-- Add Route Modal --}}
<div id="add-route-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Route</h2>
            <button onclick="document.getElementById('add-route-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('transport.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Route Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Vehicle No.</label>
                    <input type="text" name="vehicle_number" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Capacity</label>
                    <input type="number" name="capacity" min="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Fee Amount (₦)</label>
                <input type="number" name="fee_amount" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-route-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create Route</button>
            </div>
        </form>
    </div>
</div>
@endsection
