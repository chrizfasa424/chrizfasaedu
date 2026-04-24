@extends('layouts.app')

@section('title', 'Application - ' . $admission->application_number)
@section('header', 'Admission Application')

@section('content')
@php
    $sv = $admission->status->value;
    $statusConfig = [
        'pending'      => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#f59e0b', 'label' => 'Pending'],
        'under_review' => ['bg' => '#e0f2fe', 'text' => '#075985', 'dot' => '#0ea5e9', 'label' => 'Under Review'],
        'screening'    => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'dot' => '#a855f7', 'label' => 'Screening'],
        'approved'     => ['bg' => '#dcfce7', 'text' => '#14532d', 'dot' => '#22c55e', 'label' => 'Approved'],
        'rejected'     => ['bg' => '#fee2e2', 'text' => '#7f1d1d', 'dot' => '#ef4444', 'label' => 'Rejected'],
        'enrolled'     => ['bg' => '#dbeafe', 'text' => '#1e3a5f', 'dot' => '#3b82f6', 'label' => 'Enrolled'],
    ];
    $cfg      = $statusConfig[$sv] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'dot' => '#94a3b8', 'label' => ucfirst($sv)];
    $isAdmin  = auth()->check() && in_array(auth()->user()->role->value, ['super_admin','school_admin','principal']);
@endphp

{{-- Flash: general success --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
    <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Flash: enrolment credentials --}}
@if(session('enrolled_credentials'))
@php $creds = session('enrolled_credentials'); @endphp
<div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 overflow-hidden">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-200 bg-blue-100">
        <svg class="h-5 w-5 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        <div>
            <p class="text-sm font-bold text-blue-900">Student Portal Credentials Created</p>
            <p class="text-xs text-blue-600 mt-0.5">
                @if($creds['email_sent'])
                    Credentials have been emailed to <strong>{{ $creds['sent_to'] ?? $creds['email'] }}</strong>. Share the details below with the student/parent as a backup.
                @else
                    Email delivery failed - please share these credentials directly with the student/parent.
                @endif
            </p>
        </div>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Student</p>
            <p class="font-semibold text-slate-800">{{ $creds['name'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Login Email</p>
            <p class="font-semibold text-slate-800 break-all">{{ $creds['email'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-blue-200 px-4 py-3">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Temporary Password</p>
            <p class="font-mono font-bold text-slate-800 tracking-widest">{{ $creds['password'] }}</p>
        </div>
    </div>
    <div class="px-5 pb-4">
        <a href="{{ $creds['login_url'] }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 underline">
            Portal Login URL: {{ $creds['login_url'] }}
        </a>
        <p class="mt-1 text-xs text-blue-500">Advise the student to change their password after first login.</p>
    </div>
</div>
@endif

{{-- Back link --}}
<div class="mb-5">
    <a href="{{ route('admission.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        All Applications
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Left column (2/3) --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Application header card --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="h-1.5 w-full" style="background:linear-gradient(90deg,{{ $cfg['dot'] }},{{ $cfg['dot'] }}80);"></div>
            <div class="p-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 font-extrabold text-2xl">
                        {{ strtoupper(substr($admission->first_name, 0, 1)) }}{{ strtoupper(substr($admission->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">{{ $admission->first_name }} {{ $admission->other_names ? $admission->other_names . ' ' : '' }}{{ $admission->last_name }}</h2>
                        <p class="text-sm text-slate-400 mt-0.5">
                            Application No: <span class="font-mono font-bold text-slate-600">{{ $admission->application_number }}</span>
                        </p>
                        @if($admission->admission_number)
                        <p class="text-xs text-slate-400">Admission No: <span class="font-mono font-semibold text-blue-600">{{ $admission->admission_number }}</span></p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-start sm:items-end gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-bold"
                          style="background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">
                        <span class="h-2 w-2 rounded-full" style="background:{{ $cfg['dot'] }};"></span>
                        {{ $cfg['label'] }}
                    </span>
                    <p class="text-xs text-slate-400">Applied {{ $admission->created_at->format('d M Y, g:ia') }}</p>
                </div>
            </div>
        </div>

        {{-- Student Details --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Student Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Date of Birth</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->date_of_birth?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Gender</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->gender ? ucfirst($admission->gender) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Blood Group</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->blood_group ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Genotype</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->genotype ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Religion</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->religion ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Class Applied For</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->class_applied_for }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Student Email (Portal Login)</dt>
                        <dd class="font-semibold text-slate-800">
                            @if($admission->email)
                            <a href="mailto:{{ $admission->email }}" class="hover:text-indigo-600">{{ $admission->email }}</a>
                            @else
                            -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Academic Session</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->session?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">State of Origin</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->state_of_origin ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">LGA</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->lga ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Previous School</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->previous_school ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Home Address</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->address ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Parent / Guardian --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Parent / Guardian</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Full Name</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->parent_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Phone</dt>
                        <dd class="font-semibold text-slate-800">
                            <a href="tel:{{ $admission->parent_phone }}" class="hover:text-indigo-600">{{ $admission->parent_phone }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Email</dt>
                        <dd class="font-semibold text-slate-800">
                            @if($admission->parent_email)
                            <a href="mailto:{{ $admission->parent_email }}" class="hover:text-indigo-600">{{ $admission->parent_email }}</a>
                            @else
                            -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Occupation</dt>
                        <dd class="font-semibold text-slate-800">{{ $admission->parent_occupation ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Documents --}}
        @php
            $docs = [
                'photo'              => ['label' => 'Passport Photo',    'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                'birth_certificate'  => ['label' => 'Birth Certificate', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'previous_result'    => ['label' => 'Previous Result',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ];
            $hasAny = collect($docs)->keys()->some(fn($k) => $admission->$k);
        @endphp
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Uploaded Documents</h3>
            </div>
            <div class="p-6">
                @if($hasAny)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($docs as $field => $meta)
                    @if($admission->$field)
                    @php
                        $path = $admission->$field;
                        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        $isImg = in_array($ext, ['jpg','jpeg','png','webp']);
                        $url   = route('admission.documents.show', ['admission' => $admission, 'document' => $field]);
                    @endphp
                    <a href="{{ $url }}" target="_blank"
                       class="group flex flex-col items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-indigo-300 hover:bg-indigo-50 transition">
                        @if($isImg)
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white" style="width:150px;height:150px;flex-shrink:0;">
                            <img src="{{ $url }}" alt="{{ $meta['label'] }}"
                                 style="width:150px;height:150px;object-fit:cover;object-position:center;display:block;"
                                 class="group-hover:opacity-90 transition">
                        </div>
                        @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-white border border-slate-200 shadow-sm group-hover:border-indigo-300">
                            <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}"/>
                            </svg>
                        </div>
                        @endif
                        <div class="text-center">
                            <p class="text-xs font-semibold text-slate-600 group-hover:text-indigo-700">{{ $meta['label'] }}</p>
                            <p class="text-[10px] text-slate-400 uppercase mt-0.5">{{ strtoupper($ext) }}</p>
                        </div>
                    </a>
                    @endif
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-4">No documents uploaded.</p>
                @endif
            </div>
        </div>

        {{-- Review history --}}
        @if($admission->review_notes || $admission->reviewed_at)
        <div class="rounded-2xl border {{ $sv === 'approved' ? 'border-emerald-200 bg-emerald-50' : ($sv === 'rejected' ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50') }} p-6">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 shrink-0 mt-0.5 {{ $sv === 'approved' ? 'text-emerald-600' : ($sv === 'rejected' ? 'text-red-500' : 'text-amber-500') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-bold {{ $sv === 'approved' ? 'text-emerald-800' : ($sv === 'rejected' ? 'text-red-800' : 'text-amber-800') }} mb-1">Review Record</p>
                    @if($admission->review_notes)
                    <p class="text-sm {{ $sv === 'approved' ? 'text-emerald-700' : ($sv === 'rejected' ? 'text-red-700' : 'text-amber-700') }} leading-relaxed">{{ $admission->review_notes }}</p>
                    @endif
                    @if($admission->screening_score !== null)
                    <p class="mt-2 text-xs font-semibold {{ $sv === 'approved' ? 'text-emerald-600' : ($sv === 'rejected' ? 'text-red-600' : 'text-amber-600') }}">
                        Screening Score: {{ $admission->screening_score }}%
                    </p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                        @if($admission->reviewer)
                        <span>Reviewed by: <strong>{{ $admission->reviewer->first_name }} {{ $admission->reviewer->last_name }}</strong></span>
                        @endif
                        @if($admission->reviewed_at)
                        <span>on {{ $admission->reviewed_at->format('d M Y, g:ia') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Right column (1/3) --}}
    <div class="space-y-5">

        @if($isAdmin && $sv !== 'enrolled')
        {{-- Review form --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Review Application</h3>
            </div>
            <form action="{{ route('admission.review', $admission) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="pending"      {{ $sv === 'pending'      ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ $sv === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="screening"    {{ $sv === 'screening'    ? 'selected' : '' }}>Screening</option>
                        <option value="approved"     {{ $sv === 'approved'     ? 'selected' : '' }}>Approved</option>
                        <option value="rejected"     {{ $sv === 'rejected'     ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Screening Score (%)</label>
                    <input type="number" name="screening_score" min="0" max="100"
                        value="{{ old('screening_score', $admission->screening_score) }}"
                        placeholder="0 - 100"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Review Notes</label>
                    <textarea name="review_notes" rows="4"
                        placeholder="Add notes about this application..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">{{ old('review_notes', $admission->review_notes) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Save Review
                </button>
            </form>
        </div>
        @endif

        {{-- Enroll button --}}
        @if($isAdmin && $sv === 'approved')
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
            <div class="flex items-start gap-3 mb-4">
                <svg class="h-5 w-5 shrink-0 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-emerald-800">Ready to Enroll</p>
                    <p class="text-xs text-emerald-700 mt-0.5 leading-relaxed">Enrolling will create a student record, user account, and parent/guardian profile from this application data.</p>
                </div>
            </div>
            <form action="{{ route('admission.enroll', $admission) }}" method="POST"
                  onsubmit="return confirm('Enroll this student? A user account will be created with a temporary password.')">
                @csrf
                <button type="submit"
                    class="w-full rounded-xl bg-emerald-600 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition">
                    Enroll Student
                </button>
            </form>
        </div>
        @endif

        {{-- Enrolled notice + user info --}}
        @if($sv === 'enrolled')
        @php $enrolledUser = $admission->student?->user; @endphp
        <div class="rounded-2xl border border-blue-200 bg-blue-50 overflow-hidden">
            <div class="p-6 text-center border-b border-blue-200">
                <svg class="mx-auto h-10 w-10 text-blue-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm font-bold text-blue-800">Student Enrolled</p>
                @if($admission->admission_number)
                <p class="text-xs text-blue-600 mt-1 font-mono font-bold tracking-widest">{{ $admission->admission_number }}</p>
                @endif
            </div>

            @if($enrolledUser)
            <div class="p-5 space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-400">Portal Account</p>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 shrink-0">Login Email</dt>
                        <dd class="text-slate-800 font-semibold text-right break-all">{{ $enrolledUser->email }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 shrink-0">Role</dt>
                        <dd class="text-slate-800 font-semibold">{{ ucfirst($enrolledUser->role->value ?? 'student') }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 shrink-0">Account Created</dt>
                        <dd class="text-slate-800 font-semibold text-right">{{ $enrolledUser->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
                <div class="pt-1 border-t border-blue-200">
                    <p class="text-xs text-blue-500">Password is hashed - share credentials from the enrolment email sent to the parent.</p>
                </div>
                @if($isAdmin)
                <div class="pt-2 border-t border-blue-200">
                    <form action="{{ route('admission.update-student-email', $admission) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <label class="block text-xs font-semibold uppercase tracking-wider text-blue-500 mb-1">Student Portal Email</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" value="{{ old('email', $admission->email) }}"
                                   placeholder="student@example.com"
                                   class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <button type="submit"
                                class="shrink-0 rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">
                                Save
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-blue-500">Set a real student email here, then click sync below.</p>
                    </form>
                    <p class="text-xs text-blue-600 mb-2">Need to align login with application email? This safely updates only when the target email is not used by another user.</p>
                    <form action="{{ route('admission.sync-login-email', $admission) }}" method="POST"
                          onsubmit="return confirm('Sync this student login email from admission data now?')">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl border border-blue-300 bg-white py-2 text-sm font-semibold text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">
                            Sync Login Email From Admission
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        {{-- Application meta --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Application Info</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 shrink-0">Submitted</dt>
                    <dd class="text-slate-800 font-semibold text-right">{{ $admission->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 shrink-0">Last Updated</dt>
                    <dd class="text-slate-800 font-semibold text-right">{{ $admission->updated_at->diffForHumans() }}</dd>
                </div>
                @if($admission->session)
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 shrink-0">Session</dt>
                    <dd class="text-slate-800 font-semibold text-right">{{ $admission->session->name }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Delete --}}
        @if($isAdmin)
        <div class="rounded-2xl border border-red-100 bg-red-50 p-5">
            <p class="text-xs font-semibold text-red-700 mb-3 leading-relaxed">Permanently delete this application and all uploaded documents. This cannot be undone.</p>
            <form action="{{ route('admission.destroy', $admission) }}" method="POST"
                  onsubmit="return confirm('Delete this application permanently? All documents will also be removed.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full rounded-xl border border-red-300 bg-white py-2 text-sm font-semibold text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 transition">
                    Delete Application
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
