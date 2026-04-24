@extends('layouts.app')
@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <section class="rounded-[28px] border border-[#D9E1F0] bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h3 class="text-xl font-extrabold tracking-tight text-slate-900">Audit Logs</h3>
                <p class="mt-1 text-sm text-slate-600">Track system actions, failed logins, and user activities.</p>
            </div>
            <form method="GET" action="{{ route('system.audit-logs.index') }}" class="grid w-full gap-3 sm:grid-cols-3 lg:max-w-3xl">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, email, IP, action..."
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#355AA0] focus:outline-none focus:ring-2 focus:ring-[#355AA0]/15 sm:col-span-2">
                <select name="action" class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#355AA0] focus:outline-none focus:ring-2 focus:ring-[#355AA0]/15">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::of($action)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-[#2D1D5C] px-4 py-2 text-sm font-semibold text-white hover:bg-[#24174A]">
                    Filter
                </button>
                <a href="{{ route('system.audit-logs.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </form>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                @forelse($logs as $log)
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
                    <p class="text-sm text-slate-500">No audit logs found.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
