@extends('layouts.app')
@section('title', 'Parent Portal')
@section('header', 'Parent Portal')

@php
    $primaryColor   = trim((string) ($publicPage['primary_color']   ?? '#2D1D5C'));
    $secondaryColor = trim((string) ($publicPage['secondary_color'] ?? '#DFE753'));

    $totalChildren        = $childrenData->count();
    $totalOutstanding     = $childrenData->sum('outstanding_fees');
    $totalResultsCount    = $childrenData->sum(fn ($d) => $d['latest_results']->count());

    $gradeBadge = function (string $grade): string {
        return match ($grade) {
            'A1'    => 'bg-emerald-100 text-emerald-700',
            'B2'    => 'bg-green-100 text-green-700',
            'B3'    => 'bg-teal-100 text-teal-700',
            'C4'    => 'bg-blue-100 text-blue-700',
            'C5'    => 'bg-indigo-100 text-indigo-700',
            'C6'    => 'bg-violet-100 text-violet-700',
            'D7'    => 'bg-amber-100 text-amber-700',
            'E8'    => 'bg-orange-100 text-orange-700',
            'F9'    => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    };
@endphp

@section('content')
<div class="space-y-6">

    {{-- ── WELCOME HERO ─────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl"
         style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, #3a2872 60%, #1a0f3a 100%);">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 80% at 80% 50%,rgba(223,231,83,0.10) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.05) 1px,transparent 1px);background-size:24px 24px;pointer-events:none;"></div>

        <div class="relative z-10 flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8 lg:p-10">
            <div class="flex items-center gap-5">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-2 border-white/15 text-3xl font-extrabold shadow-xl"
                     style="background:{{ $secondaryColor }};color:{{ $primaryColor }}">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($parent?->first_name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/50">Parent Portal</p>
                    <h1 class="mt-1 text-2xl font-extrabold leading-tight text-white sm:text-3xl">
                        Welcome, {{ $parent?->first_name ?? 'Parent' }}
                    </h1>
                    <p class="mt-2 text-sm text-white/60">
                        Overview of your {{ $totalChildren === 1 ? 'child' : $totalChildren . ' children' }}'s academic progress
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 sm:flex-col sm:items-end">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Children</p>
                    <p class="mt-0.5 text-2xl font-extrabold" style="color:{{ $secondaryColor }}">{{ $totalChildren }}</p>
                </div>
                @if($totalOutstanding > 0)
                <div class="rounded-2xl border border-red-300/30 bg-red-500/20 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Outstanding</p>
                    <p class="mt-0.5 text-xl font-extrabold text-red-300">₦{{ number_format($totalOutstanding, 2) }}</p>
                </div>
                @else
                <div class="rounded-2xl border border-emerald-300/30 bg-emerald-500/20 px-5 py-3 backdrop-blur text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Fees</p>
                    <p class="mt-0.5 text-lg font-extrabold text-emerald-300">All Clear</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <p class="text-sm font-medium text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ── SUMMARY STAT CARDS ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-violet-600"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $totalChildren }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Children Linked</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M7.5 16.5v-4.5M12 16.5V8.25M16.5 16.5v-6.75"/></svg>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $totalResultsCount }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Result Records</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm col-span-2 lg:col-span-1">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $totalOutstanding > 0 ? 'bg-red-50' : 'bg-emerald-50' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6 {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-emerald-600' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h12A2.25 2.25 0 0 1 20.25 7.5v9A2.25 2.25 0 0 1 18 18.75H6A2.25 2.25 0 0 1 3.75 16.5v-9Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12h3.75v3H16.5a1.5 1.5 0 1 1 0-3Z"/></svg>
            </div>
            @if($totalOutstanding > 0)
                <p class="mt-4 text-2xl font-extrabold text-red-600">₦{{ number_format($totalOutstanding, 2) }}</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">Total Outstanding</p>
            @else
                <p class="mt-4 text-xl font-extrabold text-emerald-600">Cleared</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">No Outstanding Fees</p>
            @endif
        </div>
    </div>

    {{-- ── CHILDREN CARDS ───────────────────────────────────────────────── --}}
    @forelse($childrenData as $data)
    @php $child = $data['student']; @endphp

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        {{-- Child header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4" style="background: {{ $primaryColor }}0a">
            <div class="flex items-center gap-4">
                @if($child->photo)
                    <img src="{{ asset('storage/' . ltrim($child->photo, '/')) }}"
                         alt="{{ $child->full_name }}"
                         class="h-12 w-12 rounded-2xl border border-slate-200 object-cover">
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-base font-extrabold"
                         style="background:{{ $primaryColor }}1a;color:{{ $primaryColor }}">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($child->first_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $child->full_name }}</h2>
                    <p class="text-xs text-slate-500">
                        {{ $child->admission_number ?? '' }}
                        @if($child->admission_number && $child->schoolClass) &bull; @endif
                        {{ $child->schoolClass?->name ?? 'Not Assigned' }}
                        @if($child->arm?->name) &bull; {{ $child->arm->name }} @endif
                    </p>
                </div>
            </div>

            @if($data['outstanding_fees'] > 0)
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-right">
                    <p class="text-xs font-semibold text-red-500">Outstanding</p>
                    <p class="text-sm font-extrabold text-red-700">₦{{ number_format($data['outstanding_fees'], 2) }}</p>
                </div>
            @else
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                    Fees Cleared
                </span>
            @endif
        </div>

        {{-- Results grid --}}
        @if($data['latest_results']->count())
        <div class="p-6">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400">Recent Approved Results</h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach($data['latest_results'] as $result)
                @php
                    $g = $result->grade ?? '';
                    $badge = $gradeBadge($g);
                @endphp
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3 text-center">
                    <p class="truncate text-xs font-semibold text-slate-500">{{ $result->subject?->name ?? '—' }}</p>
                    <p class="mt-2 text-xl font-extrabold text-slate-900">{{ $result->total_score ?? '—' }}</p>
                    @if($g)
                        <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $badge }}">{{ $g }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <p class="text-sm text-slate-400">No approved results yet for {{ $child->first_name }}.</p>
        </div>
        @endif
    </section>

    @empty
    <div class="rounded-3xl border border-dashed border-slate-300 bg-white py-16 text-center">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 h-10 w-10 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
        <p class="text-sm font-semibold text-slate-400">No children linked to your account yet.</p>
        <p class="mt-1 text-xs text-slate-300">Contact the school admin to link your child's profile.</p>
    </div>
    @endforelse

</div>
@endsection
