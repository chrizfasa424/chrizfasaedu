<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .admin-gradient { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
        .grid-pattern {
            background-image: linear-gradient(rgba(148,163,184,0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(148,163,184,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .input-field:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .submit-btn { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .submit-btn:hover { background: linear-gradient(135deg, #4f46e5, #4338ca); transform: translateY(-1px); box-shadow: 0 10px 25px rgba(99,102,241,0.35); }
        .submit-btn:active { transform: translateY(0); }
        .toggle-btn { color: #6366f1; }
        .toggle-btn:hover { color: #a5b4fc; }
    </style>
</head>
<body class="min-h-screen admin-gradient grid-pattern flex">

    <div class="flex w-full min-h-screen">

        {{-- ── LEFT: Admin Branding Panel ── --}}
        <div class="hidden lg:flex lg:w-[45%] xl:w-[40%] flex-col justify-between px-14 py-14 relative overflow-hidden"
             style="background: linear-gradient(160deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);">

            {{-- Decorative elements --}}
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full"
                 style="background: radial-gradient(circle, rgba(99,102,241,0.25), transparent); transform: translate(30%, -30%);"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full"
                 style="background: radial-gradient(circle, rgba(129,140,248,0.2), transparent); transform: translate(-30%, 30%);"></div>
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:32px 32px;"></div>

            {{-- Top: Logo & Name --}}
            <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background:rgba(99,102,241,0.3);border:1px solid rgba(165,180,252,0.3);">
                    <svg class="h-7 w-7 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color:rgba(199,210,254,0.6);">Administration</p>
                    <h1 class="text-lg font-bold text-white">{{ config('app.name') }}</h1>
                </div>
            </div>

            {{-- Centre: Headline --}}
            <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold mb-8 w-fit"
                     style="border:1px solid rgba(165,180,252,0.25);background:rgba(99,102,241,0.15);color:rgba(199,210,254,0.9);">
                    <span class="h-2 w-2 rounded-full bg-indigo-400" style="animation:pulse 2s infinite;"></span>
                    Staff Access Only
                </div>

                <h2 class="text-4xl xl:text-5xl font-extrabold leading-tight text-white">
                    School<br>
                    <span style="color:#a5b4fc;">Management</span><br>
                    Console
                </h2>

                <p class="mt-6 text-sm leading-relaxed max-w-xs" style="color:rgba(199,210,254,0.65);">
                    Centralised control for admissions, academics, finance, staff management and reporting.
                </p>

                {{-- Feature list --}}
                <div class="mt-10 space-y-3.5">
                    @foreach([
                        ['Students & Admissions', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['Results & Report Cards', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['Fee Management & Billing', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['Staff & HR Records', 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ] as [$label, $path])
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                             style="background:rgba(99,102,241,0.2);border:1px solid rgba(165,180,252,0.2);">
                            <svg class="h-4 w-4" style="color:#a5b4fc;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                            </svg>
                        </div>
                        <span class="text-sm" style="color:rgba(199,210,254,0.75);">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom --}}
            <div class="relative z-10">
                <p class="text-xs" style="color:rgba(165,180,252,0.35);">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>

        {{-- ── RIGHT: Login Form Panel ── --}}
        <div class="flex flex-1 items-center justify-center bg-slate-950 px-6 py-14 sm:px-12 lg:border-l lg:border-slate-800">
            <div class="w-full max-w-sm">

                {{-- Mobile header --}}
                <div class="mb-8 lg:hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-widest">Administration</p>
                            <p class="text-sm font-bold text-white">{{ config('app.name') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-white">Admin Sign In</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Restricted to authorised staff members only.</p>
                </div>

                {{-- Success (after password reset) --}}
                @if(session('status'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-700/40 bg-emerald-900/20 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-emerald-400">{{ session('status') }}</p>
                </div>
                @endif

                {{-- Error --}}
                @if($errors->has('email'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-800/50 bg-red-900/20 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                    </svg>
                    <p class="text-sm text-red-400">{{ $errors->first('email') }}</p>
                </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75-9.75-6.75"/>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="admin@school.edu"
                                   class="input-field w-full rounded-xl border border-slate-700 bg-slate-900 py-3 pl-10 pr-4 text-sm text-white placeholder:text-slate-600 transition">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                                </svg>
                            </span>
                            <input type="password" name="password" id="admin_password" required
                                   placeholder="••••••••"
                                   class="input-field w-full rounded-xl border border-slate-700 bg-slate-900 py-3 pl-10 pr-10 text-sm text-white placeholder:text-slate-600 transition">
                            <button type="button" onclick="togglePassword('admin_password','admin_eye')"
                                    class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-3.5 transition">
                                <svg id="admin_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-slate-600 bg-slate-800 accent-indigo-500">
                            <span class="text-xs text-slate-500">Remember this device</span>
                        </label>
                        <a href="{{ route('admin.password.request') }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit"
                            class="submit-btn mt-2 flex w-full items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-bold text-white shadow-lg transition duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/>
                        </svg>
                        Sign In to Admin Console
                    </button>
                </form>

                {{-- Divider & Portal link --}}
                <div class="mt-8 border-t border-slate-800 pt-6">
                    <p class="text-center text-xs text-slate-600">Are you a student or parent?</p>
                    <a href="{{ route('portal.login') }}"
                       class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-xs font-semibold text-slate-400 transition hover:border-slate-600 hover:text-slate-300">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Go to Student & Parent Portal
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
