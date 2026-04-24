@extends('layouts.app')
@section('title', 'Results and Grades')
@section('header', 'Results & Grades')

@php
    $gradeBadge = function (string $grade): string {
        return match ($grade) {
            'A1' => 'bg-emerald-100 text-emerald-700',
            'B2' => 'bg-green-100 text-green-700',
            'B3' => 'bg-teal-100 text-teal-700',
            'C4' => 'bg-blue-100 text-blue-700',
            'C5' => 'bg-indigo-100 text-indigo-700',
            'C6' => 'bg-violet-100 text-violet-700',
            'D7' => 'bg-amber-100 text-amber-700',
            'E8' => 'bg-orange-100 text-orange-700',
            'F9' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    };
@endphp

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900">Results and Grades</h1>
        <p class="mt-1 text-sm text-slate-500">Recent approved results grouped by child.</p>
    </div>

    @forelse($childrenData as $data)
        @php
            $child = $data['student'];
        @endphp
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-900">{{ $child->full_name }}</h2>
                <p class="text-sm text-slate-500">
                    {{ $child->schoolClass?->name ?? 'Class not assigned' }}
                    @if($child->arm?->name)
                        • {{ $child->arm->name }}
                    @endif
                </p>
            </div>

            @if($data['latest_results']->count())
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach($data['latest_results'] as $result)
                            @php
                                $grade = (string) ($result->grade ?? '');
                                $badge = $gradeBadge($grade);
                            @endphp
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3 text-center">
                                <p class="truncate text-xs font-semibold text-slate-500">{{ $result->subject?->name ?? 'N/A' }}</p>
                                <p class="mt-2 text-xl font-extrabold text-slate-900">{{ $result->total_score ?? 'N/A' }}</p>
                                @if($grade !== '')
                                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $badge }}">{{ $grade }}</span>
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
            <p class="text-sm font-semibold text-slate-400">No children linked to your account yet.</p>
            <p class="mt-1 text-xs text-slate-300">Contact the school admin to link your child profile.</p>
        </div>
    @endforelse
</div>
@endsection
