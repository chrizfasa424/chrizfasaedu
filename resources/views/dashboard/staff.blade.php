@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('header', 'Staff Dashboard')

@section('content')
<div class="space-y-6">
    <section class="rounded-3xl border border-[#DCE4F2] bg-gradient-to-br from-[#F6F8FF] via-white to-[#F3F7FF] px-6 py-7 shadow-sm sm:px-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#2D1D5C]/60">Staff Workspace</p>
        <h3 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Welcome back, {{ $user?->full_name }}</h3>
        <p class="mt-2 text-sm text-slate-600">Access your profile, track your role details, and stay updated with school operations.</p>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Department</p>
            <p class="mt-3 text-xl font-bold text-slate-900">{{ $staff?->department ?: 'Not Set' }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Designation</p>
            <p class="mt-3 text-xl font-bold text-slate-900">{{ $staff?->designation ?: 'Not Set' }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Active Students</p>
            <p class="mt-3 text-xl font-bold text-slate-900">{{ number_format($activeStudentsCount) }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Active Staff</p>
            <p class="mt-3 text-xl font-bold text-slate-900">{{ number_format($activeStaffCount) }}</p>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h4 class="text-base font-bold text-slate-900">Quick Actions</h4>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                Open My Profile
            </a>
            <a href="{{ route('messages.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Open Messages
            </a>
        </div>
    </section>
</div>
@endsection
