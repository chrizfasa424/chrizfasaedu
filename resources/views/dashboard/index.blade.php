@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .dashboard-premium-shell {
        --dash-primary-700: #1e3a8a;
        --dash-primary-600: #1d4ed8;
        --dash-teal-700: #0f766e;
        --dash-teal-600: #0d9488;
        --dash-gold-700: #92400e;
        --dash-gold-600: #b45309;
        --dash-rose-700: #9f1239;
        --dash-rose-600: #be185d;
        --dash-indigo-700: #4338ca;
        --dash-indigo-600: #4f46e5;
        position: relative;
        isolation: isolate;
    }

    .dashboard-premium-shell::before {
        content: '';
        position: absolute;
        inset: -0.75rem -0.5rem -0.75rem -0.5rem;
        z-index: -1;
        border-radius: 2.2rem;
        background:
            radial-gradient(circle at 5% 10%, rgba(37, 68, 166, 0.12), transparent 38%),
            radial-gradient(circle at 96% 4%, rgba(13, 118, 110, 0.1), transparent 35%),
            radial-gradient(circle at 88% 88%, rgba(180, 83, 9, 0.12), transparent 32%);
    }

    .dashboard-premium-shell > section {
        animation: dashboard-fade-up 0.42s ease both;
    }

    .dashboard-premium-shell > section:nth-of-type(2) { animation-delay: 0.05s; }
    .dashboard-premium-shell > section:nth-of-type(3) { animation-delay: 0.09s; }
    .dashboard-premium-shell > section:nth-of-type(4) { animation-delay: 0.13s; }
    .dashboard-premium-shell > section:nth-of-type(5) { animation-delay: 0.17s; }
    .dashboard-premium-shell > section:nth-of-type(6) { animation-delay: 0.21s; }

    @keyframes dashboard-fade-up {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.995);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dashboard-hero-premium {
        border: 1px solid rgba(37, 68, 166, 0.14);
        background:
            radial-gradient(circle at top left, rgba(183, 121, 31, 0.18), transparent 42%),
            radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.16), transparent 42%),
            linear-gradient(128deg, #ffffff 0%, #f8fafc 40%, #eef4ff 100%);
        box-shadow:
            0 20px 38px -28px rgba(15, 23, 42, 0.42),
            inset 0 1px 0 rgba(255, 255, 255, 0.82);
    }

    .dashboard-chip {
        border: 1px solid rgba(37, 68, 166, 0.2);
        background: rgba(255, 255, 255, 0.78);
        box-shadow: 0 14px 28px -22px rgba(37, 68, 166, 0.36);
        backdrop-filter: blur(8px);
    }

    .dashboard-kpi-card {
        position: relative;
        overflow: hidden;
        box-shadow:
            0 18px 34px -24px rgba(15, 23, 42, 0.62),
            inset 0 1px 0 rgba(255, 255, 255, 0.28);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .dashboard-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow:
            0 28px 40px -24px rgba(15, 23, 42, 0.68),
            inset 0 1px 0 rgba(255, 255, 255, 0.34);
    }

    .dashboard-kpi-card::after {
        content: '';
        position: absolute;
        inset: auto -18% -72% auto;
        width: 72%;
        aspect-ratio: 1;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.30) 0%, rgba(255, 255, 255, 0) 72%);
        pointer-events: none;
    }

    .dashboard-panel {
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 1.8rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 252, 255, 0.98));
        box-shadow: 0 20px 36px -28px rgba(15, 23, 42, 0.42);
    }

    .dashboard-panel-head {
        position: relative;
        overflow: hidden;
    }

    .dashboard-panel-head::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.18), transparent 48%);
        pointer-events: none;
    }

    .dashboard-panel-head > span {
        border: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(255, 255, 255, 0.16);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .dashboard-feed-row {
        border: 1px solid rgba(203, 213, 225, 0.62);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 22px -20px rgba(15, 23, 42, 0.45);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-feed-row:hover {
        transform: translateY(-2px);
        border-color: rgba(30, 58, 138, 0.22);
        box-shadow: 0 18px 24px -20px rgba(30, 58, 138, 0.35);
    }

    @media (prefers-reduced-motion: reduce) {
        .dashboard-premium-shell > section,
        .dashboard-kpi-card,
        .dashboard-feed-row {
            animation: none !important;
            transition: none !important;
        }
    }
</style>
@endpush

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
<div class="dashboard-premium-shell space-y-7">
    <section class="dashboard-hero-premium rounded-[34px] px-6 py-7 sm:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.34em] text-[#1E3A8A]/65">Command Center</p>
                <h3 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-[2.15rem]">Welcome back, {{ $dashboardAccountName }}</h3>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">Your school pulse is live. Track performance, admissions, attendance, and cashflow from a single premium control surface.</p>
            </div>
            <div class="dashboard-chip inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold text-[#1E3A8A]">
                Live School Intelligence
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @php
            $summaryCards = [
                ['label' => 'Total Students', 'value' => number_format($stats['total_students']), 'icon' => 'users', 'gradient' => 'bg-gradient-to-br from-[#1E3A8A] to-[#2563EB]', 'iconColor' => 'text-[#DBEAFE]'],
                ['label' => 'Total Staff', 'value' => number_format($stats['total_staff']), 'icon' => 'briefcase', 'gradient' => 'bg-gradient-to-br from-[#0F766E] to-[#0D9488]', 'iconColor' => 'text-[#CCFBF1]'],
                ['label' => 'Pending Admissions', 'value' => number_format($stats['pending_admissions']), 'icon' => 'inbox', 'gradient' => 'bg-gradient-to-br from-[#92400E] to-[#B45309]', 'iconColor' => 'text-[#FEF3C7]'],
                ['label' => 'Failed Logins Today', 'value' => number_format($stats['failed_logins_today']), 'icon' => 'shield', 'gradient' => 'bg-gradient-to-br from-[#9F1239] to-[#BE185D]', 'iconColor' => 'text-[#FFE4E6]'],
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
            <div class="dashboard-kpi-card relative rounded-[28px] {{ $card['gradient'] }} p-5 text-white">
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
        <div class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#1E3A8A] to-[#2563EB] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Students by Class</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($classDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="h-[280px]">
                    <canvas id="classEnrollmentChart" role="img" aria-label="Bar chart showing student count by class.">Students by class chart.</canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#0F766E] to-[#14B8A6] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Staff Composition</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($staffComposition ?? [])->sum('count')) }} Staff
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[280px] max-w-[320px]">
                    <canvas id="staffCompositionChart" role="img" aria-label="Doughnut chart showing staff composition by type.">Staff composition chart.</canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#7A2335] to-[#6D3EC8] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Gender Distribution (Pie)</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($studentGenderDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[280px] max-w-[320px]">
                    <canvas id="studentGenderChart" role="img" aria-label="Pie chart showing student gender distribution.">Student gender distribution chart.</canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6">
        <div class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#8A5A14] to-[#BE3E2B] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Attendance Rate (Weekly %)</h3>
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    Last 8 Weeks
                </span>
            </div>
            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="attendanceRateChart" role="img" aria-label="Line chart showing weekly attendance rate percentage trend.">Attendance trend chart.</canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="dashboard-panel">
            <div class="dashboard-panel-head bg-gradient-to-r from-[#1E3A8A] to-[#3B82F6] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Admissions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentAdmissions as $admission)
                    <div class="dashboard-feed-row flex items-center justify-between gap-4 rounded-2xl px-4 py-4">
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

        <div class="dashboard-panel">
            <div class="dashboard-panel-head bg-gradient-to-r from-[#0F766E] to-[#0284C7] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Payments</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentPayments as $payment)
                    <div class="dashboard-feed-row flex items-center justify-between gap-4 rounded-2xl px-4 py-4">
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
        const chartTextColor = '#334155';
        const chartGridColor = 'rgba(100, 116, 139, 0.15)';

        const classCanvas = document.getElementById('classEnrollmentChart');
        if (classCanvas) {
            const classLabels = classData.map((item) => item.label);
            const classCounts = classData.map((item) => Number(item.count || 0));
            const classPalette = ['#1E3A8A', '#2563EB', '#0F766E', '#0D9488', '#92400E', '#B45309', '#9F1239', '#BE185D', '#4338CA', '#4F46E5'];
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
                            ticks: { color: chartTextColor, maxRotation: 0, minRotation: 0 },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#64748b', precision: 0 },
                            grid: { color: chartGridColor },
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
                        backgroundColor: ['#0F766E', '#B45309'],
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
                                color: chartTextColor,
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
                        backgroundColor: ['#2563EB', '#BE185D', '#4F46E5'],
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
                                color: chartTextColor,
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
            attendanceFill.addColorStop(0, 'rgba(30, 58, 138, 0.28)');
            attendanceFill.addColorStop(0.5, 'rgba(13, 148, 136, 0.18)');
            attendanceFill.addColorStop(1, 'rgba(180, 83, 9, 0.12)');

            new Chart(attendanceCanvas, {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: attendanceRates,
                        borderColor: '#1E3A8A',
                        backgroundColor: attendanceFill,
                        pointBackgroundColor: '#B45309',
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
                            ticks: { color: chartTextColor },
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
                            grid: { color: chartGridColor },
                        },
                    },
                },
            });
        }
    })();
</script>
@endpush

