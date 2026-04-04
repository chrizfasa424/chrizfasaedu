@extends('layouts.app')
@section('title', ($staff->user?->first_name ?? 'Staff').' Profile')
@section('header', 'Staff Profile')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Staff</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-medium text-slate-800">{{ $staff->user?->first_name }} {{ $staff->user?->last_name }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
            <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-700 mx-auto">
                {{ strtoupper(substr($staff->user?->first_name ?? 'S', 0, 1)) }}
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $staff->user?->first_name }} {{ $staff->user?->last_name }}</h2>
            <p class="text-sm text-slate-500">{{ $staff->user?->email }}</p>
            <div class="mt-3 flex justify-center gap-2 flex-wrap">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 capitalize">{{ $staff->user?->role }}</span>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ ($staff->status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }} capitalize">{{ $staff->status ?? 'active' }}</span>
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Employment Details</h3>
                <form method="POST" action="{{ route('staff.update', $staff) }}" class="flex gap-2 items-center">
                    @csrf @method('PUT')
                    <select name="status" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['active','on_leave','terminated','retired'] as $s)
                        <option value="{{ $s }}" {{ ($staff->status ?? 'active') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Update</button>
                </form>
            </div>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @foreach([
                    ['Phone', $staff->user?->phone ?? '—'],
                    ['Department', $staff->department ?? '—'],
                    ['Designation', $staff->designation ?? '—'],
                    ['Qualification', $staff->qualification ?? '—'],
                    ['Gender', ucfirst($staff->gender ?? '—')],
                    ['Date Employed', $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment)->format('d M Y') : '—'],
                    ['Basic Salary', $staff->basic_salary ? '₦'.number_format($staff->basic_salary, 2) : '—'],
                ] as [$label, $val])
                <div>
                    <dt class="text-xs text-slate-500">{{ $label }}</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
    </div>

    {{-- Class Teaching --}}
    @if($staff->classTeaching)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-2">Class Teacher Of</h3>
        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $staff->classTeaching->name }}</span>
    </div>
    @endif

    {{-- Subjects Teaching --}}
    @if($staff->subjectsTeaching && $staff->subjectsTeaching->count())
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-3">Subjects Teaching</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($staff->subjectsTeaching as $subject)
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $subject->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
