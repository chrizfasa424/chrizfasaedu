@extends('layouts.app')
@section('title', 'My Attendance')
@section('header', 'My Attendance')

@section('content')
@php
    $hasTrendData = collect($trendData['weeklyPresent'])->sum() > 0 || collect($trendData['weeklyAbsent'])->sum() > 0;
@endphp
<div class="w-full space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('student.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Dashboard</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">My Attendance</span>
    </div>

    @if($term)
    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        {{ $term->name }} &mdash; {{ $term->session->name ?? '' }}
    </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Days Present', 'value' => $stats['present'], 'color' => 'text-green-700', 'dot' => 'bg-green-500'],
                ['label' => 'Days Absent', 'value' => $stats['absent'], 'color' => 'text-red-600', 'dot' => 'bg-red-500'],
                ['label' => 'Days Late', 'value' => $stats['late'], 'color' => 'text-amber-600', 'dot' => 'bg-amber-500'],
                ['label' => 'Days Excused', 'value' => $stats['excused'], 'color' => 'text-slate-600', 'dot' => 'bg-slate-400'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm">
            <div class="mb-1.5 flex items-center justify-center gap-1.5">
                <span class="h-2 w-2 rounded-full {{ $card['dot'] }}"></span>
                <span class="text-xs text-slate-500">{{ $card['label'] }}</span>
            </div>
            <div class="text-3xl font-extrabold {{ $card['color'] }}">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="rounded-2xl border {{ $stats['absent'] >= 10 ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-white' }} p-5 shadow-sm">
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div>
                <div class="mb-1 text-xs font-semibold uppercase text-slate-500">Attendance Rate</div>
                <div class="text-5xl font-extrabold {{ $stats['rate'] >= 90 ? 'text-green-600' : ($stats['rate'] >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $stats['rate'] }}%
                </div>
                <div class="mt-3 h-2.5 w-full rounded-full bg-slate-200">
                    <div class="h-2.5 rounded-full transition-all {{ $stats['rate'] >= 90 ? 'bg-green-500' : ($stats['rate'] >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                         style="width:{{ $stats['rate'] }}%"></div>
                </div>
                <p class="mt-2 text-xs text-slate-500">{{ $stats['present'] }} present out of {{ $stats['total'] }} school days</p>
            </div>

            @if($stats['absent'] >= 10)
            <div class="h-fit rounded-xl border border-red-200 bg-red-100 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <div>
                        <div class="text-sm font-semibold text-red-700">Attendance Warning</div>
                        <div class="mt-0.5 text-xs text-red-600">You have been absent <strong>{{ $stats['absent'] }}</strong> times this term. Please improve your attendance. Your parent/guardian has been notified.</div>
                    </div>
                </div>
            </div>
            @else
            <div class="h-fit rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-semibold text-slate-700">Attendance Insight</div>
                <div class="mt-1 text-xs text-slate-500">{{ $stats['total'] - $stats['present'] }} day(s) missed</div>
                <div class="mt-0.5 text-xs text-slate-500">{{ 10 - $stats['absent'] > 0 ? (10 - $stats['absent']).' more absences before warning' : 'Warning threshold reached' }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Weekly Attendance</h3>
                <p class="mt-0.5 text-xs text-slate-400">Last 8 weeks &mdash; Present vs Absent</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>Present</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>Absent</span>
            </div>
        </div>

        @if($hasTrendData)
            <div class="h-64 md:h-72">
                <canvas id="weeklyChart" class="h-full w-full"></canvas>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                Weekly chart will appear once attendance entries are recorded.
            </div>
        @endif
    </div>

    @if($records->count())
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-700">Attendance Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Day</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($records as $record)
                    @php
                        $badgeMap = [
                            'present' => 'bg-green-100 text-green-700',
                            'absent' => 'bg-red-100 text-red-700',
                            'late' => 'bg-amber-100 text-amber-700',
                            'excused' => 'bg-slate-100 text-slate-600',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50 {{ $record->status === 'absent' ? 'bg-red-50/30' : '' }}">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $record->date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $record->date->format('l') }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex rounded-full px-3 py-0.5 text-xs font-semibold {{ $badgeMap[$record->status] ?? 'bg-slate-100 text-slate-600' }} capitalize">
                                {{ $record->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No attendance records found for this term.
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

const weeklyCanvas = document.getElementById('weeklyChart');

if (weeklyCanvas) {
    const labels = @json($trendData['weeklyLabels']);
    const present = @json($trendData['weeklyPresent']);
    const absent = @json($trendData['weeklyAbsent']);
    const ctx = weeklyCanvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Present',
                    data: present,
                    backgroundColor: 'rgba(34,197,94,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Absent',
                    data: absent,
                    backgroundColor: 'rgba(239,68,68,0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        title: items => items[0].label,
                        label: context => ` ${context.dataset.label}: ${context.parsed.y} day(s)`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#94a3b8' },
                    stacked: false,
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f8fafc', lineWidth: 1.5 },
                    ticks: { precision: 0, font: { size: 10 }, color: '#94a3b8' },
                },
            },
        },
    });
}
</script>
@endpush
