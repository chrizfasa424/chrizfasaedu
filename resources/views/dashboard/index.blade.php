@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
@php
    $dashboardSchoolSettings = auth()->user()?->school?->settings ?? [];
    $dashboardAccountName = auth()->user()?->full_name ?? 'Account';
    $currencySymbol = trim((string) data_get(
        $dashboardSchoolSettings,
        'currency_symbol',
        config('ems.currency_symbol', 'NGN')
    ));
    if ($currencySymbol === '') {
        $currencySymbol = 'NGN';
    }
    $currencyPrefix = preg_match('/^[A-Za-z]{3}$/', $currencySymbol)
        ? strtoupper($currencySymbol) . ' '
        : $currencySymbol;
@endphp
<div class="space-y-6">
    <section class="rounded-[32px] border border-[#DCE4F2] bg-gradient-to-br from-[#F6F8FF] via-white to-[#F3F7FF] px-6 py-7 shadow-sm sm:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.34em] text-[#2D1D5C]/60">School Overview</p>
                <h3 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Welcome back, {{ $dashboardAccountName }}</h3>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Manage academics, admissions, payments, testimonials, and school-wide operations from one clean workspace.</p>
            </div>
            <div class="inline-flex items-center rounded-full border border-[#DFE753]/70 bg-[#DFE753]/30 px-4 py-2 text-sm font-semibold text-[#2D1D5C]">
                Single Brand School Management
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @php
            $summaryCards = [
                ['label' => 'Total Students', 'value' => number_format($stats['total_students']), 'icon' => 'users', 'gradient' => 'bg-gradient-to-br from-fuchsia-500 to-indigo-600', 'iconColor' => 'text-[#FFE082]'],
                ['label' => 'Total Staff', 'value' => number_format($stats['total_staff']), 'icon' => 'briefcase', 'gradient' => 'bg-gradient-to-br from-emerald-500 to-cyan-600', 'iconColor' => 'text-[#FFF59D]'],
                ['label' => 'Pending Admissions', 'value' => number_format($stats['pending_admissions']), 'icon' => 'inbox', 'gradient' => 'bg-gradient-to-br from-amber-500 to-orange-600', 'iconColor' => 'text-[#FFF8E1]'],
                ['label' => 'Failed Logins Today', 'value' => number_format($stats['failed_logins_today']), 'icon' => 'shield', 'gradient' => 'bg-gradient-to-br from-rose-500 to-pink-600', 'iconColor' => 'text-[#FFECB3]'],
            ];

            $renderDashboardIcon = function (string $icon): string {
                return match ($icon) {
                    'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5a4.5 4.5 0 1 0-9 0"/><circle cx="11.25" cy="9" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5a3.75 3.75 0 0 0-3-3.675"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 5.625a3 3 0 1 1 0 5.75"/></svg>',
                    'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6V4.875A1.125 1.125 0 0 1 10.125 3.75h3.75A1.125 1.125 0 0 1 15 4.875V6"/><rect x="3.75" y="6" width="16.5" height="12.75" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5"/></svg>',
                    'inbox' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25 5.4 5.325A1.5 1.5 0 0 1 6.705 4.5h10.59A1.5 1.5 0 0 1 18.6 5.325l1.65 2.925"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25h16.5V18a1.5 1.5 0 0 1-1.5 1.5h-3.621a1.5 1.5 0 0 1-1.342-.829l-.574-1.147a1.5 1.5 0 0 0-1.342-.829h-.742a1.5 1.5 0 0 0-1.342.829l-.574 1.147A1.5 1.5 0 0 1 8.871 19.5H5.25A1.5 1.5 0 0 1 3.75 18V8.25Z"/></svg>',
                    'receipt' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5L6 18.75V3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25h7.5M8.25 12h7.5"/></svg>',
                    'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 16.5 10.5 12l3 2.25 3.75-5.25"/><circle cx="6.75" cy="16.5" r=".75" fill="currentColor" stroke="none"/><circle cx="10.5" cy="12" r=".75" fill="currentColor" stroke="none"/><circle cx="13.5" cy="14.25" r=".75" fill="currentColor" stroke="none"/><circle cx="17.25" cy="9" r=".75" fill="currentColor" stroke="none"/></svg>',
                    'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.181c0 4.152 2.69 7.826 6.75 9.069 4.06-1.243 6.75-4.917 6.75-9.07V6L12 3.75Z"/></svg>',
                    default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><circle cx="12" cy="12" r="8.25"/></svg>',
                };
            };
        @endphp

        @foreach($summaryCards as $card)
            <div class="relative overflow-hidden rounded-[28px] {{ $card['gradient'] }} p-5 text-white shadow-lg shadow-[#2D1D5C]/15">
                <div class="absolute -bottom-8 -right-6 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="absolute right-5 top-5 {{ $card['iconColor'] }}">
                    {!! $renderDashboardIcon($card['icon']) !!}
                </div>
                <p class="relative max-w-[11rem] text-sm font-semibold uppercase tracking-[0.12em] text-white/88">{{ $card['label'] }}</p>
                <p class="relative mt-5 text-4xl font-extrabold tracking-tight">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="flex items-center justify-between bg-gradient-to-r from-indigo-600 to-sky-500 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Students by Class</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($classDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="h-[280px]">
                    <canvas id="classEnrollmentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="flex items-center justify-between bg-gradient-to-r from-emerald-500 to-cyan-500 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Staff Composition</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($staffComposition ?? [])->sum('count')) }} Staff
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[280px] max-w-[320px]">
                    <canvas id="staffCompositionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="flex items-center justify-between bg-gradient-to-r from-pink-500 to-violet-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Gender Distribution (Pie)</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($studentGenderDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[280px] max-w-[320px]">
                    <canvas id="studentGenderChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6">
        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="flex items-center justify-between bg-gradient-to-r from-amber-500 to-rose-500 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Attendance Rate (Weekly %)</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    Last 8 Weeks
                </span>
            </div>
            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="attendanceRateChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Admissions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentAdmissions as $admission)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $admission->first_name }} {{ $admission->last_name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $admission->application_number }} &middot; {{ $admission->class_applied_for }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-{{ $admission->status->color() }}-100 text-{{ $admission->status->color() }}-700">
                            {{ $admission->status->label() }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No recent admissions</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="bg-gradient-to-r from-teal-500 to-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Payments</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $payment->student?->full_name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $payment->payment_reference }}</p>
                        </div>
                        <span class="text-sm font-bold text-emerald-600">{{ $currencyPrefix }}{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No recent payments</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (() => {
        const classData = @json($classDistribution ?? []);
        const staffData = @json($staffComposition ?? []);
        const genderData = @json($studentGenderDistribution ?? []);
        const attendanceTrend = @json($attendanceTrend ?? ['labels' => [], 'rates' => []]);

        const classCanvas = document.getElementById('classEnrollmentChart');
        if (classCanvas) {
            const classLabels = classData.map((item) => item.label);
            const classCounts = classData.map((item) => Number(item.count || 0));
            const classPalette = ['#6366F1', '#EC4899', '#10B981', '#F59E0B', '#06B6D4', '#8B5CF6', '#EF4444', '#22C55E', '#14B8A6', '#F97316'];
            const classBarColors = classCounts.map((_, index) => classPalette[index % classPalette.length]);

            new Chart(classCanvas, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Students',
                        data: classCounts,
                        borderRadius: 8,
                        borderSkipped: false,
                        backgroundColor: classBarColors,
                        hoverBackgroundColor: classBarColors,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.parsed.y ?? 0} student(s)`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: { color: '#334155', maxRotation: 0, minRotation: 0 },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#64748b', precision: 0 },
                            grid: { color: 'rgba(100, 116, 139, 0.15)' },
                        },
                    },
                },
            });
        }

        const staffCanvas = document.getElementById('staffCompositionChart');
        if (staffCanvas) {
            const staffLabels = staffData.map((item) => item.label);
            const staffCounts = staffData.map((item) => Number(item.count || 0));

            new Chart(staffCanvas, {
                type: 'doughnut',
                data: {
                    labels: staffLabels,
                    datasets: [{
                        data: staffCounts,
                        backgroundColor: ['#14B8A6', '#F97316'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#334155',
                                usePointStyle: true,
                                boxWidth: 10,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${ctx.parsed} staff`,
                            },
                        },
                    },
                },
            });
        }

        const genderCanvas = document.getElementById('studentGenderChart');
        if (genderCanvas) {
            const genderLabels = genderData.map((item) => item.label);
            const genderCounts = genderData.map((item) => Number(item.count || 0));
            const hasGenderData = genderCounts.some((count) => count > 0);

            new Chart(genderCanvas, {
                type: 'pie',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: hasGenderData ? genderCounts : [1, 1, 1],
                        backgroundColor: ['#3B82F6', '#EC4899', '#A855F7'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#334155',
                                usePointStyle: true,
                                boxWidth: 10,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${hasGenderData ? ctx.parsed : 0} student(s)`,
                            },
                        },
                    },
                },
            });
        }

        const attendanceCanvas = document.getElementById('attendanceRateChart');
        if (attendanceCanvas) {
            const attendanceLabels = attendanceTrend.labels || [];
            const attendanceRates = (attendanceTrend.rates || []).map((value) => Number(value || 0));
            const attendanceCtx = attendanceCanvas.getContext('2d');
            const attendanceFill = attendanceCtx.createLinearGradient(0, 0, 0, 320);
            attendanceFill.addColorStop(0, 'rgba(59, 130, 246, 0.30)');
            attendanceFill.addColorStop(0.5, 'rgba(168, 85, 247, 0.20)');
            attendanceFill.addColorStop(1, 'rgba(249, 115, 22, 0.10)');

            new Chart(attendanceCanvas, {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: attendanceRates,
                        borderColor: '#8B5CF6',
                        backgroundColor: attendanceFill,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.parsed.y ?? 0}%`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: { color: '#334155' },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: {
                                color: '#64748b',
                                callback: (value) => `${value}%`,
                            },
                            grid: { color: 'rgba(100, 116, 139, 0.15)' },
                        },
                    },
                },
            });
        }
    })();
</script>
@endpush

