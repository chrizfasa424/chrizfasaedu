@extends('layouts.app')
@section('title', 'Attendance Report')
@section('header', 'Attendance Report')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Attendance Report</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $term ? 'Term: '.$term->name.' — '.$term->session->name : 'Current term attendance summary.' }}
            </p>
        </div>
        <a href="{{ route('academic.attendance.index') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            Record Attendance
        </a>
    </div>

    {{-- Class filter --}}
    <form method="GET" action="{{ route('academic.attendance.history') }}"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                    {{ $c->grade_level?->label() ?? $c->name }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">View Report</button>
    </form>

    @if($classId && $studentStats->count())

    {{-- Class overview charts --}}
    @if($classChart)
    @php
        $chartTotal = array_sum($classChart);
        $attendanceRate = $chartTotal > 0
            ? round((($classChart['present'] + $classChart['late']) / $chartTotal) * 100)
            : 0;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Premium stat cards --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
            <h3 class="text-xs font-semibold uppercase text-slate-500 tracking-wide mb-4">Term Overview</h3>
            <div class="space-y-3">
                @foreach([
                    ['label'=>'Present/Late','value'=>$classChart['present']+$classChart['late'],'color'=>'bg-green-500','pct'=>$chartTotal>0?round(($classChart['present']+$classChart['late'])/$chartTotal*100):0],
                    ['label'=>'Absent','value'=>$classChart['absent'],'color'=>'bg-red-500','pct'=>$chartTotal>0?round($classChart['absent']/$chartTotal*100):0],
                    ['label'=>'Excused','value'=>$classChart['excused'],'color'=>'bg-slate-400','pct'=>$chartTotal>0?round($classChart['excused']/$chartTotal*100):0],
                ] as $item)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600">{{ $item['label'] }}</span>
                        <span class="font-semibold text-slate-800">{{ $item['value'] }} <span class="text-slate-400 font-normal">({{ $item['pct'] }}%)</span></span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-slate-100">
                        <div class="h-1.5 rounded-full {{ $item['color'] }}" style="width:{{ $item['pct'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <div class="text-3xl font-extrabold {{ $attendanceRate >= 90 ? 'text-green-600' : ($attendanceRate >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $attendanceRate }}%
                </div>
                <div class="text-xs text-slate-400 mt-1">Class Attendance Rate</div>
            </div>
        </div>

        {{-- Doughnut --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col items-center justify-center">
            <h3 class="text-xs font-semibold uppercase text-slate-500 tracking-wide mb-3 self-start">Attendance Split</h3>
            <canvas id="termDonut" style="max-height:200px"></canvas>
        </div>

        {{-- Bar chart per student --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-slate-500 tracking-wide mb-3">Top Absentees</h3>
            <canvas id="absentBar" style="max-height:200px"></canvas>
        </div>
    </div>
    @endif

    {{-- Student breakdown table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Student Breakdown — {{ $studentStats->count() }} students</h3>
            <span class="text-xs text-slate-400">⚠ = 10+ absences this term</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Student</th>
                        <th class="px-4 py-3 text-center">Gender</th>
                        <th class="px-4 py-3 text-center">Present</th>
                        <th class="px-4 py-3 text-center">Absent</th>
                        <th class="px-4 py-3 text-center">Late</th>
                        <th class="px-4 py-3 text-center">Excused</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Rate</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($studentStats->sortByDesc('absent') as $row)
                    @php $s = $row['student']; @endphp
                    <tr class="hover:bg-slate-50 {{ $row['warning'] ? 'bg-red-50/40' : '' }}">
                        <td class="px-5 py-3">
                            <div class="font-medium text-slate-800">
                                {{ $s->full_name }}
                                @if($row['warning'])<span class="ml-1 text-red-500 text-xs">⚠</span>@endif
                            </div>
                            <div class="text-xs text-slate-400">{{ $s->admission_number }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-xs {{ $s->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }} font-medium capitalize">
                            {{ $s->gender }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-green-700">{{ $row['present'] }}</td>
                        <td class="px-4 py-3 text-center font-semibold {{ $row['absent'] >= 10 ? 'text-red-600' : 'text-slate-700' }}">{{ $row['absent'] }}</td>
                        <td class="px-4 py-3 text-center text-amber-600">{{ $row['late'] }}</td>
                        <td class="px-4 py-3 text-center text-slate-500">{{ $row['excused'] }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $row['total'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <div class="h-1.5 w-16 rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full {{ $row['rate'] >= 90 ? 'bg-green-500' : ($row['rate'] >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         style="width:{{ $row['rate'] }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $row['rate'] >= 90 ? 'text-green-600' : ($row['rate'] >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $row['rate'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($row['warning'])
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Warning</span>
                            @elseif($row['rate'] >= 90)
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Excellent</span>
                            @elseif($row['rate'] >= 75)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Fair</span>
                            @else
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Poor</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @elseif($classId)
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No attendance records found for this class in the current term.
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        Select a class to view the attendance report.
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($classId && $classChart)
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

// Doughnut
const donutCtx = document.getElementById('termDonut');
if (donutCtx) {
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present/Late', 'Absent', 'Excused'],
            datasets: [{
                data: [
                    {{ $classChart['present'] + $classChart['late'] }},
                    {{ $classChart['absent'] }},
                    {{ $classChart['excused'] }},
                ],
                backgroundColor: ['#22c55e','#ef4444','#94a3b8'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, boxWidth: 10, font: { size: 11 } } },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8', bodyColor: '#f1f5f9',
                    padding: 10, cornerRadius: 8,
                },
            },
        },
    });
}

// Top Absentees bar chart
const absentCtx = document.getElementById('absentBar');
if (absentCtx) {
    @php
        $top = $studentStats->sortByDesc('absent')->take(8);
    @endphp
    const labels  = @json($top->map(fn($r) => $r['student']->first_name)->values());
    const absents = @json($top->map(fn($r) => $r['absent'])->values());
    const colors  = absents.map(v => v >= 10 ? '#ef4444' : v >= 5 ? '#f59e0b' : '#22c55e');

    new Chart(absentCtx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Absences',
                data: absents,
                backgroundColor: colors,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8', bodyColor: '#f1f5f9',
                    padding: 10, cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.parsed.y} absences` },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { size: 10 }, color: '#94a3b8' } },
            },
        },
    });
}
@endif
</script>
@endpush
