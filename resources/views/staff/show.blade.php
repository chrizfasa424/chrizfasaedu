@extends('layouts.app')
@section('title', ($staff->user?->first_name ?? 'Staff').' Profile')
@section('header', 'Staff Profile')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Staff</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-medium text-slate-800">{{ $staff->user?->first_name }} {{ $staff->user?->last_name }}</span>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('staff.reset-password', $staff) }}"
                  onsubmit="return confirm('Reset this staff password and generate a new temporary password?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                    Reset Password
                </button>
            </form>
            <a href="{{ route('staff.edit', $staff) }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600">
                Edit Staff
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    @if(session('staff_login_details'))
        @php $creds = session('staff_login_details'); @endphp
        <div class="rounded-2xl border border-blue-200 bg-blue-50 overflow-hidden">
            <div class="px-5 py-4 border-b border-blue-200 bg-blue-100">
                <h3 class="text-sm font-bold text-blue-900">Staff Login Details</h3>
                <p class="text-xs text-blue-700 mt-1">Share this temporary password now. It is shown only once, and staff must change it after first login.</p>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Staff</p>
                    <p class="font-semibold text-slate-800">{{ $creds['name'] ?? ($staff->user?->first_name . ' ' . $staff->user?->last_name) }}</p>
                </div>
                <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Login Email</p>
                    <p class="font-semibold text-slate-800 break-all">{{ $creds['email'] ?? $staff->user?->email }}</p>
                </div>
                <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Temporary Password</p>
                    <p class="font-mono font-bold text-slate-800 tracking-widest">{{ $creds['password'] ?? 'Not available' }}</p>
                </div>
            </div>
            <div class="px-5 pb-4">
                <a href="{{ $creds['login_url'] ?? route('staff.login') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 underline">
                    Staff Login URL: {{ $creds['login_url'] ?? route('staff.login') }}
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-3xl font-bold text-indigo-700">
                {{ strtoupper(substr($staff->user?->first_name ?? 'S', 0, 1)) }}
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $staff->user?->first_name }} {{ $staff->user?->last_name }}</h2>
            <p class="text-sm text-slate-500">{{ $staff->user?->email }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-2">
                @php
                    $staffRole = $staff->user?->role;
                    $staffRoleLabel = is_object($staffRole) && method_exists($staffRole, 'label')
                        ? $staffRole->label()
                        : ucwords(str_replace('_', ' ', (string) $staffRole));
                @endphp
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $staffRoleLabel }}</span>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ ($staff->status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }} capitalize">{{ $staff->status ?? 'active' }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Employment Details</h3>
                <span class="text-xs text-slate-500">Use Edit Staff to update details</span>
            </div>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @foreach([
                    ['Phone', $staff->user?->phone ?? '-'],
                    ['Department', $staff->department ?? '-'],
                    ['Designation', $staff->designation ?? '-'],
                    ['Qualification', $staff->qualification ?? '-'],
                    ['Gender', ucfirst($staff->gender ?? '-')],
                    ['Date Employed', $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment)->format('d M Y') : '-'],
                    ['Basic Salary', $staff->basic_salary ? 'NGN '.number_format($staff->basic_salary, 2) : '-'],
                ] as [$label, $val])
                <div>
                    <dt class="text-xs text-slate-500">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium text-slate-800">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
    </div>

    @if($staff->classTeaching)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="mb-2 text-sm font-semibold text-slate-700">Class Teacher Of</h3>
        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $staff->classTeaching->name }}</span>
    </div>
    @endif

    @if($staff->subjectsTeaching && $staff->subjectsTeaching->count())
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-slate-700">Subjects Teaching</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($staff->subjectsTeaching as $subject)
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $subject->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
