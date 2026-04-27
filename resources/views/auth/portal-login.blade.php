<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - {{ $schoolName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --portal-focus: color-mix(in srgb, {{ $primary }} 30%, transparent);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-bg {
            background:
                radial-gradient(circle at 15% 20%, {{ $primary }}22, transparent 35%),
                radial-gradient(circle at 85% 82%, {{ $primary }}20, transparent 38%),
                linear-gradient(130deg, #f8fafc 0%, #eef6ff 55%, #f5fbff 100%);
        }
        .brand-panel {
            background: linear-gradient(155deg, {{ $primary }} 0%, {{ $primary }}d9 60%, {{ $primary }}b8 100%);
        }
        .field {
            border: 1.5px solid #e2e8f0;
            transition: all .2s;
        }
        .field:focus {
            outline: none;
            border-color: {{ $primary }};
            box-shadow: 0 0 0 3px var(--portal-focus);
        }
        .submit-btn {
            background: {{ $primary }};
            color: {{ $secondary }};
        }
        .submit-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 10px 25px {{ $primary }}40;
        }
        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--portal-focus);
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
                border-color: {{ $secondary }};
                box-shadow: 0 0 0 1px color-mix(in srgb, {{ $secondary }} 88%, transparent),
                            0 0 20px color-mix(in srgb, {{ $secondary }} 52%, transparent);
            }
        }

        @keyframes fx-neon-pulse {
            0%, 100% {
                text-shadow: 0 0 8px color-mix(in srgb, {{ $secondary }} 46%, transparent),
                             0 0 16px color-mix(in srgb, {{ $secondary }} 24%, transparent);
            }
            50% {
                text-shadow: 0 0 16px color-mix(in srgb, {{ $secondary }} 95%, transparent),
                             0 0 28px color-mix(in srgb, {{ $secondary }} 65%, transparent),
                             0 0 38px color-mix(in srgb, {{ $secondary }} 34%, transparent);
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
            background: linear-gradient(145deg,
                color-mix(in srgb, {{ $primary }} 58%, transparent),
                color-mix(in srgb, {{ $primary }} 20%, transparent));
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
<body class="min-h-screen page-bg">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-3xl bg-white shadow-2xl shadow-slate-900/10 lg:grid-cols-2">
            <section class="brand-panel relative hidden overflow-hidden p-10 lg:block">
                <div class="absolute -right-24 -top-16 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14 3 9l9-5 9 5-9 5Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12v3.5c0 .8 2.2 2.5 5 2.5s5-1.7 5-2.5V12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/70">Student Portal</p>
                            <h1 class="text-lg font-extrabold text-white">{{ $schoolName }}</h1>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <h2 class="text-4xl font-extrabold leading-tight text-white">
                            Welcome Back
                            <span class="block text-white/80">Keep Learning, Keep Growing</span>
                        </h2>
                        <p class="mt-5 text-sm leading-relaxed text-white/75">
                            Access published scores, attendance updates, and your school communication in one place.
                        </p>
                        <div class="mt-8 space-y-3">
                            <div class="rounded-xl border border-white/20 bg-white/12 px-4 py-2.5 text-sm text-white">Published First Test, Second Test, and Exam scores</div>
                            <div class="rounded-xl border border-white/20 bg-white/12 px-4 py-2.5 text-sm text-white">Full terminal result when school publishes it</div>
                            <div class="rounded-xl border border-white/20 bg-white/12 px-4 py-2.5 text-sm text-white">Feedback and result query submission</div>
                        </div>
                    </div>

                    <p class="text-xs text-white/55">&copy; {{ date('Y') }} {{ $schoolName }}</p>
                </div>
            </section>

            <section class="p-6 sm:p-10">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:{{ $primary }};">Student Portal</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $schoolName }}</h2>
                    </div>

                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                        Students and parents only. Staff should use
                        <a href="{{ route('login') }}" class="font-bold underline">admin login</a>.
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900">Sign In</h3>
                    <p class="mt-1 text-sm text-slate-500">Use your portal account credentials.</p>

                    @if(session('status'))
                        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->has('email'))
                        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('portal.login.submit') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="field w-full rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400"
                                placeholder="student@school.edu">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="portal_password" required
                                    class="field w-full rounded-xl bg-slate-50 px-4 py-3 pr-11 text-sm text-slate-900 placeholder:text-slate-400"
                                    placeholder="Enter password">
                                <button type="button" onclick="togglePassword('portal_password','portal_eye')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-700">
                                    <svg id="portal_eye" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-xs text-slate-500">
                                <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-slate-300">
                                Remember me
                            </label>
                            <a href="{{ route('portal.password.request') }}" class="text-xs font-semibold" style="color:{{ $primary }};">Forgot password?</a>
                        </div>

                        <button type="submit" class="submit-btn w-full rounded-xl px-5 py-3 text-sm font-bold transition">
                            Sign In to My Portal
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-100 pt-5 text-center">
                        <a href="{{ route('public.home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Back to school website</a>
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
