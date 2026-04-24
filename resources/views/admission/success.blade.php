<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted — {{ $schoolName }}</title>
    @if($faviconPath)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ltrim($faviconPath, '/')) }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('public.partials.nav-styles')
    <style>
        :root {
            --success-focus: color-mix(in srgb, {{ $primary }} 24%, transparent);
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--success-focus);
        }
    </style>
</head>
<body style="background:{{ $bg }};font-family:'Manrope',sans-serif;min-height:100vh;">
@include('public.partials.page-loader', ['school' => $school, 'primary' => $primary])
@include('public.partials.nav', ['school' => $school, 'publicPage' => $publicPage, 'theme' => $theme])

    <div class="min-h-[calc(100vh-68px)] flex items-center justify-center py-16 px-4">
        <div class="w-full max-w-lg">
            {{-- Success card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">

                {{-- Top band --}}
                <div class="h-2" style="background:linear-gradient(90deg,{{ $primary }},{{ $secondary }});"></div>

                <div class="p-10 text-center">
                    {{-- Check icon --}}
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full" style="background:{{ $secondary }}20;">
                        <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="{{ $primary }}" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Application Submitted!</h1>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Thank you for applying to <strong class="text-slate-700">{{ $schoolName }}</strong>. Your application has been received and is currently under review.
                    </p>

                    {{-- Application number --}}
                    <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-5">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Your Application Number</p>
                        <p class="text-2xl font-extrabold tracking-wider" style="color:{{ $primary }};">{{ $appNum }}</p>
                        <p class="mt-2 text-xs text-slate-400">Keep this number safe for reference and follow-up enquiries.</p>
                    </div>

                    {{-- Email notice --}}
                    @if($parentEmail)
                    <div class="mt-5 rounded-xl border px-5 py-4 text-sm text-left {{ $emailSent ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' }}">
                        @if($emailSent)
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 shrink-0 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-green-800">Confirmation email sent</p>
                                    <p class="text-green-700 mt-0.5">A confirmation has been sent to <strong>{{ $parentEmail }}</strong>. Please check your inbox (and spam folder).</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 shrink-0 text-amber-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-amber-800">Application saved</p>
                                    <p class="text-amber-700 mt-0.5">Your application was saved successfully. We were unable to send a confirmation email at this time — please keep your application number safe.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- What's next --}}
                    <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50 px-6 py-5 text-left">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">What Happens Next?</p>
                        <ol class="space-y-2">
                            @foreach(['Documents are verified by our admissions team','You will be notified of the outcome via email','Approved applicants will receive enrolment instructions'] as $i => $step)
                            <li class="flex items-start gap-3 text-sm text-slate-600">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-extrabold text-white" style="background:{{ $primary }};">{{ $i + 1 }}</span>
                                {{ $step }}
                            </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('public.home') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5"
                           style="background:{{ $primary }};">
                            Back to Homepage
                        </a>
                        <a href="{{ route('admission.apply') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:-translate-y-0.5">
                            New Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage])
</body>
</html>
