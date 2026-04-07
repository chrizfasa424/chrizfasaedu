<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — {{ $schoolName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .input-field { transition: all 0.2s; border: 1.5px solid #e2e8f0; }
        .input-field:focus { outline: none; border-color: {{ $primary }}; box-shadow: 0 0 0 3px {{ $primary }}26; }
        .submit-btn { background: {{ $primary }}; color: {{ $secondary }}; }
        .submit-btn:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 10px 25px {{ $primary }}40; }
        .card-shadow { box-shadow: 0 25px 60px rgba(0,0,0,0.10), 0 8px 20px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    {{-- Blobs --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
        <div class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="rounded-3xl bg-white card-shadow p-8">

            {{-- Icon --}}
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl mx-auto mb-6"
                 style="background:{{ $primary }}1a;border:1px solid {{ $primary }}33;">
                <svg class="h-7 w-7" style="color:{{ $primary }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-slate-900">Forgot Password?</h2>
                <p class="mt-2 text-sm text-slate-500">Enter your email and we'll send a reset link.</p>
            </div>

            {{-- Success --}}
            @if(session('status'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
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

            <form method="POST" action="{{ route('portal.password.email') }}" class="space-y-4">
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

                <button type="submit" class="submit-btn w-full flex items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold shadow-lg transition duration-200 mt-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('portal.login') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 transition hover:text-slate-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/>
                    </svg>
                    Back to Student Portal Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
