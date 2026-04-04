@extends('layouts.app')
@section('title', 'Academic Report')
@section('header', 'Academic Report')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Academic Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">Enrollment statistics and student distribution.</p>
    </div>

    {{-- Gender Distribution --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Gender Distribution</h3>
            @php $total = $genderDistribution->sum() ?: 1; @endphp
            <div class="space-y-3">
                @foreach($genderDistribution as $gender => $count)
                <div class="flex items-center gap-3">
                    <span class="w-16 text-sm capitalize text-slate-600">{{ $gender }}</span>
                    <div class="flex-1 h-3 rounded-full bg-slate-100">
                        <div class="h-3 rounded-full {{ $gender === 'male' ? 'bg-blue-400' : 'bg-pink-400' }}" style="width: {{ ($count / $total) * 100 }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-slate-800 w-12 text-right">{{ $count }}</span>
                    <span class="text-xs text-slate-400 w-10 text-right">{{ number_format(($count / $total) * 100, 1) }}%</span>
                </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-slate-400">Total active students: {{ $genderDistribution->sum() }}</p>
        </div>

        {{-- Enrollment Summary --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">Total Enrollment</h3>
            <p class="text-4xl font-bold text-indigo-600">{{ $enrollmentByClass->sum('count') }}</p>
            <p class="text-xs text-slate-500 mt-1">Active students across all classes</p>
            <p class="mt-3 text-xs text-slate-400">{{ $enrollmentByClass->count() }} classes active</p>
        </div>
    </div>

    {{-- Enrollment by Class --}}
    @if($enrollmentByClass->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Enrollment by Class</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Class</th>
                    <th class="px-5 py-2 text-center">Students</th>
                    <th class="px-5 py-2 text-left w-1/2">Bar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $maxClass = $enrollmentByClass->max('count') ?: 1; @endphp
                @foreach($enrollmentByClass->sortByDesc('count') as $row)
                <tr>
                    <td class="px-5 py-2 font-medium text-slate-800">{{ $row->schoolClass?->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-2 text-center font-semibold text-slate-800">{{ $row->count }}</td>
                    <td class="px-5 py-2">
                        <div class="h-2 rounded-full bg-indigo-100">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ ($row->count / $maxClass) * 100 }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
