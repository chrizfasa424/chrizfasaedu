@extends('layouts.app')
@section('title', 'Attendance Report')
@section('header', 'Attendance Report')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Attendance Report</h1>
        <p class="text-sm text-slate-500 mt-0.5">Attendance statistics by class and month.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('reports.attendance') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class ID</label>
            <input type="number" name="class_id" value="{{ request('class_id') }}" placeholder="Class ID"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-28">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
            <select name="month" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All months</option>
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
        @if(request()->hasAny(['class_id','month']))
        <a href="{{ route('reports.attendance') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    @if($data->count())
    @php
        $total = $data->sum();
        $present = $data['present'] ?? 0;
        $absent = $data['absent'] ?? 0;
        $late = $data['late'] ?? 0;
        $excused = $data['excused'] ?? 0;
        $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;
    @endphp

    {{-- Overview Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-bold text-green-600">{{ $present }}</p>
            <p class="text-xs text-slate-500 mt-1">Present</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-bold text-red-500">{{ $absent }}</p>
            <p class="text-xs text-slate-500 mt-1">Absent</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $late }}</p>
            <p class="text-xs text-slate-500 mt-1">Late</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-bold text-blue-500">{{ $excused }}</p>
            <p class="text-xs text-slate-500 mt-1">Excused</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Attendance Rate</h3>
        <div class="flex items-center gap-4">
            <div class="text-4xl font-bold text-indigo-600">{{ $rate }}%</div>
            <div class="flex-1 h-4 rounded-full bg-slate-100">
                <div class="h-4 rounded-full {{ $rate >= 80 ? 'bg-green-500' : ($rate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                    style="width: {{ $rate }}%"></div>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-400">{{ $total }} total records · {{ $present + $late }} attended</p>
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No attendance data for the selected filters.
    </div>
    @endif

</div>
@endsection
