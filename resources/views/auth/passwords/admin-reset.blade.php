<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password â€” {{ config('app.name') }}</title>
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

        .toggle-btn {
            color: #38bdf8;
        }

        .toggle-btn:hover {
            color: #bae6fd;
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
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl mx-auto mb-6" style="background:rgba(14,165,233,0.2);border:1px solid rgba(125,211,252,0.3);">
                <svg class="h-7 w-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-white">Set New Password</h2>
                <p class="mt-2 text-sm text-slate-400">Choose a strong password for your admin account.</p>
            </div>

            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-800/50 bg-red-900/20 px-4 py-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                </svg>
                <ul class="text-sm text-red-400 space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75-9.75-6.75"/>
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email', $email) }}" required
                               class="input-field w-full rounded-xl py-3 pl-10 pr-4 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">New Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required placeholder="Min. 8 characters"
                               class="input-field w-full rounded-xl py-3 pl-10 pr-10 text-sm">
                        <button type="button" onclick="togglePassword('password','eye1')"
                                class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-3.5 transition">
                            <svg id="eye1" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Confirm Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="5.25" y="10.5" width="13.5" height="9.75" rx="2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>
                            </svg>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repeat password"
                               class="input-field w-full rounded-xl py-3 pl-10 pr-10 text-sm">
                        <button type="button" onclick="togglePassword('password_confirmation','eye2')"
                                class="toggle-btn absolute inset-y-0 right-0 flex items-center pr-3.5 transition">
                            <svg id="eye2" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn w-full flex items-center justify-center gap-2 rounded-xl py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 mt-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Reset Password
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

