@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <section class="rounded-[32px] border border-[#DCE4F2] bg-gradient-to-br from-[#F6F8FF] via-white to-[#F3F7FF] px-6 py-7 shadow-sm sm:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.34em] text-[#2D1D5C]/60">School Overview</p>
                <h3 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Welcome back to your control panel</h3>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Manage academics, admissions, payments, testimonials, and school-wide operations from one clean workspace.</p>
            </div>
            <div class="inline-flex items-center rounded-full border border-[#DFE753]/70 bg-[#DFE753]/30 px-4 py-2 text-sm font-semibold text-[#2D1D5C]">
                Single Brand School Management
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        @php
            $summaryCards = [
                ['label' => 'Total Students', 'value' => number_format($stats['total_students']), 'icon' => 'users'],
                ['label' => 'Total Staff', 'value' => number_format($stats['total_staff']), 'icon' => 'briefcase'],
                ['label' => 'Pending Admissions', 'value' => number_format($stats['pending_admissions']), 'icon' => 'inbox'],
                ['label' => 'Outstanding Fees', 'value' => '?' . number_format($stats['outstanding_fees'], 2), 'icon' => 'receipt'],
                ['label' => 'This Month Revenue', 'value' => '?' . number_format($stats['recent_payments'], 2), 'icon' => 'chart'],
                ['label' => 'Failed Logins Today', 'value' => number_format($stats['failed_logins_today']), 'icon' => 'shield'],
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
            <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#355AA0] to-[#2D1D5C] p-5 text-white shadow-lg shadow-[#2D1D5C]/15">
                <div class="absolute -bottom-8 -right-6 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="absolute right-5 top-5 text-[#8ED1FF]">
                    {!! $renderDashboardIcon($card['icon']) !!}
                </div>
                <p class="relative max-w-[11rem] text-sm font-semibold uppercase tracking-[0.12em] text-white/88">{{ $card['label'] }}</p>
                <p class="relative mt-5 text-4xl font-extrabold tracking-tight">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
            <div class="bg-[#2D1D5C] px-6 py-4 text-white">
                <h3 class="text-lg font-bold">Recent Admissions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentAdmissions as $admission)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $admission->first_name }} {{ $admission->last_name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $admission->application_number }} · {{ $admission->class_applied_for }}</p>
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
            <div class="bg-[#355AA0] px-6 py-4 text-white">
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
                        <span class="text-sm font-bold text-emerald-600">?{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No recent payments</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 bg-gradient-to-r from-[#2D1D5C] to-[#355AA0] px-6 py-4 text-white">
            <div>
                <h3 class="text-lg font-bold">Audit Log</h3>
                <p class="mt-1 text-sm text-white/80">Recent system activity including failed login attempts.</p>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($recentAuditLogs as $log)
                    @php
                        $changes = is_array($log->changes) ? $log->changes : [];
                        $isFailedLogin = $log->action === 'failed_login';
                    @endphp
                    <div class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $isFailedLogin ? 'bg-red-100 text-red-700' : 'bg-[#DFE753]/50 text-[#2D1D5C]' }}">
                                    {{ \Illuminate\Support\Str::of($log->action)->replace('_', ' ')->title() }}
                                </span>
                                <span class="text-sm font-semibold text-slate-900">
                                    {{ $log->user?->full_name ?? ($changes['email'] ?? 'System Event') }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600">
                                {{ class_basename($log->model_type) }}{{ $log->model_id ? ' #' . $log->model_id : '' }}
                                @if(!empty($changes['reason']))
                                    · {{ $changes['reason'] }}
                                @endif
                            </p>
                            <p class="text-xs text-slate-500">
                                IP: {{ $log->ip_address ?: 'Unavailable' }}
                                @if(!empty($changes['path']))
                                    · Path: /{{ ltrim($changes['path'], '/') }}
                                @endif
                            </p>
                        </div>
                        <div class="text-xs font-medium text-slate-500 md:text-right">
                            {{ $log->created_at?->format('d M Y, H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No audit logs yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
