@extends('layouts.app')

@section('title', 'Admissions')
@section('header', 'Admission Applications')

@section('content')
@php
    $statusConfig = [
        'pending'      => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#f59e0b', 'label' => 'Pending'],
        'under_review' => ['bg' => '#e0f2fe', 'text' => '#075985', 'dot' => '#0ea5e9', 'label' => 'Under Review'],
        'screening'    => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'dot' => '#a855f7', 'label' => 'Screening'],
        'approved'     => ['bg' => '#dcfce7', 'text' => '#14532d', 'dot' => '#22c55e', 'label' => 'Approved'],
        'rejected'     => ['bg' => '#fee2e2', 'text' => '#7f1d1d', 'dot' => '#ef4444', 'label' => 'Rejected'],
        'enrolled'     => ['bg' => '#dbeafe', 'text' => '#1e3a5f', 'dot' => '#3b82f6', 'label' => 'Enrolled'],
    ];
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Total',       'value' => $stats['total'],    'bg' => '#f8fafc', 'border' => '#e2e8f0', 'color' => '#1e293b',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Pending',     'value' => $stats['pending'],  'bg' => '#fffbeb', 'border' => '#fde68a', 'color' => '#92400e',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Approved',    'value' => $stats['approved'], 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'color' => '#14532d',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Rejected',    'value' => $stats['rejected'], 'bg' => '#fef2f2', 'border' => '#fecaca', 'color' => '#7f1d1d',  'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Enrolled',    'value' => $stats['enrolled'], 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'color' => '#1e3a5f',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ] as $card)
    <div class="rounded-2xl border p-5 flex flex-col gap-2" style="background:{{ $card['bg'] }};border-color:{{ $card['border'] }};">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider" style="color:{{ $card['color'] }};opacity:0.6;">{{ $card['label'] }}</span>
            <svg class="h-5 w-5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="{{ $card['color'] }}" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
            </svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:{{ $card['color'] }};">{{ number_format($card['value']) }}</p>
    </div>
    @endforeach
</div>

{{-- Toolbar --}}
<div class="mb-5 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <form method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto" id="filterForm">
        {{-- Search --}}
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Name, app number, phone…"
                class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
        </div>
        {{-- Status filter --}}
        <select name="status" onchange="document.getElementById('filterForm').submit()"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            <option value="">All Statuses</option>
            @foreach(['pending' => 'Pending', 'under_review' => 'Under Review', 'screening' => 'Screening', 'approved' => 'Approved', 'rejected' => 'Rejected', 'enrolled' => 'Enrolled'] as $val => $lbl)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
            Search
        </button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admission.index') }}"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
            Clear
        </a>
        @endif
    </form>

    <a href="{{ route('admission.apply') }}" target="_blank"
        class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Application
    </a>
</div>

{{-- Table --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">App. No.</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Student</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hidden md:table-cell">Class Applied</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hidden lg:table-cell">Parent / Phone</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hidden sm:table-cell">Date</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($admissions as $admission)
            @php
                $sv  = $admission->status->value;
                $cfg = $statusConfig[$sv] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'dot' => '#94a3b8', 'label' => ucfirst($sv)];
            @endphp
            <tr class="group hover:bg-slate-50/70 transition-colors">
                <td class="px-5 py-4">
                    <span class="font-mono text-xs font-semibold text-slate-500">{{ $admission->application_number }}</span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                            {{ strtoupper(substr($admission->first_name, 0, 1)) }}{{ strtoupper(substr($admission->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $admission->first_name }} {{ $admission->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $admission->gender ? ucfirst($admission->gender) : '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 hidden md:table-cell text-slate-600">{{ $admission->class_applied_for }}</td>
                <td class="px-5 py-4 hidden lg:table-cell">
                    <p class="text-slate-700 font-medium">{{ $admission->parent_name }}</p>
                    <p class="text-xs text-slate-400">{{ $admission->parent_phone }}</p>
                </td>
                <td class="px-5 py-4 hidden sm:table-cell text-slate-500 text-xs">
                    {{ $admission->created_at->format('d M Y') }}
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
                          style="background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">
                        <span class="h-1.5 w-1.5 rounded-full" style="background:{{ $cfg['dot'] }};"></span>
                        {{ $cfg['label'] }}
                    </span>
                </td>
                <td class="px-5 py-4 text-right">
                    <a href="{{ route('admission.show', $admission) }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-700 transition group-hover:shadow-md">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-16 text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-400">No applications found</p>
                    @if(request()->hasAny(['search','status']))
                    <p class="text-xs text-slate-400 mt-1">Try adjusting your search or filter criteria.</p>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination footer --}}
    @if($admissions->hasPages())
    <div class="border-t border-slate-100 bg-slate-50 px-5 py-3 flex items-center justify-between gap-4">
        <p class="text-xs text-slate-500">
            Showing <strong>{{ $admissions->firstItem() }}</strong>–<strong>{{ $admissions->lastItem() }}</strong>
            of <strong>{{ $admissions->total() }}</strong> applications
        </p>
        <div class="text-sm">
            {{ $admissions->withQueryString()->links() }}
        </div>
    </div>
    @else
    <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
        <p class="text-xs text-slate-500">{{ $admissions->total() }} application{{ $admissions->total() !== 1 ? 's' : '' }}</p>
    </div>
    @endif
</div>
@endsection
