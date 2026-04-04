@extends('layouts.app')
@section('title', $student->full_name)
@section('header', $student->full_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('academic.students.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Students</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-medium text-slate-800">{{ $student->full_name }}</span>
        </div>
        <a href="{{ route('academic.students.edit', $student) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
            @if($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" class="h-24 w-24 rounded-full object-cover mx-auto border-4 border-white shadow" alt="">
            @else
                <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-700 mx-auto">
                    {{ strtoupper(substr($student->first_name,0,1)) }}
                </div>
            @endif
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $student->full_name }}</h2>
            <p class="text-sm text-slate-500">{{ $student->admission_number }}</p>
            <div class="mt-3 flex justify-center gap-2">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $student->schoolClass?->name ?? 'No class' }}</span>
                @if($student->arm)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $student->arm->name }}</span>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Personal Information</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @foreach([
                    ['Gender', ucfirst($student->gender)],
                    ['Date of Birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '—'],
                    ['Blood Group', $student->blood_group ?? '—'],
                    ['Genotype', $student->genotype ?? '—'],
                    ['Religion', $student->religion ?? '—'],
                    ['State of Origin', $student->state_of_origin ?? '—'],
                    ['LGA', $student->lga ?? '—'],
                    ['Previous School', $student->previous_school ?? '—'],
                    ['Session Admitted', $student->session_admitted ?? '—'],
                    ['Status', ucfirst($student->status ?? 'active')],
                ] as [$label, $val])
                <div>
                    <dt class="text-xs text-slate-500">{{ $label }}</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
            @if($student->address)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <dt class="text-xs text-slate-500">Address</dt>
                <dd class="text-sm text-slate-700 mt-0.5">{{ $student->address }}</dd>
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Results --}}
    @if($student->results->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Recent Results</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Subject</th>
                    <th class="px-5 py-2 text-left">Score</th>
                    <th class="px-5 py-2 text-left">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($student->results->take(10) as $result)
                <tr>
                    <td class="px-5 py-2 text-slate-800">{{ $result->subject?->name }}</td>
                    <td class="px-5 py-2 text-slate-700">{{ $result->total_score ?? '—' }}</td>
                    <td class="px-5 py-2 font-semibold text-indigo-700">{{ $result->grade ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
