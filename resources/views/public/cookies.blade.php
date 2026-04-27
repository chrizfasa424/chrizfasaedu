<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies Policy | {{ $schoolName }}</title>
    @php
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
        $bodyColor = $theme['muted'] ?? '#475569';
        $headingColor = $theme['ink'] ?? '#0F172A';
        $surfaceColor = $theme['surface'] ?? '#FFFFFF';
        $siteBackground = $theme['site_background'] ?? '#F8FAFC';
        $cookiesTitle = trim((string) ($publicPage['cookies_policy_title'] ?? 'Cookies Policy')) ?: 'Cookies Policy';
        $cookiesIntro = trim((string) ($publicPage['cookies_policy_intro'] ?? ''));
        $cookiesContent = trim((string) ($publicPage['cookies_policy_content'] ?? ''));
        $policyEffectiveDate = trim((string) ($publicPage['legal_effective_date'] ?? ''));
        $tailwindThemeVars = \App\Support\ThemePalette::tailwindCssVars($theme);
        if ($policyEffectiveDate === '') {
            $policyEffectiveDate = $effectiveDate ?? now()->toFormattedDateString();
        }
    @endphp
    @if($faviconPath)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ltrim($faviconPath, '/')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public.partials.nav-styles')
</head>
<body class="text-muted antialiased" style="background-color: {{ $siteBackground }}; color: {{ $bodyColor }}; --submenu-primary: {{ $theme['primary']['500'] ?? '#2D1D5C' }}; --submenu-secondary: {{ $theme['secondary']['500'] ?? '#DFE753' }}; --submenu-hover-text: {{ $theme['primary_text_on_secondary'] ?? '#2D1D5C' }}; {{ $tailwindThemeVars }};">
    <div class="min-h-screen">
        @include('public.partials.nav', ['school' => $school, 'publicPage' => $publicPage, 'theme' => $theme])

        <main class="bg-pattern-grid">
            <section class="bg-gradient-to-br from-slate-50 to-white">
                <div class="mx-auto max-w-5xl px-6 py-14 lg:px-8 lg:py-16">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-brand-700">Legal</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-ink sm:text-5xl">{{ $cookiesTitle }}</h1>
                    @if($cookiesIntro !== '')
                        <div class="policy-rich-text mt-4 max-w-3xl text-base leading-relaxed text-muted sm:text-lg">{!! \App\Support\RichText::render($cookiesIntro) !!}</div>
                    @endif
                    <p class="mt-4 text-sm font-semibold text-slate-500">Effective Date: {{ $policyEffectiveDate }}</p>
                </div>
            </section>

            <section class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
                <div class="rounded-3xl bg-white p-6 shadow-sm sm:p-8" style="background-color: {{ $surfaceColor }};">
                    <div class="policy-rich-text text-base leading-relaxed text-muted">{!! \App\Support\RichText::render($cookiesContent) !!}</div>
                </div>
            </section>
        </main>

        @include('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage])
    </div>
    <style>
        .policy-rich-text p + p,
        .policy-rich-text h2,
        .policy-rich-text h3,
        .policy-rich-text h4,
        .policy-rich-text ul,
        .policy-rich-text ol,
        .policy-rich-text blockquote {
            margin-top: 0.9rem;
        }

        .policy-rich-text h2,
        .policy-rich-text h3,
        .policy-rich-text h4 {
            color: {{ $headingColor }};
            font-weight: 700;
            line-height: 1.3;
        }

        .policy-rich-text ul,
        .policy-rich-text ol {
            padding-left: 1.35rem;
        }

        .policy-rich-text ul {
            list-style: disc;
        }

        .policy-rich-text ol {
            list-style: decimal;
        }

        .policy-rich-text a {
            color: {{ $theme['primary']['600'] ?? '#2D1D5C' }};
            text-decoration: underline;
            text-underline-offset: 0.18em;
        }
    </style>
</body>
</html>
