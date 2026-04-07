<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — {{ $schoolName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .input-field { transition: all 0.2s; border: 1.5px solid #e2e8f0; }
        .input-field:focus { outline: none; border-color: {{ $primary }}; box-shadow: 0 0 0 3px {{ $primary }}26; }
        .submit-btn { background: {{ $primary }}; color: {{ $secondary }}; }
        .submit-btn:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 10px 25px {{ $primary }}40; }
        .card-shadow { box-shadow: 0 25px 60px rgba(0,0,0,0.10), 0 8px 20px rgba(0,0,0,0.06); }
        .toggle-btn { color: {{ $primary }}; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
        <div class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full opacity-10 blur-3xl" style="background:{{ $primary }};"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="rounded-3xl bg-white card-shadow p-8">

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl mx-auto mb-6"
                 style="background:{{ $primary }}1a;border:1px solid {{ $primary }}33;">
                <svg class="h-7 w-7" style="color:{{ $primary }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-slate-900">Set New Password</h2>
                <p class="mt-2 text-sm text-slate-500">Choose a strong password for your portal account.</p>
            </div>

            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                </svg>
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('portal.password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Email Address</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75-9.75-6.75"/>
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email', $email) }}" required
                               class="input-field w-full rounded-2xl bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">New Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required placeholder="Min. 8 characters"
                               class="input-field w-full rounded-2xl bg-slate-50 py-3.5 pl-12 pr-10 text-sm text-slate-900 placeholder:text-slate-400">
                        <button type="button" onclick="togglePassword('password','eye1')"
                                class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-4 transition">
                            <svg id="eye1" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Confirm Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                            </svg>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repeat password"
                               class="input-field w-full rounded-2xl bg-slate-50 py-3.5 pl-12 pr-10 text-sm text-slate-900 placeholder:text-slate-400">
                        <button type="button" onclick="togglePassword('password_confirmation','eye2')"
                                class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-4 transition">
                            <svg id="eye2" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn w-full flex items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold shadow-lg transition duration-200 mt-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Reset Password
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
