@extends('layouts.app')
@section('title', 'Academic Overview')
@section('header', 'Academic Overview')

@php
    $primaryColor = trim((string) ($publicPage['primary_color'] ?? '#2D1D5C'));
    $secondaryColor = trim((string) ($publicPage['secondary_color'] ?? '#DFE753'));
    $parentAccountName = auth()->user()?->full_name ?? ($parent?->first_name ?? 'Parent');

    $totalChildren = $childrenData->count();
    $totalOutstanding = $childrenData->sum('outstanding_fees');
    $totalResultsCount = $childrenData->sum(fn ($d) => $d['latest_results']->count());
@endphp

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl shadow-xl" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, #3a2872 60%, #1a0f3a 100%);">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 80% at 80% 50%,rgba(223,231,83,0.10) 0%,transparent 70%);pointer-events:none;"></div>
        <div class="relative z-10 flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/50">Parent Portal</p>
                <h1 class="mt-1 text-2xl font-extrabold text-white sm:text-3xl">{{ $parentAccountName }}</h1>
                <p class="mt-2 text-sm text-white/70">Academic overview for all linked children.</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-right backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wider text-white/60">Children</p>
                <p class="mt-0.5 text-2xl font-extrabold" style="color:{{ $secondaryColor }}">{{ $totalChildren }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Children Linked</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalChildren }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recent Results</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalResultsCount }}</p>
        </div>
        <div class="col-span-2 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Outstanding Fees</p>
            <p class="mt-2 text-2xl font-extrabold {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $totalOutstanding > 0 ? 'NGN ' . number_format($totalOutstanding, 2) : 'Cleared' }}
            </p>
        </div>
    </div>

    @forelse($childrenData as $data)
        @php
            $child = $data['student'];
        @endphp
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $child->full_name }}</h2>
                    <p class="text-sm text-slate-500">
                        {{ $child->schoolClass?->name ?? 'Class not assigned' }}
                        @if($child->arm?->name)
                            • {{ $child->arm->name }}
                        @endif
                    </p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $data['outstanding_fees'] > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $data['outstanding_fees'] > 0 ? 'Outstanding Fees' : 'Fees Cleared' }}
                </span>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recent Results Loaded</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $data['latest_results']->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fee Balance</p>
                    <p class="mt-1 text-2xl font-extrabold {{ $data['outstanding_fees'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $data['outstanding_fees'] > 0 ? 'NGN ' . number_format($data['outstanding_fees'], 2) : 'Cleared' }}
                    </p>
                </div>
            </div>
        </section>
    @empty
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white py-16 text-center">
            <p class="text-sm font-semibold text-slate-400">No children linked to your account yet.</p>
            <p class="mt-1 text-xs text-slate-300">Contact the school admin to link your child profile.</p>
        </div>
    @endforelse
</div>
@endsection
