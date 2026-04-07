<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — {{ $schoolName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .portal-left { background: linear-gradient(160deg, {{ $primary }} 0%, {{ $primary }}dd 60%, {{ $primary }}bb 100%); }
        .input-field { transition: all 0.2s; border: 1.5px solid #e2e8f0; }
        .input-field:focus { outline: none; border-color: {{ $primary }}; box-shadow: 0 0 0 3px {{ $primary }}26; }
        .submit-btn { background: {{ $primary }}; color: {{ $secondary }}; }
        .submit-btn:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 10px 25px {{ $primary }}40; }
        .submit-btn:active { transform: translateY(0); }
        .accent-text { color: {{ $secondary }}; }
        .accent-bg   { background: {{ $secondary }}; }
        .toggle-btn { color: {{ $primary }}; }
        .card-shadow { box-shadow: 0 25px 60px rgba(0,0,0,0.10), 0 8px 20px rgba(0,0,0,0.06); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float-icon { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">

    {{-- Decorative blobs --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[500px] w-[500px] rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
        <div class="absolute -bottom-40 -right-40 h-[400px] w-[400px] rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
    </div>

    <div class="relative w-full max-w-4xl">

        <div class="overflow-hidden rounded-3xl bg-white card-shadow flex flex-col lg:flex-row">

            {{-- ── LEFT: Branded Panel ── --}}
            <div class="portal-left lg:w-[42%] relative flex flex-col justify-between overflow-hidden px-10 py-12">

                {{-- Dot pattern --}}
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle, rgba(255,255,255,0.6) 1px, transparent 1px);
                            background-size: 28px 28px;"></div>
                <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-white opacity-5"></div>
                <div class="absolute top-0 right-0 h-32 w-32 rounded-full bg-white opacity-5 -translate-y-1/2 translate-x-1/2"></div>

                {{-- Top: Logo & Name --}}
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="float-icon flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-white/60">Student Portal</p>
                            <p class="text-base font-extrabold text-white">{{ $schoolName }}</p>
                        </div>
                    </div>
                </div>

                {{-- Centre: Headline --}}
                <div class="relative z-10 flex-1 flex flex-col justify-center py-10">
                    <h2 class="text-3xl xl:text-4xl font-extrabold leading-tight text-white">
                        Hello,<br>Scholar! 👋
                    </h2>
                    <p class="mt-4 text-sm text-white/70 leading-relaxed">
                        Access your grades, attendance, timetable, and fee information — all in one place.
                    </p>

                    {{-- Feature pills --}}
                    <div class="mt-8 space-y-3">
                        @foreach([
                            ['📊', 'Academic Results & Report Cards'],
                            ['📅', 'Attendance & Timetable'],
                            ['💳', 'Fee Invoices & Payments'],
                            ['📢', 'School Announcements'],
                        ] as [$emoji, $label])
                        <div class="flex items-center gap-3 rounded-2xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <span class="text-base leading-none">{{ $emoji }}</span>
                            <span class="text-sm font-medium text-white">{{ $label }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Bottom --}}
                <div class="relative z-10">
                    <p class="text-xs text-white/40">&copy; {{ date('Y') }} {{ $schoolName }}</p>
                </div>
            </div>

            {{-- ── RIGHT: Form Panel ── --}}
            <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-12">

                {{-- Staff redirect notice --}}
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                    <p class="text-xs text-amber-700">
                        <strong>Students &amp; Parents only.</strong> Staff members should use the
                        <a href="{{ route('login') }}" class="font-bold underline hover:text-amber-900">admin login</a>.
                    </p>
                </div>

                <div class="mb-7">
                    <h2 class="text-2xl font-extrabold text-slate-900">Welcome Back!</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Sign in with the credentials provided at enrolment.</p>
                </div>

                {{-- Success (after reset) --}}
                @if(session('status'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-emerald-700">{{ session('status') }}</p>
                </div>
                @endif

                {{-- Error --}}
                @if($errors->has('email'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                    </svg>
                    <p class="text-sm text-red-700">{{ $errors->first('email') }}</p>
                </div>
                @endif

                <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75-9.75-6.75"/>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="your@email.com"
                                   class="input-field w-full rounded-2xl bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 placeholder:text-slate-400">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                                    <circle cx="12" cy="15.375" r="1.125" fill="currentColor" stroke="none"/>
                                </svg>
                            </span>
                            <input type="password" name="password" id="portal_password" required
                                   placeholder="••••••••"
                                   class="input-field w-full rounded-2xl bg-slate-50 py-3.5 pl-12 pr-10 text-sm text-slate-900 placeholder:text-slate-400">
                            <button type="button" onclick="togglePassword('portal_password','portal_eye')"
                                    class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-4 transition">
                                <svg id="portal_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300">
                            <span class="text-sm text-slate-600">Remember me</span>
                        </label>
                        <a href="{{ route('portal.password.request') }}" class="text-xs font-medium transition" style="color:{{ $primary }};">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit"
                            class="submit-btn mt-2 flex w-full items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold shadow-lg transition duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/>
                        </svg>
                        Sign In to My Portal
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-100 pt-6 text-center space-y-3">
                    <p class="text-xs text-slate-400">Need your login details? Contact your school admin.</p>
                    <a href="{{ route('public.home') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 transition hover:text-slate-700">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/>
                        </svg>
                        Back to school website
                    </a>
                </div>

            </div>
        </div>
    </div>
    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            icon.innerHTML = isPassword
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
        }
    </script>
</body>
</html>
