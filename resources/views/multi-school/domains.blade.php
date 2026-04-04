@extends('layouts.app')

@section('title', 'School Domains')
@section('header', 'School Domains')

@section('content')
<div class="space-y-8">
    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Schools</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['totalSchools']) }}</p>
            <p class="mt-2 text-sm text-slate-500">All schools currently registered in the platform.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Domains Configured</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ number_format($summary['schoolsWithDomains']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Schools already mapped to their own public domain.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Domains Pending</p>
            <p class="mt-3 text-3xl font-semibold text-amber-600">{{ number_format($summary['schoolsWithoutDomains']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Schools still using the default or fallback public route.</p>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-violet-700">DNS Setup Guide</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">What each school should point to</h3>
                <p class="mt-2 text-sm text-slate-500">Use these values in the school domain provider panel. The root domain usually points with an <span class="font-medium">A record</span>, and <span class="font-medium">www</span> usually points with a <span class="font-medium">CNAME</span>.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">App Host:</span> {{ $dnsGuide['appHost'] ?? 'Unavailable' }}</p>
                <p class="mt-1"><span class="font-semibold text-slate-800">Target IP:</span> {{ $dnsGuide['appIp'] ?? 'Could not detect automatically' }}</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Root Domain Record</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-[120px_1fr]">
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Type</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">A</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Value</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $dnsGuide['appIp'] ?? 'Use your server public IP' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">WWW Record</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-[120px_1fr]">
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Type</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">CNAME</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Value</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">@</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-violet-700">Domain Directory</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">Add or edit one custom domain per school</h3>
                <p class="mt-2 text-sm text-slate-500">This page maps a school to one public domain inside the app. Nameservers and DNS are still managed at the domain provider outside this SaaS.</p>
            </div>

            <div class="flex w-full max-w-3xl flex-col gap-3 lg:items-end">
                <form method="GET" action="{{ route('multi-school.domains') }}" class="flex w-full flex-col gap-3 sm:flex-row">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search by school, email, or domain"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100"
                    >
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#DFE753] focus:ring-offset-2">
                        Search
                    </button>
                </form>

                <form method="POST" action="{{ route('multi-school.domains.clear-cache') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#DFE753] focus:ring-offset-2">
                        Clear Domain Cache
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-6 p-6">
            @forelse($schools as $school)
                @php
                    $status = $domainStatuses[$school->id] ?? ['label' => 'Unknown', 'tone' => 'slate', 'detail' => 'Status unavailable.'];
                    $statusClasses = match($status['tone']) {
                        'emerald' => 'bg-emerald-100 text-emerald-700',
                        'amber' => 'bg-amber-100 text-amber-700',
                        'sky' => 'bg-sky-100 text-sky-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    $isUpdatedSchool = (int) session('updated_school_id') === (int) $school->id;
                @endphp

                <article class="rounded-3xl border {{ $isUpdatedSchool ? 'border-emerald-300 ring-2 ring-emerald-100' : 'border-slate-200' }} bg-white p-5 shadow-sm">
                    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900">{{ $school->name }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">{{ $school->email ?: 'No email provided' }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $status['label'] }}</span>
                                    @if($school->domain)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $school->domain }}</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">No domain set</span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Current Mapping</p>
                                    <p class="mt-3 text-sm font-semibold text-slate-900">{{ $school->domain ?: 'No custom domain saved yet' }}</p>
                                    <p class="mt-2 text-sm text-slate-500">{{ $status['detail'] }}</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Public Link</p>
                                    @if(!empty($status['public_url']))
                                        <a href="{{ $status['public_url'] }}" target="_blank" rel="noopener" class="mt-3 inline-flex text-sm font-semibold text-violet-700 hover:underline">{{ $status['public_url'] }}</a>
                                    @elseif($school->domain)
                                        <a href="http://{{ $school->domain }}" target="_blank" rel="noopener" class="mt-3 inline-flex text-sm font-semibold text-violet-700 hover:underline">http://{{ $school->domain }}</a>
                                    @else
                                        <p class="mt-3 text-sm text-slate-500">Add a domain first to open the public link.</p>
                                    @endif
                                </div>
                            </div>

                            @if($school->domain)
                                <div class="grid gap-3 lg:grid-cols-2">
                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">A Record</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $school->domain }} -> {{ $dnsGuide['appIp'] ?? 'Use your server public IP' }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            data-copy-value="{{ $school->domain }} A {{ $dnsGuide['appIp'] ?? 'Use your server public IP' }}"
                                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C]"
                                        >
                                            Copy
                                        </button>
                                    </div>

                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">WWW Record</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">www -> CNAME -> @</p>
                                        </div>
                                        <button
                                            type="button"
                                            data-copy-value="www CNAME @"
                                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C]"
                                        >
                                            Copy
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Domain Editor</p>
                            <h5 class="mt-2 text-lg font-semibold text-slate-900">{{ $school->domain ? 'Edit school domain' : 'Add school domain' }}</h5>
                            <p class="mt-2 text-sm text-slate-500">Enter only the domain name, for example <span class="font-medium text-slate-700">schoolname.com</span>. Do not enter paths like <span class="font-medium text-slate-700">/about</span>.</p>

                            <form action="{{ route('multi-school.domains.update', $school) }}" method="POST" class="mt-5 space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="domain-{{ $school->id }}" class="mb-2 block text-sm font-medium text-slate-700">Custom Domain</label>
                                    <input
                                        id="domain-{{ $school->id }}"
                                        type="text"
                                        name="domain"
                                        value="{{ old('domain', $school->domain) }}"
                                        placeholder="schoolname.com"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100"
                                    >
                                </div>

                                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-500">
                                    One school can have one live public domain inside this SaaS. To change it, just save a new domain here.
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#DFE753] focus:ring-offset-2">
                                        {{ $school->domain ? 'Save Domain Changes' : 'Add Domain' }}
                                    </button>

                                    @if($school->domain)
                                        <button
                                            type="submit"
                                            name="domain"
                                            value=""
                                            class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-100 focus:ring-offset-2"
                                        >
                                            Remove Domain
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">
                    No schools matched your search.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $schools->links() }}
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copy-value]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.getAttribute('data-copy-value') || '';

            if (!value) {
                return;
            }

            const original = button.textContent.trim();

            try {
                await navigator.clipboard.writeText(value);
                button.textContent = 'Copied';
            } catch (error) {
                button.textContent = 'Copy Failed';
            }

            window.setTimeout(() => {
                button.textContent = original;
            }, 1400);
        });
    });
</script>
@endpush
