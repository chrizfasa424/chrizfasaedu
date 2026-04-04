@extends('layouts.app')
@section('title', 'Sign In')

@section('content')
@php
    use App\Support\SchoolContext;
    use App\Support\PublicPageContent;
    $loginSchool = SchoolContext::current(request());
    $loginPublicPage = PublicPageContent::forSchool($loginSchool);
    $loginSchoolName  = $loginSchool?->name ?? 'ChrizFasa Academy';
    $loginSchoolMotto = $loginSchool?->motto ?: 'A modern learning environment for KG, Primary, and Secondary students';
    $loginSchoolLogo  = $loginSchool?->logo;
    $loginPrimaryColor    = trim((string) ($loginPublicPage['primary_color']    ?? '#2D1D5C'));
    $loginSecondaryColor  = trim((string) ($loginPublicPage['secondary_color']  ?? '#DFE753'));
    $loginInitials = \Illuminate\Support\Str::upper(
        collect(preg_split('/\s+/', trim($loginSchoolName)) ?: [])
            ->filter()->take(2)
            ->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))
            ->implode('')
    ) ?: 'CA';
@endphp

<style>
    body { background: {{ $loginPrimaryColor }} !important; }
    .login-panel { background: {{ $loginPrimaryColor }}; }
    .login-accent { color: {{ $loginSecondaryColor }}; }
    .login-accent-bg { background: {{ $loginSecondaryColor }}; }
    .login-accent-border { border-color: {{ $loginSecondaryColor }}; }
    .login-btn {
        background: {{ $loginSecondaryColor }};
        color: {{ $loginPrimaryColor }};
    }
    .login-btn:hover { filter: brightness(1.08); }
    .login-input:focus {
        outline: none;
        border-color: {{ $loginSecondaryColor }};
        box-shadow: 0 0 0 3px {{ $loginSecondaryColor }}26;
    }
    .dot-pattern {
        background-image: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 28px 28px;
    }
</style>

<div class="login-panel min-h-screen flex items-stretch dot-pattern -m-0">
    <div class="flex w-full min-h-screen flex-col lg:flex-row">

        {{-- ── LEFT BRANDING PANEL ── --}}
        <div class="relative hidden lg:flex lg:w-[46%] xl:w-[42%] flex-col justify-between overflow-hidden px-12 py-14">
            {{-- Radial glow --}}
            <div style="position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 30% 40%,rgba(223,231,83,0.12) 0%,transparent 70%);pointer-events:none;"></div>
            {{-- Decorative circle --}}
            <div style="position:absolute;bottom:-140px;right:-100px;width:460px;height:460px;border-radius:50%;border:1px solid rgba(255,255,255,0.06);pointer-events:none;"></div>
            <div style="position:absolute;bottom:-60px;right:-60px;width:280px;height:280px;border-radius:50%;border:1px solid rgba(255,255,255,0.04);pointer-events:none;"></div>

            {{-- Logo + School name --}}
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    @if($loginSchoolLogo)
                        <img src="{{ asset('storage/' . ltrim($loginSchoolLogo, '/')) }}"
                             alt="{{ $loginSchoolName }} logo"
                             class="h-16 w-16 rounded-2xl object-cover border-2 shadow-2xl"
                             style="border-color: {{ $loginSecondaryColor }}40;">
                    @else
                        <div class="login-accent-bg flex h-16 w-16 items-center justify-center rounded-2xl text-2xl font-extrabold shadow-2xl"
                             style="color: {{ $loginPrimaryColor }}">
                            {{ $loginInitials }}
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/45">Student Portal</p>
                        <h1 class="mt-1 text-xl font-extrabold leading-tight text-white">{{ $loginSchoolName }}</h1>
                    </div>
                </div>
            </div>

            {{-- Centre tagline block --}}
            <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/6 px-4 py-2 text-xs font-semibold text-white/70 backdrop-blur mb-6 w-fit">
                    <span class="login-accent-bg inline-block h-2 w-2 rounded-full"></span>
                    School Management System
                </div>
                <h2 class="text-4xl xl:text-5xl font-extrabold leading-[1.15] text-white">
                    Welcome Back<br>
                    <span class="login-accent">to Your Portal</span>
                </h2>
                <p class="mt-5 text-base text-white/60 leading-relaxed max-w-sm">
                    {{ $loginSchoolMotto }}
                </p>

                {{-- Feature bullets --}}
                <div class="mt-10 space-y-4">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 0 0 1.946-.806 3.42 3.42 0 0 1 4.438 0 3.42 3.42 0 0 0 1.946.806 3.42 3.42 0 0 1 3.138 3.138 3.42 3.42 0 0 0 .806 1.946 3.42 3.42 0 0 1 0 4.438 3.42 3.42 0 0 0-.806 1.946 3.42 3.42 0 0 1-3.138 3.138 3.42 3.42 0 0 0-1.946.806 3.42 3.42 0 0 1-4.438 0 3.42 3.42 0 0 0-1.946-.806 3.42 3.42 0 0 1-3.138-3.138 3.42 3.42 0 0 0-.806-1.946 3.42 3.42 0 0 1 0-4.438 3.42 3.42 0 0 0 .806-1.946 3.42 3.42 0 0 1 3.138-3.138z', 'text' => 'Real-time academic results & grades'],
                        ['icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 1 1 0 4H9a2 2 0 0 1-2-2', 'text' => 'Attendance tracking & reports'],
                        ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z', 'text' => 'Fee invoices & payment status'],
                    ] as $feat)
                    <div class="flex items-start gap-3">
                        <span class="login-accent-bg mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg shadow"
                              style="color:{{ $loginPrimaryColor }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                            </svg>
                        </span>
                        <span class="text-sm text-white/70">{{ $feat['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom tagline --}}
            <div class="relative z-10">
                <p class="text-xs text-white/30">&copy; {{ date('Y') }} {{ $loginSchoolName }}. All rights reserved.</p>
            </div>
        </div>

        {{-- ── RIGHT FORM PANEL ── --}}
        <div class="flex flex-1 items-center justify-center bg-white px-6 py-12 sm:px-12 lg:rounded-l-[2.5rem] lg:shadow-2xl">
            <div class="w-full max-w-md">

                {{-- Mobile logo --}}
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    @if($loginSchoolLogo)
                        <img src="{{ asset('storage/' . ltrim($loginSchoolLogo, '/')) }}"
                             alt="{{ $loginSchoolName }}"
                             class="h-12 w-12 rounded-xl object-cover border border-slate-200">
                    @else
                        <div class="login-accent-bg flex h-12 w-12 items-center justify-center rounded-xl text-lg font-extrabold"
                             style="color:{{ $loginPrimaryColor }}">{{ $loginInitials }}</div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Portal Login</p>
                        <p class="text-base font-bold text-slate-900">{{ $loginSchoolName }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-slate-900">Sign in</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your credentials to access your portal.</p>
                </div>

                @if($errors->has('email'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 h-4 w-4 shrink-0 text-red-500"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/></svg>
                    <p class="text-sm text-red-700">{{ $errors->first('email') }}</p>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4.5 w-4.5 h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0-9.75 6.75-9.75-6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="you@school.edu"
                                   class="login-input w-full rounded-2xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 transition placeholder:text-slate-400">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 1 1 7.5 0v3"/><circle cx="12" cy="15.375" r="1.125" fill="currentColor" stroke="none"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required
                                   placeholder="••••••••"
                                   class="login-input w-full rounded-2xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 transition placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 accent-[{{ $loginPrimaryColor }}]">
                            <span class="text-sm text-slate-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="login-btn mt-2 flex w-full items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4.5 w-4.5 h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/></svg>
                        Sign In to Portal
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-100 pt-6 text-center">
                    <p class="text-xs text-slate-400">
                        Need help? Contact the school admin or front desk.
                    </p>
                    <a href="{{ route('public.home') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-slate-500 transition hover:text-slate-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/></svg>
                        Back to school website
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
