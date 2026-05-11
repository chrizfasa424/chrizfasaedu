@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .dashboard-elite-shell {
        --ds-navy-900: #0f1e4d;
        --ds-navy-800: #173177;
        --ds-cyan-700: #0d9488;
        --ds-amber-700: #b45309;
        --ds-rose-700: #be185d;
        --ds-violet-700: #6d28d9;
        --ds-ink-900: #0f172a;
        --ds-ink-700: #334155;
        --ds-ink-500: #64748b;
        --ds-surface: #ffffff;
        --ds-surface-muted: #f7faff;
        --ds-border: rgba(148, 163, 184, 0.28);
        position: relative;
        isolation: isolate;
    }

    .dashboard-elite-shell::before {
        content: '';
        position: absolute;
        inset: -0.95rem -0.75rem -0.95rem -0.75rem;
        border-radius: 2.25rem;
        pointer-events: none;
        z-index: -1;
        background:
            radial-gradient(circle at 3% 6%, rgba(37, 99, 235, 0.15), transparent 34%),
            radial-gradient(circle at 96% 2%, rgba(13, 148, 136, 0.13), transparent 30%),
            radial-gradient(circle at 88% 96%, rgba(180, 83, 9, 0.12), transparent 32%);
    }

    .dashboard-hero {
        border: 1px solid rgba(37, 99, 235, 0.14);
        border-radius: 2rem;
        background:
            linear-gradient(122deg, rgba(17, 24, 39, 0.02), rgba(59, 130, 246, 0.08)),
            linear-gradient(180deg, #ffffff, #f8fbff);
        box-shadow:
            0 30px 54px -44px rgba(15, 23, 42, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .dashboard-live-pill {
        border: 1px solid rgba(37, 99, 235, 0.2);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(219, 234, 254, 0.8));
        box-shadow: 0 14px 26px -20px rgba(37, 99, 235, 0.5);
    }

    .dashboard-hero-chip {
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(255, 255, 255, 0.82);
        box-shadow: 0 10px 24px -20px rgba(15, 23, 42, 0.45);
    }

    .dashboard-hero-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .dashboard-hero-content {
        min-width: 0;
    }

    .dashboard-hero-tools {
        width: 100%;
        max-width: 22rem;
    }

    @media (min-width: 1280px) {
        .dashboard-hero-layout {
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            gap: 2rem;
        }

        .dashboard-hero-content {
            max-width: 48rem;
            flex: 1 1 auto;
        }

        .dashboard-hero-tools {
            flex: 0 0 22rem;
            margin-left: auto;
        }
    }

    .dashboard-clock-card,
    .dashboard-calendar-card {
        border: 1px solid rgba(59, 130, 246, 0.24);
        border-radius: 1rem;
        color: #fff;
        box-shadow: 0 20px 34px -26px rgba(30, 58, 138, 0.55);
    }

    .dashboard-clock-card {
        background:
            radial-gradient(circle at 80% 14%, rgba(255, 255, 255, 0.24), transparent 30%),
            linear-gradient(135deg, #1e3a8a 0%, #2563eb 52%, #06b6d4 100%);
    }

    .dashboard-calendar-card {
        background:
            radial-gradient(circle at 14% 10%, rgba(255, 255, 255, 0.2), transparent 34%),
            linear-gradient(135deg, #7c3aed 0%, #ec4899 54%, #f97316 100%);
    }

    .dashboard-analog-clock {
        position: relative;
        height: 84px;
        width: 84px;
        border-radius: 999px;
        border: 3px solid rgba(255, 255, 255, 0.64);
        background:
            radial-gradient(circle at 35% 25%, rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.08) 56%, rgba(15, 23, 42, 0.14) 100%);
        box-shadow: inset 0 4px 12px rgba(15, 23, 42, 0.28), 0 10px 18px -12px rgba(15, 23, 42, 0.58);
    }

    .dashboard-clock-hand {
        position: absolute;
        left: 50%;
        bottom: 50%;
        transform-origin: bottom center;
        border-radius: 999px;
        transform: translateX(-50%) rotate(0deg);
    }

    .dashboard-clock-hour {
        width: 4px;
        height: 23px;
        background: #ffffff;
    }

    .dashboard-clock-minute {
        width: 3px;
        height: 31px;
        background: #dbeafe;
    }

    .dashboard-clock-second {
        width: 2px;
        height: 35px;
        background: #fef08a;
    }

    .dashboard-clock-center {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: #f8fafc;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.5);
    }

    .dashboard-calendar-week {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.24rem;
        color: rgba(255, 255, 255, 0.86);
    }

    .dashboard-calendar-week > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.24rem;
    }

    .dashboard-calendar-day {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.55rem;
        height: 1.6rem;
        font-size: 0.67rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.88);
        background: rgba(255, 255, 255, 0.09);
    }

    .dashboard-calendar-day-empty {
        background: transparent;
    }

    .dashboard-calendar-day-today {
        color: #312e81;
        background: #fef08a;
        box-shadow: 0 8px 14px -10px rgba(254, 240, 138, 0.9);
    }

    .dashboard-kpi {
        position: relative;
        overflow: hidden;
        border-radius: 1.6rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow:
            0 24px 38px -28px rgba(15, 23, 42, 0.72),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .dashboard-kpi:hover {
        transform: translateY(-3px);
        box-shadow:
            0 28px 42px -24px rgba(15, 23, 42, 0.74),
            inset 0 1px 0 rgba(255, 255, 255, 0.22);
    }

    .dashboard-kpi::after {
        content: '';
        position: absolute;
        right: -16%;
        bottom: -62%;
        width: 74%;
        aspect-ratio: 1;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.27), rgba(255, 255, 255, 0));
    }

    .dashboard-kpi .metric-label {
        letter-spacing: 0.13em;
    }

    .dashboard-kpi .metric-value {
        letter-spacing: -0.025em;
    }

    .dashboard-panel {
        border-radius: 1.7rem;
        overflow: hidden;
        border: 1px solid var(--ds-border);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 251, 255, 0.98));
        box-shadow: 0 24px 42px -34px rgba(15, 23, 42, 0.55);
    }

    .dashboard-panel-head {
        position: relative;
        overflow: hidden;
    }

    .dashboard-panel-head::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(125deg, rgba(255, 255, 255, 0.2), transparent 45%);
        pointer-events: none;
    }

    .dashboard-panel-tag {
        border: 1px solid rgba(255, 255, 255, 0.24);
        background: rgba(255, 255, 255, 0.14);
    }

    .dashboard-feed-row {
        border: 1px solid rgba(203, 213, 225, 0.62);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 20px -20px rgba(15, 23, 42, 0.48);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-feed-row:hover {
        transform: translateY(-2px);
        border-color: rgba(30, 58, 138, 0.2);
        box-shadow: 0 18px 28px -20px rgba(30, 58, 138, 0.42);
    }

    .dashboard-empty {
        border: 1px dashed rgba(148, 163, 184, 0.5);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.72), rgba(241, 245, 249, 0.65));
    }

    .dashboard-anim > section {
        animation: dashboard-enter 0.42s ease both;
    }

    .dashboard-anim > section:nth-of-type(2) { animation-delay: 0.05s; }
    .dashboard-anim > section:nth-of-type(3) { animation-delay: 0.09s; }
    .dashboard-anim > section:nth-of-type(4) { animation-delay: 0.13s; }
    .dashboard-anim > section:nth-of-type(5) { animation-delay: 0.17s; }

    @keyframes dashboard-enter {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.995);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 640px) {
        .dashboard-kpi .metric-value {
            font-size: 1.85rem;
        }

        .dashboard-analog-clock {
            height: 76px;
            width: 76px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .dashboard-anim > section,
        .dashboard-kpi,
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
    $dashboardNow = now();

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

    $formatMoney = static fn ($value): string => $currencyPrefix . number_format((float) $value, 2);
    $totalStudents = (int) ($stats['total_students'] ?? 0);
    $totalStaff = (int) ($stats['total_staff'] ?? 0);
    $pendingAdmissions = (int) ($stats['pending_admissions'] ?? 0);
    $failedLogins = (int) ($stats['failed_logins_today'] ?? 0);

    $summaryCards = [
        ['label' => 'Total Students', 'value' => number_format($totalStudents), 'icon' => 'users', 'gradient' => 'linear-gradient(135deg, #172554 0%, #1D4ED8 54%, #3B82F6 100%)', 'iconColor' => '#DBEAFE'],
        ['label' => 'Total Staff', 'value' => number_format($totalStaff), 'icon' => 'briefcase', 'gradient' => 'linear-gradient(135deg, #134E4A 0%, #0F766E 54%, #14B8A6 100%)', 'iconColor' => '#CCFBF1'],
        ['label' => 'Pending Admissions', 'value' => number_format($pendingAdmissions), 'icon' => 'inbox', 'gradient' => 'linear-gradient(135deg, #78350F 0%, #92400E 54%, #D97706 100%)', 'iconColor' => '#FEF3C7'],
        ['label' => 'Open Student Queries', 'value' => number_format((int) ($stats['open_student_queries'] ?? 0)), 'icon' => 'message-alert', 'gradient' => 'linear-gradient(135deg, #312E81 0%, #4338CA 54%, #6366F1 100%)', 'iconColor' => '#E0E7FF'],
        ['label' => 'Failed Logins Today', 'value' => number_format($failedLogins), 'icon' => 'shield', 'gradient' => 'linear-gradient(135deg, #881337 0%, #9F1239 54%, #DB2777 100%)', 'iconColor' => '#FFE4E6'],
        ['label' => 'Outstanding Fees', 'value' => $formatMoney($stats['outstanding_fees'] ?? 0), 'icon' => 'wallet', 'gradient' => 'linear-gradient(135deg, #1E293B 0%, #334155 54%, #475569 100%)', 'iconColor' => '#E2E8F0'],
        ['label' => 'Students With Unpaid Fees', 'value' => number_format((int) ($stats['students_with_unpaid_fees'] ?? 0)), 'icon' => 'user-alert', 'gradient' => 'linear-gradient(135deg, #7C2D12 0%, #C2410C 54%, #F97316 100%)', 'iconColor' => '#FFEDD5'],
        ['label' => 'Payments This Month', 'value' => $formatMoney($stats['recent_payments'] ?? 0), 'icon' => 'receipt', 'gradient' => 'linear-gradient(135deg, #0F3B66 0%, #0369A1 54%, #0EA5E9 100%)', 'iconColor' => '#E0F2FE'],
    ];

    $attendanceRates = collect(data_get($attendanceTrend ?? [], 'rates', []))
        ->map(fn ($value) => (float) $value);
    $latestAttendanceRate = (float) ($attendanceRates->last() ?? 0);

    $attendanceTone = $latestAttendanceRate >= 75
        ? ['label' => 'Healthy', 'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-200']
        : ($latestAttendanceRate >= 50
            ? ['label' => 'Watch', 'classes' => 'bg-amber-50 text-amber-700 ring-amber-200']
            : ['label' => 'Critical', 'classes' => 'bg-rose-50 text-rose-700 ring-rose-200']);

    $admissionStatusTone = static function ($statusColor): string {
        return match ((string) $statusColor) {
            'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'yellow' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'orange' => 'bg-orange-50 text-orange-700 ring-orange-200',
            'red' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200',
        };
    };

    $renderDashboardIcon = static function (string $icon): string {
        return match ($icon) {
            'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5a4.5 4.5 0 1 0-9 0"/><circle cx="11.25" cy="9" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5a3.75 3.75 0 0 0-3-3.675"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 5.625a3 3 0 1 1 0 5.75"/></svg>',
            'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6V4.875A1.125 1.125 0 0 1 10.125 3.75h3.75A1.125 1.125 0 0 1 15 4.875V6"/><rect x="3.75" y="6" width="16.5" height="12.75" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5"/></svg>',
            'inbox' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25 5.4 5.325A1.5 1.5 0 0 1 6.705 4.5h10.59A1.5 1.5 0 0 1 18.6 5.325l1.65 2.925"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25h16.5V18a1.5 1.5 0 0 1-1.5 1.5h-3.621a1.5 1.5 0 0 1-1.342-.829l-.574-1.147a1.5 1.5 0 0 0-1.342-.829h-.742a1.5 1.5 0 0 0-1.342.829l-.574 1.147A1.5 1.5 0 0 1 8.871 19.5H5.25A1.5 1.5 0 0 1 3.75 18V8.25Z"/></svg>',
            'receipt' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5L6 18.75V3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25h7.5M8.25 12h7.5"/></svg>',
            'wallet' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5A2.25 2.25 0 0 1 6.75 5.25h10.5A2.25 2.25 0 0 1 19.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5v-9Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12h3"/><circle cx="15.75" cy="12" r=".75" fill="currentColor" stroke="none"/></svg>',
            'user-alert' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5a4.5 4.5 0 1 0-9 0"/><circle cx="11.25" cy="9" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25v3"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5h.01"/></svg>',
            'message-alert' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5a2.25 2.25 0 0 1 2.25 2.25v6a2.25 2.25 0 0 1-2.25 2.25h-6l-3.75 3v-3H6.75A2.25 2.25 0 0 1 4.5 12.75v-6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75h.01"/></svg>',
            'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.181c0 4.152 2.69 7.826 6.75 9.069 4.06-1.243 6.75-4.917 6.75-9.07V6L12 3.75Z"/></svg>',
            default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10"><circle cx="12" cy="12" r="8.25"/></svg>',
        };
    };
@endphp

<div class="dashboard-elite-shell dashboard-anim space-y-7">
    <section class="dashboard-hero px-6 py-7 sm:px-8 sm:py-8">
        <div class="dashboard-hero-layout">
            <div class="dashboard-hero-content space-y-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.34em] text-[#1E3A8A]/70">Executive Dashboard</p>
                <h3 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-[2.2rem]">Welcome back, {{ $dashboardAccountName }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">School intelligence is live. Monitor enrollment, staffing, attendance, revenue movement, and operational risk from one professional command surface.</p>

                <div class="grid max-w-xl gap-3 sm:grid-cols-2">
                    <div class="dashboard-live-pill rounded-2xl px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#1E3A8A]/80">Last Refresh</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $dashboardNow->format('D, d M Y - H:i') }}</p>
                    </div>
                    <div class="dashboard-live-pill rounded-2xl px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#1E3A8A]/80">Attendance Health</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ rtrim(rtrim(number_format($latestAttendanceRate, 1), '0'), '.') }}% weekly average</p>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-2.5">
                    <span class="dashboard-hero-chip inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-semibold text-slate-700">Live School Intelligence</span>
                    <span class="dashboard-hero-chip inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-semibold text-slate-700">Data Scoped by School</span>
                    <span class="inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-semibold ring-1 {{ $attendanceTone['classes'] }}">Attendance: {{ $attendanceTone['label'] }}</span>
                </div>
            </div>

            <div class="dashboard-hero-tools space-y-3">
                <div class="dashboard-clock-card px-4 py-3.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/90">School Time</p>
                    <div class="mt-2.5 flex items-center gap-3">
                        <div class="dashboard-analog-clock" aria-hidden="true">
                            <span id="dashboardClockHour" class="dashboard-clock-hand dashboard-clock-hour"></span>
                            <span id="dashboardClockMinute" class="dashboard-clock-hand dashboard-clock-minute"></span>
                            <span id="dashboardClockSecond" class="dashboard-clock-hand dashboard-clock-second"></span>
                            <span class="dashboard-clock-center"></span>
                        </div>
                        <div>
                            <p id="dashboardDigitalTime" class="text-lg font-black leading-tight text-white">--:--:--</p>
                            <p id="dashboardClockMeta" class="text-[11px] font-semibold text-white/85">Local time</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-calendar-card px-4 py-3.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/90">Calendar</p>
                    <p id="dashboardCalendarTitle" class="mt-1 text-sm font-bold text-white">--</p>
                    <div class="dashboard-calendar-week mt-2 text-center text-[10px] font-semibold uppercase tracking-[0.08em]">
                        <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                    </div>
                    <div id="dashboardCalendarGrid" class="dashboard-calendar-grid mt-1"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($summaryCards as $card)
            <article class="dashboard-kpi p-5 text-white" style="background: {{ $card['gradient'] }};">
                <div class="absolute -bottom-8 -right-6 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="absolute right-5 top-5" style="color: {{ $card['iconColor'] }};">
                    {!! $renderDashboardIcon($card['icon']) !!}
                </div>
                <p class="metric-label relative max-w-[13rem] text-xs font-semibold uppercase text-white/90">{{ $card['label'] }}</p>
                <p class="metric-value relative mt-5 text-4xl font-black">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#1E3A8A] to-[#2563EB] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Students by Class</h3>
                <span class="dashboard-panel-tag rounded-full px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($classDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="h-[300px]">
                    <canvas id="classEnrollmentChart" role="img" aria-label="Bar chart showing student count by class.">Students by class chart.</canvas>
                </div>
            </div>
        </article>

        <article class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#0F766E] to-[#14B8A6] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Staff Composition</h3>
                <span class="dashboard-panel-tag rounded-full px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($staffComposition ?? [])->sum('count')) }} Staff
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[300px] max-w-[320px]">
                    <canvas id="staffCompositionChart" role="img" aria-label="Doughnut chart showing staff composition by type.">Staff composition chart.</canvas>
                </div>
            </div>
        </article>

        <article class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#7A2335] to-[#6D3EC8] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Gender Distribution</h3>
                <span class="dashboard-panel-tag rounded-full px-3 py-1 text-xs font-semibold">
                    {{ number_format(collect($studentGenderDistribution ?? [])->sum('count')) }} Students
                </span>
            </div>
            <div class="p-6">
                <div class="mx-auto h-[300px] max-w-[320px]">
                    <canvas id="studentGenderChart" role="img" aria-label="Pie chart showing student gender distribution.">Student gender distribution chart.</canvas>
                </div>
            </div>
        </article>
    </section>

    <section>
        <article class="dashboard-panel">
            <div class="dashboard-panel-head flex items-center justify-between bg-gradient-to-r from-[#8A5A14] to-[#BE3E2B] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Attendance Rate (Weekly %)</h3>
                <span class="dashboard-panel-tag rounded-full px-3 py-1 text-xs font-semibold">Last 8 Weeks</span>
            </div>
            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="attendanceRateChart" role="img" aria-label="Line chart showing weekly attendance rate percentage trend.">Attendance trend chart.</canvas>
                </div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="dashboard-panel">
            <div class="dashboard-panel-head bg-gradient-to-r from-[#1E3A8A] to-[#3B82F6] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Admissions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentAdmissions as $admission)
                        @php
                            $statusColor = $admission->status->color();
                            $statusClasses = $admissionStatusTone($statusColor);
                            $initials = strtoupper(substr((string) $admission->first_name, 0, 1) . substr((string) $admission->last_name, 0, 1));
                        @endphp
                        <div class="dashboard-feed-row flex items-center justify-between gap-4 rounded-2xl px-4 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-xs font-bold text-[#1E3A8A]">{{ $initials !== '' ? $initials : 'NA' }}</span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ trim($admission->first_name . ' ' . $admission->last_name) }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $admission->application_number }} · {{ $admission->class_applied_for }}</p>
                                </div>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses }}">
                                {{ $admission->status->label() }}
                            </span>
                        </div>
                    @empty
                        <div class="dashboard-empty rounded-2xl px-4 py-8 text-center">
                            <p class="text-sm font-semibold text-slate-700">No recent admissions</p>
                            <p class="mt-1 text-xs text-slate-500">New applications will appear here in real time.</p>
                        </div>
                    @endforelse
                </div>
                @if(method_exists($recentAdmissions, 'hasPages') && $recentAdmissions->hasPages())
                    <div class="mt-5 border-t border-slate-200/70 pt-4">
                        {{ $recentAdmissions->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </article>

        <article class="dashboard-panel">
            <div class="dashboard-panel-head bg-gradient-to-r from-[#0F766E] to-[#0284C7] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Payments</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentPayments as $payment)
                        @php
                            $paymentName = $payment->student?->full_name ?: 'Unknown Student';
                            $paymentInitials = strtoupper(substr((string) $paymentName, 0, 1));
                        @endphp
                        <div class="dashboard-feed-row flex items-center justify-between gap-4 rounded-2xl px-4 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 text-xs font-bold text-[#0F766E]">{{ $paymentInitials }}</span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $paymentName }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $payment->payment_reference }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-emerald-600">{{ $formatMoney($payment->amount) }}</span>
                        </div>
                    @empty
                        <div class="dashboard-empty rounded-2xl px-4 py-8 text-center">
                            <p class="text-sm font-semibold text-slate-700">No recent payments</p>
                            <p class="mt-1 text-xs text-slate-500">Approved and confirmed payments will appear here.</p>
                        </div>
                    @endforelse
                </div>
                @if(method_exists($recentPayments, 'hasPages') && $recentPayments->hasPages())
                    <div class="mt-5 border-t border-slate-200/70 pt-4">
                        {{ $recentPayments->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </article>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const classData = @json($classDistribution ?? []);
        const staffData = @json($staffComposition ?? []);
        const genderData = @json($studentGenderDistribution ?? []);
        const attendanceTrend = @json($attendanceTrend ?? ['labels' => [], 'rates' => []]);
        const clockHour = document.getElementById('dashboardClockHour');
        const clockMinute = document.getElementById('dashboardClockMinute');
        const clockSecond = document.getElementById('dashboardClockSecond');
        const digitalTime = document.getElementById('dashboardDigitalTime');
        const clockMeta = document.getElementById('dashboardClockMeta');
        const calendarTitle = document.getElementById('dashboardCalendarTitle');
        const calendarGrid = document.getElementById('dashboardCalendarGrid');
        const dashboardTimeZone = 'Africa/Lagos';

        const chartTextColor = '#334155';
        const chartGridColor = 'rgba(100, 116, 139, 0.15)';

        const updateClockAndCalendar = () => {
            const now = new Date();
            const lagosParts = new Intl.DateTimeFormat('en-GB', {
                timeZone: dashboardTimeZone,
                hour12: false,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }).formatToParts(now).reduce((acc, part) => {
                if (part.type !== 'literal') {
                    acc[part.type] = part.value;
                }
                return acc;
            }, {});

            const year = Number(lagosParts.year || 0);
            const month = Math.max(0, Number(lagosParts.month || 1) - 1);
            const today = Number(lagosParts.day || 1);
            const hours = Number(lagosParts.hour || 0);
            const minutes = Number(lagosParts.minute || 0);
            const seconds = Number(lagosParts.second || 0);

            if (clockHour && clockMinute && clockSecond) {
                const hourDeg = ((hours % 12) + (minutes / 60)) * 30;
                const minuteDeg = (minutes + (seconds / 60)) * 6;
                const secondDeg = seconds * 6;

                clockHour.style.transform = `translateX(-50%) rotate(${hourDeg}deg)`;
                clockMinute.style.transform = `translateX(-50%) rotate(${minuteDeg}deg)`;
                clockSecond.style.transform = `translateX(-50%) rotate(${secondDeg}deg)`;
            }

            if (digitalTime) {
                digitalTime.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            if (clockMeta) {
                clockMeta.textContent = 'West Africa Time (Lagos)';
            }

            if (calendarTitle && calendarGrid) {
                const firstWeekday = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                calendarTitle.textContent = new Intl.DateTimeFormat('en-GB', {
                    timeZone: dashboardTimeZone,
                    month: 'long',
                    year: 'numeric',
                }).format(new Date(Date.UTC(year, month, 1)));

                const cells = [];
                for (let i = 0; i < firstWeekday; i++) {
                    cells.push('<span class="dashboard-calendar-day dashboard-calendar-day-empty"></span>');
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const isToday = day === today;
                    cells.push(`<span class="dashboard-calendar-day${isToday ? ' dashboard-calendar-day-today' : ''}">${day}</span>`);
                }

                calendarGrid.innerHTML = cells.join('');
            }
        };

        updateClockAndCalendar();
        window.setInterval(updateClockAndCalendar, 1000);

        const centerTextPlugin = {
            id: 'centerTextPlugin',
            afterDraw(chart, _args, options) {
                if (!options || !options.text) {
                    return;
                }

                const {ctx, chartArea} = chart;
                if (!chartArea) {
                    return;
                }

                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#0f172a';
                ctx.font = '700 26px "Sora", sans-serif';
                ctx.fillText(options.text, centerX, centerY - 6);
                ctx.fillStyle = '#64748b';
                ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(options.subText || '', centerX, centerY + 18);
                ctx.restore();
            },
        };

        const noDataPlugin = {
            id: 'noDataPlugin',
            afterDraw(chart, _args, options) {
                const hasData = chart.data.datasets.some((dataset) =>
                    (dataset.data || []).some((value) => Number(value) > 0)
                );

                if (hasData) {
                    return;
                }

                const {ctx, chartArea} = chart;
                if (!chartArea) {
                    return;
                }

                const x = (chartArea.left + chartArea.right) / 2;
                const y = (chartArea.top + chartArea.bottom) / 2;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 12px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(options?.message || 'No data yet', x, y);
                ctx.restore();
            },
        };

        Chart.register(centerTextPlugin, noDataPlugin);

        const classCanvas = document.getElementById('classEnrollmentChart');
        if (classCanvas) {
            const classLabels = classData.map((item) => item.label);
            const classCounts = classData.map((item) => Number(item.count || 0));
            const classPalette = ['#1E3A8A', '#2563EB', '#0F766E', '#14B8A6', '#92400E', '#D97706', '#9F1239', '#DB2777', '#4338CA', '#6D28D9'];

            new Chart(classCanvas, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Students',
                        data: classCounts,
                        borderRadius: 8,
                        borderSkipped: false,
                        backgroundColor: classCounts.map((_, index) => classPalette[index % classPalette.length]),
                        hoverBackgroundColor: classCounts.map((_, index) => classPalette[index % classPalette.length]),
                        maxBarThickness: 34,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        noDataPlugin: { message: 'No class data available' },
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
            const staffTotal = staffCounts.reduce((total, count) => total + count, 0);

            new Chart(staffCanvas, {
                type: 'doughnut',
                data: {
                    labels: staffLabels,
                    datasets: [{
                        data: staffCounts,
                        backgroundColor: ['#0F766E', '#B45309'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '64%',
                    plugins: {
                        centerTextPlugin: {
                            text: String(staffTotal),
                            subText: 'Total Staff',
                        },
                        noDataPlugin: { message: 'No staff composition data' },
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
            const genderTotal = hasGenderData ? genderCounts.reduce((sum, count) => sum + count, 0) : 0;

            new Chart(genderCanvas, {
                type: 'pie',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: hasGenderData ? genderCounts : [0, 0, 0],
                        backgroundColor: ['#2563EB', '#BE185D', '#6D28D9'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        noDataPlugin: { message: 'No gender distribution data' },
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
                        title: {
                            display: hasGenderData,
                            text: `Total: ${genderTotal} students`,
                            color: '#64748b',
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 12,
                                weight: '600',
                            },
                            padding: {
                                bottom: 8,
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
            const attendanceFill = attendanceCtx.createLinearGradient(0, 0, 0, 330);
            attendanceFill.addColorStop(0, 'rgba(30, 58, 138, 0.28)');
            attendanceFill.addColorStop(0.45, 'rgba(13, 148, 136, 0.16)');
            attendanceFill.addColorStop(1, 'rgba(180, 83, 9, 0.08)');

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
                        pointHoverRadius: 6,
                        pointHitRadius: 12,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        noDataPlugin: { message: 'No attendance records in the selected period' },
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


