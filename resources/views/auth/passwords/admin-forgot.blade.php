<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password â€” {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-focus: rgba(14, 165, 233, 0.2);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 12% 18%, rgba(14, 165, 233, 0.18), transparent 34%),
                radial-gradient(circle at 84% 84%, rgba(15, 118, 110, 0.16), transparent 36%),
                linear-gradient(135deg, #0b1324 0%, #0f172a 48%, #111827 100%);
        }

        .input-field {
            transition: all 0.2s;
            border: 1.5px solid #334155;
            background: #0f172a;
            color: #f1f5f9;
        }

        .input-field::placeholder {
            color: #64748b;
        }

        .input-field:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px var(--auth-focus);
        }

        .submit-btn {
            background: linear-gradient(135deg, #0891b2, #0ea5e9);
        }

        .submit-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.35);
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--auth-focus);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">

            {{-- Icon --}}
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl mx-auto mb-6" style="background:rgba(14,165,233,0.2);border:1px solid rgba(125,211,252,0.3);">
                <svg class="h-7 w-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-white">Forgot Password?</h2>
                <p class="mt-2 text-sm text-slate-400">Enter your admin email and we'll send a reset link.</p>
            </div>

            {{-- Success --}}
            @if(session('status'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-700/40 bg-emerald-900/20 px-4 py-3">
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

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-4">
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
                               class="input-field w-full rounded-xl py-3 pl-10 pr-4 text-sm transition">
                    </div>
                </div>

                <button type="submit" class="submit-btn w-full flex items-center justify-center gap-2 rounded-xl py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 mt-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 transition hover:text-slate-300">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/>
                    </svg>
                    Back to Admin Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

