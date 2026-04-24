@extends('layouts.app')
@section('title', 'Fees Summary')
@section('header', 'Fees Summary')

@php
    $totalOutstanding = $childrenData->sum('outstanding_fees');
@endphp

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900">Fees Summary</h1>
        <p class="mt-1 text-sm text-slate-500">Outstanding and cleared fee status for each child.</p>
        <div class="mt-4 inline-flex rounded-2xl px-4 py-2 text-sm font-bold {{ $totalOutstanding > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
            {{ $totalOutstanding > 0 ? 'Total Outstanding: NGN ' . number_format($totalOutstanding, 2) : 'All Fees Cleared' }}
        </div>
    </div>

    @if($childrenData->isNotEmpty())
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($childrenData as $data)
                @php
                    $child = $data['student'];
                    $hasOutstanding = $data['outstanding_fees'] > 0;
                @endphp
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900">{{ $child->full_name }}</h2>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $child->schoolClass?->name ?? 'Class not assigned' }}
                        @if($child->arm?->name)
                            • {{ $child->arm->name }}
                        @endif
                    </p>

                    <div class="mt-4 rounded-2xl border px-4 py-3 {{ $hasOutstanding ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50' }}">
                        <p class="text-xs font-semibold uppercase tracking-wide {{ $hasOutstanding ? 'text-red-600' : 'text-emerald-700' }}">
                            {{ $hasOutstanding ? 'Outstanding Balance' : 'Status' }}
                        </p>
                        <p class="mt-1 text-xl font-extrabold {{ $hasOutstanding ? 'text-red-700' : 'text-emerald-700' }}">
                            {{ $hasOutstanding ? 'NGN ' . number_format($data['outstanding_fees'], 2) : 'Cleared' }}
                        </p>
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white py-16 text-center">
            <p class="text-sm font-semibold text-slate-400">No children linked to your account yet.</p>
            <p class="mt-1 text-xs text-slate-300">Contact the school admin to link your child profile.</p>
        </div>
    @endif
</div>
@endsection
