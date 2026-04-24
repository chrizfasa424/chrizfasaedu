<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $loginMode = $loginMode ?? 'admin';
        $isStaffLogin = $loginMode === 'staff';
        $pageTitle = $pageTitle ?? ($isStaffLogin ? 'Staff Login' : 'Admin Login');
        $panelLabel = $panelLabel ?? ($isStaffLogin ? 'Staff Area' : 'Admin Area');
        $loginAction = $loginAction ?? route('login');
        $primaryCtaLabel = $primaryCtaLabel ?? ($isStaffLogin ? 'Sign In to Staff Dashboard' : 'Sign In to Admin Console');
    @endphp
    <title>{{ $pageTitle }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-focus: rgba(14, 165, 233, 0.2);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-bg {
            background:
                radial-gradient(circle at 18% 22%, rgba(14, 165, 233, 0.2), transparent 34%),
                radial-gradient(circle at 82% 82%, rgba(16, 185, 129, 0.14), transparent 40%),
                linear-gradient(130deg, #0b1324 0%, #111827 55%, #0b1220 100%);
        }
        .card-bg {
            background: linear-gradient(180deg, rgba(15,23,42,0.96) 0%, rgba(15,23,42,0.9) 100%);
            border: 1px solid rgba(148,163,184,0.2);
        }
        .field:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px var(--auth-focus);
        }
        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--auth-focus);
        }
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        @keyframes fx-lift-card {
            from {
                transform: translateY(16px);
            }
            to {
                transform: translateY(-8px);
            }
        }

        @keyframes fx-primary-flash {
            0%, 100% {
                opacity: 0;
            }
            35% {
                opacity: 0.35;
            }
        }

        @keyframes fx-border-blink {
            0%, 49% {
                border-color: transparent;
                box-shadow: none;
            }
            50%, 100% {
                border-color: #38bdf8;
                box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.85), 0 0 20px rgba(56, 189, 248, 0.45);
            }
        }

        @keyframes fx-neon-pulse {
            0%, 100% {
                text-shadow: 0 0 8px rgba(56, 189, 248, 0.4), 0 0 16px rgba(56, 189, 248, 0.24);
            }
            50% {
                text-shadow: 0 0 16px rgba(56, 189, 248, 0.95), 0 0 28px rgba(56, 189, 248, 0.7), 0 0 38px rgba(56, 189, 248, 0.34);
            }
        }

        .fx-sidebar-effects {
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            transition: transform 0.35s ease;
        }

        .fx-sidebar-effects::before,
        .fx-sidebar-effects::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: inherit;
        }

        .fx-sidebar-effects::before {
            background: linear-gradient(145deg, rgba(14, 165, 233, 0.45), rgba(15, 23, 42, 0.15));
            opacity: 0;
        }

        .fx-sidebar-effects::after {
            inset: 1px;
            border: 2px solid transparent;
        }

        .fx-sidebar-effects:hover,
        .fx-sidebar-effects:focus-within {
            animation: fx-lift-card 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .fx-sidebar-effects:hover::before,
        .fx-sidebar-effects:focus-within::before {
            animation: fx-primary-flash 0.24s ease-out 2;
        }

        .fx-sidebar-effects:hover::after,
        .fx-sidebar-effects:focus-within::after {
            animation: fx-border-blink 0.14s steps(2, end) infinite;
        }

        .fx-sidebar-effects:hover .fx-sidebar-title,
        .fx-sidebar-effects:focus-within .fx-sidebar-title {
            animation: fx-neon-pulse 1.05s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen page-bg text-slate-100">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-3xl shadow-2xl shadow-black/30 lg:grid-cols-2">
            <section class="relative hidden overflow-hidden bg-sky-900/70 p-10 lg:block">
                <div class="absolute -right-24 -top-20 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-sky-400/20 blur-3xl"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                            <svg class="h-6 w-6 text-cyan-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 7.5 12 12l9-4.5L12 3Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 15l7.5-4.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 13.5 12 18l7.5-4.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-100/75">{{ $panelLabel }}</p>
                            <h1 class="text-lg font-extrabold text-white">{{ config('app.name') }}</h1>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <h2 class="text-4xl font-extrabold leading-tight text-white">
                            School Operations
                            <span class="text-sky-200">In One Secure Console</span>
                        </h2>
                        <p class="mt-5 text-sm leading-relaxed text-sky-100/75">
                            Manage academics, finance, admissions, and school reporting from one place.
                        </p>
                        <div class="mt-8 space-y-3">
                            <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm text-sky-50">Academic and assessment management</div>
                            <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm text-sky-50">Students, staff, and communication tools</div>
                            <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm text-sky-50">Secure access for authorized personnel</div>
                        </div>
                    </div>

                    <p class="text-xs text-sky-100/55">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                </div>
            </section>

            <section class="card-bg p-6 sm:p-10">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-300/80">{{ $panelLabel }}</p>
                        <h2 class="mt-2 text-xl font-bold text-white">{{ config('app.name') }}</h2>
                    </div>

                    <h3 class="text-2xl font-extrabold text-white">Sign In</h3>
                    <p class="mt-1 text-sm text-slate-400">Use your staff account credentials to continue.</p>

                    @if(session('status'))
                        <div class="mt-5 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->has('email'))
                        <div class="mt-5 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $loginAction }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="field w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500"
                                placeholder="admin@school.edu">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="admin_password" required
                                    class="field w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 pr-11 text-sm text-white placeholder:text-slate-500"
                                    placeholder="Enter password">
                                <button type="button" onclick="togglePassword('admin_password','admin_eye')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-200">
                                    <svg id="admin_eye" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                                <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-slate-600 bg-slate-800">
                                Remember me
                            </label>
                            <a href="{{ route('admin.password.request') }}" class="text-xs font-semibold text-sky-300 hover:text-sky-200">Forgot password?</a>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 px-5 py-3 text-sm font-bold text-white transition hover:from-cyan-400 hover:to-blue-400">
                            {{ $primaryCtaLabel }}
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-800 pt-5">
                        @if($isStaffLogin)
                            <p class="text-center text-xs text-slate-500">Student or parent account?</p>
                            <a href="{{ route('portal.login') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-xs font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100">
                                Open Student and Parent Portal
                            </a>
                        @else
                            <p class="text-center text-xs text-slate-500">Student, parent, or staff account?</p>
                            <a href="{{ route('portal.login') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-xs font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100">
                                Open Student and Parent Portal
                            </a>
                            <a href="{{ route('staff.login') }}" class="mt-2 inline-flex w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-xs font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100">
                                Open Staff Login
                            </a>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            const reveal = field.type === 'password';
            field.type = reveal ? 'text' : 'password';
            icon.innerHTML = reveal
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.77 9.77 0 0 1 12 4.5C18 4.5 21.75 12 21.75 12a14.5 14.5 0 0 1-3.28 4.37M6.23 6.23C4.26 7.56 2.86 9.52 2.25 12c0 0 3.75 7.5 9.75 7.5a9.8 9.8 0 0 0 4.12-.9"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="3"/>';
        }
    </script>
</body>
</html>
