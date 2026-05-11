<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $school?->name ?? 'ChrizFasa Academy' }} | {{ trim((string) ($publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School')) }}</title>
    @php
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $seoHomeTitle = ($school?->name ?? 'ChrizFasa Academy') . ' | ' . trim((string) ($publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School'));
        $seoHomeDescription = trim((string) ($publicPage['hero_subtitle'] ?? ''));
        $seoHomeLogo = $school?->logo ? asset('storage/' . ltrim($school->logo, '/')) : '';
    @endphp
    @include('public.partials.seo', [
        'title' => $seoHomeTitle,
        'description' => $seoHomeDescription,
        'canonical' => route('public.home'),
        'type' => 'website',
        'schemaType' => 'WebSite',
        'siteName' => $school?->name ?? 'ChrizFasa Academy',
        'image' => $seoHomeLogo,
        'school' => $school,
    ])
    @if($faviconPath)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ltrim($faviconPath, '/')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700;9..144,800&family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public.partials.nav-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
        $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
        $tailwindThemeVars = \App\Support\ThemePalette::tailwindCssVars($theme);
    @endphp
<style>
    :root {
        --theme-focus: rgba(15, 118, 110, 0.25);
    }

    body {
        text-align: justify;
        text-justify: inter-word;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        text-align: left;
        text-justify: auto;
    }

    html {
        scroll-behavior: smooth;
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    a,
    button,
    input,
    select,
    textarea {
        transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    a:focus-visible,
    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px var(--theme-focus);
    }

    .theme-nav-link,
    .theme-submenu-link,
    .theme-mobile-submenu-link {
        border-radius: 0.7rem;
    }

    .theme-nav-link {
        color: #475569;
    }

    @keyframes fx-menu-hover-blink {
        0%, 49% {
            box-shadow: none;
        }
        50%, 100% {
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--submenu-secondary, #DFE753) 78%, transparent),
                        0 0 15px color-mix(in srgb, var(--submenu-secondary, #DFE753) 40%, transparent);
        }
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-nav-link-active {
        background-color: var(--submenu-primary, #25333E) !important;
        color: #ffffff !important;
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible,
    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible,
    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        animation: fx-menu-hover-blink 0.14s steps(2, end) infinite;
    }

    .theme-header-action-outline {
        border-color: rgba(255, 255, 255, 0.45);
        color: #ffffff;
    }

    .theme-header-action-outline:hover,
    .theme-header-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-header-action-solid {
        background-color: #ffffff;
        color: var(--submenu-primary, #25333E);
    }

    .theme-header-action-solid:hover,
    .theme-header-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-mobile-action-outline {
        border-color: var(--submenu-primary, #25333E);
        color: var(--submenu-primary, #25333E);
    }

    .theme-mobile-action-outline:hover,
    .theme-mobile-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-mobile-action-solid {
        background-color: var(--submenu-primary, #25333E);
        color: #ffffff;
    }

    .theme-mobile-action-solid:hover,
    .theme-mobile-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-cta-solid {
        background-color: var(--submenu-primary, #25333E);
        border: 1px solid var(--submenu-primary, #25333E);
        color: #ffffff;
    }

    .theme-cta-outline {
        background-color: #ffffff;
        border: 1px solid var(--submenu-primary, #25333E);
        color: var(--submenu-primary, #25333E);
    }

    .theme-cta-solid:hover,
    .theme-cta-solid:focus-visible,
    .theme-cta-outline:hover,
    .theme-cta-outline:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        border-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-submenu-panel {
        background-color: var(--submenu-primary, #25333E);
        border-color: var(--submenu-primary, #25333E);
    }

    .theme-submenu-heading {
        color: rgba(255, 255, 255, 0.72);
        opacity: 0.78;
    }

    .theme-submenu-link {
        color: #ffffff;
    }

    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-submenu-link-active {
        background-color: var(--submenu-primary, #25333E) !important;
        color: #ffffff !important;
    }

    .theme-mobile-submenu-link {
        color: var(--submenu-primary);
    }

    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-mobile-submenu-link-active {
        background-color: var(--submenu-primary, #25333E) !important;
        color: #ffffff !important;
    }

    .text-slate-950,
    .text-slate-900,
    .text-gray-900 {
        color: var(--theme-heading, #0F172A) !important;
    }

    .text-slate-700,
    .text-slate-600,
    .text-slate-500,
    .text-gray-700,
    .text-gray-600,
    .text-gray-500,
    .text-muted {
        color: var(--theme-body, #475569) !important;
    }

    .bg-slate-50,
    .bg-gray-50,
    .bg-brand-100,
    .bg-secondary-100 {
        background-color: var(--theme-soft-surface, #EEF6FF) !important;
    }

    .bg-white {
        background-color: var(--theme-surface, #FFFFFF) !important;
    }

    .text-slate-950,
    .text-slate-900,
    .text-gray-900 {
        color: var(--theme-heading, #0F172A) !important;
    }

    .text-slate-700,
    .text-slate-600,
    .text-slate-500,
    .text-gray-700,
    .text-gray-600,
    .text-gray-500,
    .text-muted {
        color: var(--theme-body, #475569) !important;
    }

    .bg-slate-50,
    .bg-gray-50,
    .bg-brand-100,
    .bg-secondary-100 {
        background-color: var(--theme-soft-surface, #EEF6FF) !important;
    }

    .bg-white {
        background-color: var(--theme-surface, #FFFFFF) !important;
    }

    [x-cloak] {
        display: none !important;
    }

    .rich-text-content p + p,
    .rich-text-content ul + p,
    .rich-text-content ol + p,
    .rich-text-content p + ul,
    .rich-text-content p + ol,
    .rich-text-content figure,
    .rich-text-content blockquote,
    .rich-text-content h2 + p,
    .rich-text-content h3 + p,
    .rich-text-content h4 + p {
        margin-top: 0.75rem;
    }
    .rich-text-content ul,
    .rich-text-content ol {
        margin-left: 1.25rem;
        list-style-position: outside;
    }
    .rich-text-content ul {
        list-style-type: disc;
    }
    .rich-text-content ol {
        list-style-type: decimal;
    }
    .rich-text-content blockquote {
        border-left: 3px solid rgba(45, 29, 92, 0.24);
        margin-top: 0.75rem;
        padding-left: 0.9rem;
        font-style: italic;
    }
    .rich-text-content a {
        color: var(--submenu-primary, #25333E);
        font-weight: 700;
        text-decoration: underline;
        text-decoration-thickness: 2px;
        text-underline-offset: 0.18em;
    }
    .rich-text-content img {
        display: block;
        max-width: 100%;
        border-radius: 1rem;
        box-shadow: 0 18px 38px -28px rgba(15, 23, 42, 0.45);
    }
    .rich-text-content figure {
        overflow: hidden;
    }
    .rich-text-content figcaption {
        margin-top: 0.6rem;
        color: #64748b;
        font-size: 0.875rem;
    }
    .rich-text-content h2,
    .rich-text-content h3,
    .rich-text-content h4 {
        color: var(--theme-heading, #0F172A);
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        line-height: 1.12;
    }
    .rich-text-display h2,
    .rich-text-display h3,
    .rich-text-display h4,
    .rich-text-display p:first-child {
        font-size: clamp(1.95rem, 4vw, 3rem);
        font-weight: 600;
        line-height: 1.08;
    }
    .rich-text-display p:not(:first-child) {
        font-size: 1rem;
        line-height: 1.7;
    }
    .academics-heading,
    .academics-heading * {
        text-align: left !important;
        text-justify: auto !important;
    }
    .academics-heading h1,
    .academics-heading h2,
    .academics-heading h3,
    .academics-heading h4,
    .academics-heading p:first-child {
        font-size: clamp(1.875rem, 3vw, 2.25rem) !important;
        line-height: 1.35 !important;
        font-weight: 800 !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
    }
    .section-kicker-unified {
        color: var(--submenu-primary, #25333E);
        font-family: 'Outfit', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: 0.24em;
        line-height: 1.2;
        text-align: center;
        text-transform: uppercase;
    }

    .section-heading-unified {
        color: var(--theme-heading, #0F172A);
        font-family: 'Outfit', sans-serif;
        font-size: 1.875rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
        text-align: center;
    }

    @media (min-width: 640px) {
        .section-heading-unified {
            font-size: 2.25rem;
        }
    }
    .rich-text-content-inverse blockquote {
        border-left-color: rgba(223, 231, 83, 0.75);
    }
    .rich-text-content-inverse a,
    .rich-text-content-inverse h2,
    .rich-text-content-inverse h3,
    .rich-text-content-inverse h4,
    .rich-text-content-inverse figcaption {
        color: #ffffff;
    }

    .student-life-fx-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        background-color: var(--submenu-secondary, #DFE753) !important;
        border: 0 !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
    }

    .student-life-fx-card .student-life-fx-title {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .student-life-fx-card .student-life-fx-text {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .student-life-fx-card:hover,
    .student-life-fx-card:focus-within {
        transform: translateY(-4px);
        background-color: rgba(37, 51, 62, 0.86) !important;
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.45);
    }

    .student-life-fx-card:hover .student-life-fx-title,
    .student-life-fx-card:focus-within .student-life-fx-title {
        color: #ffffff !important;
    }

    .student-life-fx-card:hover .student-life-fx-text,
    .student-life-fx-card:focus-within .student-life-fx-text {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .contact-primary-card {
        background-color: var(--submenu-secondary, #DFE753) !important;
        border: 0 !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
        transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .contact-primary-title {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .contact-primary-text,
    .contact-primary-text p,
    .contact-primary-text span,
    .contact-primary-text a,
    .contact-primary-text strong,
    .contact-primary-card .rich-text-content,
    .contact-primary-card .rich-text-content * {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .contact-primary-card:hover,
    .contact-primary-card:focus-within {
        transform: translateY(-4px);
        background-color: rgba(37, 51, 62, 0.86) !important;
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.45);
    }

    .contact-primary-card:hover .contact-primary-title,
    .contact-primary-card:hover .contact-primary-text,
    .contact-primary-card:hover .contact-primary-text p,
    .contact-primary-card:hover .contact-primary-text span,
    .contact-primary-card:hover .contact-primary-text a,
    .contact-primary-card:hover .contact-primary-text strong,
    .contact-primary-card:hover .rich-text-content,
    .contact-primary-card:hover .rich-text-content *,
    .contact-primary-card:focus-within .contact-primary-title,
    .contact-primary-card:focus-within .contact-primary-text,
    .contact-primary-card:focus-within .contact-primary-text p,
    .contact-primary-card:focus-within .contact-primary-text span,
    .contact-primary-card:focus-within .contact-primary-text a,
    .contact-primary-card:focus-within .contact-primary-text strong,
    .contact-primary-card:focus-within .rich-text-content,
    .contact-primary-card:focus-within .rich-text-content * {
        color: rgba(255, 255, 255, 0.95) !important;
    }

    .contact-primary-card:hover .theme-cta-outline,
    .contact-primary-card:focus-within .theme-cta-outline {
        border-color: rgba(255, 255, 255, 0.92) !important;
        color: #ffffff !important;
        background-color: transparent !important;
    }

    .academics-primary-card {
        position: relative;
        overflow: hidden;
        background-color: var(--submenu-secondary, #DFE753) !important;
        border: 0 !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .academics-primary-card::before {
        content: none;
    }

    .academics-primary-card::after {
        content: none;
    }

    .academics-primary-card > * {
        position: relative;
        z-index: 2;
    }

    .academics-primary-card h3,
    .academics-primary-card .rich-text-content,
    .academics-primary-card .rich-text-content * {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .academics-primary-card:hover,
    .academics-primary-card:focus-within {
        background-color: rgba(37, 51, 62, 0.86) !important;
    }

    .academics-primary-card:hover h3,
    .academics-primary-card:hover .rich-text-content,
    .academics-primary-card:hover .rich-text-content *,
    .academics-primary-card:focus-within h3,
    .academics-primary-card:focus-within .rich-text-content,
    .academics-primary-card:focus-within .rich-text-content * {
        color: #ffffff !important;
    }

    @keyframes fx-academics-gold-bg-flash {
        0%, 100% {
            opacity: 0;
        }
        50% {
            opacity: 0.58;
        }
    }

    @keyframes fx-academics-gold-border-blink {
        0%, 49% {
            box-shadow: 0 18px 38px -28px rgba(15, 23, 42, 0.5);
        }
        50%, 100% {
            box-shadow: 0 22px 44px -30px rgba(15, 23, 42, 0.55);
        }
    }

    @keyframes fx-academics-heading-neon {
        0%, 100% {
            text-shadow:
                0 0 0 rgba(223, 231, 83, 0),
                0 0 0 rgba(223, 231, 83, 0);
        }
        50% {
            text-shadow:
                0 0 10px rgba(223, 231, 83, 0.95),
                0 0 22px rgba(223, 231, 83, 0.65);
        }
    }

    .testimonials-section {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 8% 12%, rgb(var(--tw-secondary-300) / 0.34), transparent 42%),
            radial-gradient(circle at 92% 86%, rgb(var(--tw-brand-200) / 0.26), transparent 46%),
            linear-gradient(180deg, #eaf2fb 0%, #dde8f7 100%);
    }

    .testimonials-section::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.35;
        background-image:
            linear-gradient(rgba(37, 51, 62, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(37, 51, 62, 0.08) 1px, transparent 1px);
        background-size: 34px 34px;
    }

    .testimonials-shell {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.75);
        background:
            linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.92));
        box-shadow:
            0 28px 70px -44px rgba(15, 23, 42, 0.5),
            inset 0 1px 0 rgba(255, 255, 255, 0.92);
    }

    .testimonials-shell::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.35), transparent 38%);
    }

    .testimonials-kicker {
        color: var(--submenu-primary, #25333E);
        font-weight: 800;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .testimonials-metric-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        border-radius: 999px;
        border: 1px solid rgba(37, 51, 62, 0.14);
        background: rgba(255, 255, 255, 0.72);
        padding: 0.42rem 0.9rem;
        box-shadow: 0 10px 22px -18px rgba(15, 23, 42, 0.35);
        backdrop-filter: blur(6px);
    }

    .testimonials-metric-chip-label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(37, 51, 62, 0.68);
    }

    .testimonials-metric-chip-value {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--submenu-primary, #25333E);
    }

    .testimonials-stage {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(37, 51, 62, 0.08);
        background: linear-gradient(165deg, rgba(37, 51, 62, 0.03), rgba(255, 255, 255, 0.76));
    }

    .testimonials-stage-title {
        color: var(--submenu-primary, #25333E);
        letter-spacing: -0.02em;
    }

    .testimonials-nav-btn {
        border: 1px solid rgba(37, 51, 62, 0.2);
        background: rgba(255, 255, 255, 0.88);
        color: var(--submenu-primary, #25333E);
        box-shadow: 0 8px 18px -14px rgba(37, 51, 62, 0.38);
        transition: border-color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
    }

    .testimonials-nav-btn:hover,
    .testimonials-nav-btn:focus-visible {
        border-color: rgba(37, 51, 62, 0.38);
        background: rgb(var(--tw-secondary-100) / 1);
        transform: translateY(-1px);
    }

    .testimonials-empty {
        position: relative;
        border: 1px dashed rgba(37, 51, 62, 0.24);
        background: rgba(255, 255, 255, 0.86);
        color: rgba(37, 51, 62, 0.84);
    }

    .testimonials-empty::before {
        content: "''";
        position: absolute;
        left: 1.2rem;
        top: 0.55rem;
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
        color: rgba(37, 51, 62, 0.16);
    }

    .testimonials-slider {
        background: rgba(255, 255, 255, 0.72);
    }

    .testimonials-card {
        position: relative;
        border: 1px solid rgba(37, 51, 62, 0.1);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 250, 255, 0.94));
        box-shadow: 0 16px 34px -26px rgba(15, 23, 42, 0.4);
    }

    .testimonials-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(125deg, rgba(255, 255, 255, 0.35), transparent 42%);
    }

    .testimonials-quote {
        position: relative;
        color: var(--submenu-primary, #25333E);
    }

    .testimonials-quote::before {
        content: "“";
        position: absolute;
        left: -0.4rem;
        top: -0.85rem;
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1;
        color: rgb(var(--tw-secondary-500) / 0.55);
    }

    .testimonials-author-role {
        color: rgba(37, 51, 62, 0.62);
    }

    .testimonials-dot {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .testimonials-dot:hover {
        transform: scale(1.05);
    }

    .teacher-marquee-section {
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.12), transparent 35%),
            radial-gradient(circle at bottom left, rgba(45, 29, 92, 0.08), transparent 44%),
            linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
    }

    .teacher-marquee-shell {
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.92));
        box-shadow: 0 26px 62px -44px rgba(15, 23, 42, 0.45);
    }

    .teacher-marquee-kicker {
        color: var(--submenu-primary, #25333E);
        font-weight: 800;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .teacher-marquee-window {
        overflow: hidden;
        background: transparent !important;
    }

    .teacher-marquee-track {
        --teacher-marquee-gap: 1.35rem;
        --teacher-marquee-card-width: 255px;
        display: grid;
        grid-template-columns: repeat(var(--teacher-columns, 4), minmax(220px, 280px));
        gap: 1.35rem;
        width: 100%;
        padding: 0;
        justify-content: center;
    }

    .teacher-marquee-track.is-static {
        animation: none;
    }

    .teacher-marquee-track.is-scrolling {
        display: flex;
        width: max-content;
        gap: 0;
        animation: teacher-marquee-scroll 34s linear infinite;
        will-change: transform;
    }

    .teacher-marquee-track.is-scrolling:hover {
        animation-play-state: paused;
    }

    .teacher-marquee-track.is-scrolling .teacher-marquee-card {
        flex: 0 0 var(--teacher-marquee-card-width);
        width: var(--teacher-marquee-card-width);
        margin-right: var(--teacher-marquee-gap);
    }

    @keyframes teacher-marquee-scroll {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-50%);
        }
    }

    .teacher-marquee-card {
        display: block;
        min-width: 220px;
        max-width: 280px;
        overflow: hidden;
        border-radius: 1.2rem;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(37, 51, 62, 0.14);
        padding: 0;
        margin-inline: auto;
    }

    .teacher-marquee-media {
        position: relative;
        overflow: hidden;
        width: 200px;
        height: 200px;
        margin: 0.95rem auto 0.2rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #dfe7f5, #e4edf9);
    }

    .teacher-marquee-media img {
        display: block;
        height: 100%;
        width: 100%;
        object-fit: cover;
        object-position: center top;
        transform-origin: top center;
        transition: transform 0.33s ease;
    }

    .teacher-marquee-media-fallback {
        display: flex;
        height: 100%;
        width: 100%;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #25333E, #36516b);
        color: #f8fafc;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: 0.1em;
    }

    .teacher-marquee-hover {
        position: absolute;
        left: 50%;
        bottom: 0.95rem;
        transform: translate(-50%, 14px);
        border-radius: 999px;
        background: rgba(37, 51, 62, 0.92);
        color: #ffffff;
        padding: 0.42rem 0.75rem;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        opacity: 0;
        transition: opacity 0.26s ease, transform 0.26s ease;
        pointer-events: none;
    }

    .teacher-marquee-avatar {
        display: none;
    }

    .teacher-marquee-meta {
        min-width: 0;
        padding: 1rem 1rem 1.1rem;
        text-align: center;
    }

    .teacher-marquee-name {
        color: var(--submenu-hover-text, #25333E) !important;
        font-size: 1.06rem;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .teacher-marquee-role {
        color: var(--submenu-primary, #25333E) !important;
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 0.35rem;
        line-height: 1.45;
    }

    .teacher-marquee-card:hover .teacher-marquee-hover {
        opacity: 1;
        transform: translate(-50%, 0);
    }

    .teacher-marquee-card:hover .teacher-marquee-media img {
        transform: scale(1.05);
    }

    @media (max-width: 767px) {
        .teacher-marquee-track.is-static {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .teacher-marquee-track.is-scrolling {
            --teacher-marquee-card-width: 220px;
        }

        .teacher-marquee-name {
            font-size: 1rem;
        }
    }

    .why-enhance-section {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(45, 29, 92, 0.08), transparent 36%),
            radial-gradient(circle at bottom right, rgba(223, 231, 83, 0.14), transparent 44%),
            linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
    }

    .why-enhance-shell {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        background: linear-gradient(165deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.86));
        box-shadow: 0 28px 72px -42px rgba(15, 23, 42, 0.45);
    }

    .why-enhance-shell::before {
        content: "";
        position: absolute;
        pointer-events: none;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.52), transparent 35%);
    }

    .why-enhance-kicker {
        color: var(--submenu-primary, #25333E);
        font-weight: 800;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .why-enhance-intro {
        border-radius: 1.35rem;
        background: #ffffff;
        padding: 1.1rem 1.2rem;
        box-shadow: 0 14px 36px -28px rgba(15, 23, 42, 0.38);
    }

    .why-enhance-intro p {
        color: #334155 !important;
        line-height: 1.68;
    }

    .why-enhance-intro h2,
    .why-enhance-intro h3,
    .why-enhance-intro h4,
    .why-enhance-intro strong {
        color: #0f172a !important;
    }

    .why-enhance-intro ul {
        margin: 0.8rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.7rem;
    }

    .why-enhance-intro li {
        position: relative;
        border-radius: 0.9rem;
        background: var(--submenu-secondary, #DFE753);
        padding: 0.72rem 0.86rem 0.72rem 2.18rem;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .why-enhance-intro li strong {
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .why-enhance-intro li::before {
        content: "";
        position: absolute;
        left: 0.72rem;
        top: 0.9rem;
        width: 0.82rem;
        height: 0.82rem;
        border-radius: 999px;
        background: var(--submenu-primary, #25333E);
        box-shadow: 0 0 0 3px rgba(223, 231, 83, 0.4);
    }

    .why-enhance-card {
        min-height: 12.5rem;
        border-radius: 1.2rem;
        overflow: hidden;
        box-shadow: 0 14px 34px -26px rgba(15, 23, 42, 0.45);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .why-enhance-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 48px -28px rgba(15, 23, 42, 0.5);
    }

    .why-enhance-card-body {
        position: relative;
        z-index: 2;
        display: flex;
        min-height: 100%;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1rem;
    }

    .why-enhance-card-body h3 {
        color: #ffffff !important;
        font-weight: 800;
        line-height: 1.2;
    }

    .why-enhance-card-body .rich-text-content,
    .why-enhance-card-body .rich-text-content * {
        color: rgba(255, 255, 255, 0.94) !important;
    }

    :root {
        --fx-ease-premium: cubic-bezier(.22, .61, .36, 1);
    }

    .section-kicker-unified {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        letter-spacing: 0.22em;
    }

    .section-heading-unified {
        font-family: "Fraunces", "Outfit", serif;
        font-variation-settings: "SOFT" 60, "WONK" 0;
        letter-spacing: -0.02em;
        line-height: 1.04;
    }

    [data-reveal] {
        opacity: 0;
        transform: translate3d(0, 18px, 0);
        transition: opacity 0.7s var(--fx-ease-premium), transform 0.7s var(--fx-ease-premium);
        will-change: opacity, transform;
    }

    [data-reveal="left"] {
        transform: translate3d(-22px, 0, 0);
    }

    [data-reveal="right"] {
        transform: translate3d(22px, 0, 0);
    }

    [data-reveal].is-visible {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }

    .why-enhance-section {
        background:
            radial-gradient(circle at 8% 10%, rgba(245, 158, 11, 0.16), transparent 40%),
            radial-gradient(circle at 95% 88%, rgba(13, 148, 136, 0.14), transparent 38%),
            linear-gradient(180deg, #fbfdf9 0%, #f2f8f4 100%);
    }

    .why-enhance-shell {
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 2rem;
        box-shadow: 0 30px 70px -45px rgba(15, 23, 42, 0.52);
    }

    .why-enhance-card {
        min-height: 14.25rem;
        border: 1px solid rgba(255, 255, 255, 0.28);
        transition: transform 0.3s var(--fx-ease-premium), box-shadow 0.3s var(--fx-ease-premium);
    }

    .why-enhance-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 26px 56px -34px rgba(15, 23, 42, 0.6);
    }

    .why-enhance-card-body h3 {
        font-family: "Fraunces", "Outfit", serif;
        font-size: 1.35rem;
    }

    #academics {
        background:
            radial-gradient(circle at 92% 6%, rgba(245, 158, 11, 0.2), transparent 34%),
            radial-gradient(circle at 6% 90%, rgba(13, 148, 136, 0.16), transparent 38%),
            linear-gradient(180deg, #eef9f6 0%, #e8f5f1 100%) !important;
    }

    #academics > .relative.mx-auto > .rounded-3xl {
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.9));
        box-shadow: 0 28px 60px -40px rgba(15, 23, 42, 0.45);
    }

    .academics-heading {
        font-family: "Fraunces", "Outfit", serif;
        font-variation-settings: "SOFT" 60, "WONK" 0;
    }

    .academics-primary-card {
        border: 1px solid rgba(148, 163, 184, 0.28) !important;
        border-radius: 1.2rem !important;
        transition: transform 0.28s var(--fx-ease-premium), box-shadow 0.28s var(--fx-ease-premium), background-color 0.28s var(--fx-ease-premium);
    }

    .academics-primary-card:hover,
    .academics-primary-card:focus-within {
        transform: translateY(-5px);
        box-shadow: 0 22px 44px -30px rgba(15, 23, 42, 0.42);
    }

    #student-life {
        background:
            radial-gradient(circle at 12% 0%, rgba(245, 158, 11, 0.14), transparent 36%),
            linear-gradient(180deg, #fbfdf9 0%, #f2f8f4 100%);
    }

    .student-life-fx-card {
        border: 1px solid rgba(148, 163, 184, 0.24) !important;
        border-radius: 1.15rem !important;
        box-shadow: 0 18px 38px -30px rgba(15, 23, 42, 0.48);
        transition: transform 0.28s var(--fx-ease-premium), box-shadow 0.28s var(--fx-ease-premium), background-color 0.28s var(--fx-ease-premium);
    }

    .student-life-fx-card:hover,
    .student-life-fx-card:focus-within {
        transform: translateY(-6px);
        box-shadow: 0 25px 44px -34px rgba(15, 23, 42, 0.54);
    }

    .testimonials-shell {
        border: 1px solid rgba(255, 255, 255, 0.65);
        box-shadow: 0 30px 70px -46px rgba(15, 23, 42, 0.5);
    }

    .testimonials-section {
        background:
            radial-gradient(circle at 8% 12%, rgba(245, 158, 11, 0.16), transparent 42%),
            radial-gradient(circle at 92% 86%, rgba(20, 184, 166, 0.14), transparent 46%),
            linear-gradient(180deg, #f8fcf9 0%, #eef6f1 100%) !important;
    }

    .testimonials-stage {
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: linear-gradient(165deg, rgba(255, 255, 255, 0.78), rgba(255, 255, 255, 0.62));
        backdrop-filter: blur(6px);
    }

    .testimonials-card {
        border: 1px solid rgba(148, 163, 184, 0.24);
        box-shadow: 0 24px 44px -34px rgba(15, 23, 42, 0.42);
        transition: transform 0.28s var(--fx-ease-premium), box-shadow 0.28s var(--fx-ease-premium);
    }

    .testimonials-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 54px -36px rgba(15, 23, 42, 0.5);
    }

    .teacher-marquee-shell {
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 24px 56px -38px rgba(15, 23, 42, 0.4);
    }

    .teacher-marquee-section {
        background:
            radial-gradient(circle at top right, rgba(245, 158, 11, 0.12), transparent 35%),
            radial-gradient(circle at bottom left, rgba(13, 148, 136, 0.09), transparent 44%),
            linear-gradient(180deg, #f9fcfa 0%, #eff7f2 100%) !important;
    }

    .teacher-marquee-card {
        border: 1px solid rgba(148, 163, 184, 0.2);
        box-shadow: 0 16px 30px -24px rgba(15, 23, 42, 0.38);
        transition: transform 0.24s var(--fx-ease-premium), box-shadow 0.24s var(--fx-ease-premium);
    }

    .teacher-marquee-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 38px -28px rgba(15, 23, 42, 0.46);
    }

    @media (prefers-reduced-motion: reduce) {
        [data-reveal],
        [data-reveal="left"],
        [data-reveal="right"] {
            opacity: 1;
            transform: none;
            transition: none;
        }
    }
</style>
</head>
<?php
    $schoolName = $school?->name ?? 'ChrizFasa Academy';
    $metrics = $publicPage['metrics'] ?? [];
    $whyChooseUs = $publicPage['why_choose_us'] ?? [];
    $whyChooseUsLabel = trim((string) ($publicPage['why_choose_us_label'] ?? 'Why Choose Us'));
    $whyChooseUsIntro = trim((string) ($publicPage['why_choose_us_intro'] ?? ''));
    $teachersMarqueeHeading = trim((string) ($publicPage['teachers_marquee_heading'] ?? 'Our Qualified Teachers'));
    $teachersMarqueeIntro = trim((string) ($publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.'));
    $programsLabel = trim((string) ($publicPage['programs_label'] ?? 'Programs'));
    $admissionsLabel = trim((string) ($publicPage['admissions_label'] ?? 'Admissions'));
    $admissionsProcessLabel = trim((string) ($publicPage['admissions_process_label'] ?? 'Admissions Process'));
    $academicsLabel = trim((string) ($publicPage['academics_label'] ?? 'Academics'));
    $facilitiesLabel = trim((string) ($publicPage['facilities_label'] ?? 'Facilities'));
    $aboutLabel = trim((string) ($publicPage['about_label'] ?? 'About Us'));
    $studentLifeLabel = trim((string) ($publicPage['student_life_label'] ?? 'Student Life'));
    $studentLifeHeading = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($publicPage['student_life_intro'] ?? ''))));
    if ($studentLifeHeading === '') {
        $studentLifeHeading = 'A vibrant school experience beyond the classroom.';
    }
    $parentsLabel = trim((string) ($publicPage['parents_label'] ?? 'Parents'));
    $contactLabel = trim((string) ($publicPage['contact_label'] ?? 'Contact'));
    $contactHeading = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($publicPage['contact_intro'] ?? ''))));
    if ($contactHeading === '') {
        $contactHeading = 'Reach us through our official channels.';
    }
    $headerApplyText = trim((string) ($publicPage['header_apply_text'] ?? 'Apply'));
    $headerPortalLoginText = trim((string) ($publicPage['header_portal_login_text'] ?? 'Portal Login'));
    $mobileApplyText = trim((string) ($publicPage['mobile_apply_text'] ?? 'Apply Now'));
    $mobilePortalLoginText = trim((string) ($publicPage['mobile_portal_login_text'] ?? 'Portal Login'));
    $heroSliderPlaceholderText = trim((string) ($publicPage['hero_slider_placeholder_text'] ?? 'Upload hero slider images from Admin Settings to personalize this section.'));
    $parentsPortalButtonText = trim((string) ($publicPage['parents_portal_button_text'] ?? 'Parent Portal Login'));
    $testimonialsBadgeText = trim((string) ($publicPage['testimonials_badge_text'] ?? ''));
    $testimonialsHeading = trim((string) ($publicPage['testimonials_heading'] ?? ''));
    $testimonialsSubheading = trim((string) ($publicPage['testimonials_subheading'] ?? ''));
    $testimonialsFormTitle = trim((string) ($publicPage['testimonials_form_title'] ?? ''));
    $testimonialsFormNameLabel = trim((string) ($publicPage['testimonials_form_name_label'] ?? ''));
    $testimonialsFormNamePlaceholder = trim((string) ($publicPage['testimonials_form_name_placeholder'] ?? ''));
    $testimonialsFormRoleLabel = trim((string) ($publicPage['testimonials_form_role_label'] ?? ''));
    $testimonialsFormRolePlaceholder = trim((string) ($publicPage['testimonials_form_role_placeholder'] ?? ''));
    $testimonialsFormRatingLabel = trim((string) ($publicPage['testimonials_form_rating_label'] ?? ''));
    $testimonialsFormMessageLabel = trim((string) ($publicPage['testimonials_form_message_label'] ?? ''));
    $testimonialsFormMessagePlaceholder = trim((string) ($publicPage['testimonials_form_message_placeholder'] ?? ''));
    $testimonialsFormSubmitText = trim((string) ($publicPage['testimonials_form_submit_text'] ?? ''));
    $testimonialsSliderTitle = trim((string) ($publicPage['testimonials_slider_title'] ?? ''));
    $testimonialsEmptyText = trim((string) ($publicPage['testimonials_empty_text'] ?? ''));
    $testimonialFormStartedAt = now()->timestamp;
    $testimonials = ($approvedTestimonials ?? collect())->values();
    $quickContactLabel = trim((string) ($publicPage['quick_contact_label'] ?? 'Quick Contact'));
    $contactPhoneLabel = trim((string) ($publicPage['contact_phone_label'] ?? 'Phone'));
    $contactWhatsappLabel = trim((string) ($publicPage['contact_whatsapp_label'] ?? 'WhatsApp'));
    $contactEmailLabel = trim((string) ($publicPage['contact_email_label'] ?? 'Email'));
    $contactAddressLabel = trim((string) ($publicPage['contact_address_label'] ?? 'Address'));
    $contactNotProvidedText = trim((string) ($publicPage['contact_not_provided_text'] ?? 'Not provided yet'));
    $mapEmbedTitleText = trim((string) ($publicPage['map_embed_title_text'] ?? 'School map'));
    $visitBookingButtonText = trim((string) ($publicPage['visit_booking_button_text'] ?? 'Visit Booking'));
    $quickApplyButtonText = trim((string) ($publicPage['quick_apply_button_text'] ?? 'Apply Now'));
    $menuOverviewSuffix = trim((string) ($publicPage['menu_overview_suffix'] ?? 'Overview'));
    $mobileMenuTitle = trim((string) ($publicPage['mobile_menu_title'] ?? 'Menu'));
    $programs = $publicPage['programs'] ?? [];
    $admissions = $publicPage['admissions'] ?? [];
    $academics = $publicPage['academics'] ?? [];
    $facilities = $publicPage['facilities'] ?? [];
    $aboutItems = $publicPage['about'] ?? [];
    $studentLifeItems = $publicPage['student_life'] ?? [];
    $parentsItems = $publicPage['parents'] ?? [];
    $contactItems = $publicPage['contact_items'] ?? [];
    $admissionSteps = $publicPage['admission_steps'] ?? [];
    $whyChooseUsBanners = collect($publicPage['why_choose_us_banners'] ?? [])
        ->map(function ($item) {
            return [
                'image' => trim((string) ($item['image'] ?? ($item['path'] ?? ''))),
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        })
        ->filter(function (array $item) {
            return $item['title'] !== '' || $item['description'] !== '' || $item['image'] !== '';
        })
        ->values();

    if ($whyChooseUsBanners->isEmpty()) {
        $whyChooseUsBanners = collect($whyChooseUs)
            ->map(function ($text, int $index) {
                return [
                    'image' => '',
                    'title' => 'Why Choose Us ' . ($index + 1),
                    'description' => trim((string) $text),
                ];
            })
            ->filter(function (array $item) {
                return $item['description'] !== '';
            })
            ->values();
    }

    $whyChooseUsFromList = collect($whyChooseUs)
        ->map(function ($text, int $index) {
            return [
                'image' => '',
                'title' => 'Why Choose Us ' . ($index + 1),
                'description' => trim((string) $text),
            ];
        })
        ->filter(fn (array $item) => $item['description'] !== '')
        ->values();

    $whyChooseUsDefaults = collect([
        ['image' => '', 'title' => 'Structured Curriculum', 'description' => 'A progressive curriculum that builds strong literacy, numeracy, and critical thinking skills.'],
        ['image' => '', 'title' => 'Safe and Inclusive Campus', 'description' => 'A secure school environment where every learner is respected, supported, and encouraged.'],
        ['image' => '', 'title' => 'Digital Learning Culture', 'description' => 'Technology-supported classrooms and modern learning tools that improve engagement and outcomes.'],
        ['image' => '', 'title' => 'Balanced Development', 'description' => 'Academics, discipline, leadership, and creativity developed together for all-round growth.'],
        ['image' => '', 'title' => 'Qualified Educators', 'description' => 'Dedicated teachers with strong instructional practice and consistent learner support.'],
        ['image' => '', 'title' => 'Character and Values', 'description' => 'Intentional moral instruction, responsibility, and service-based leadership training.'],
    ]);

    $whyChooseUsExistingTitles = $whyChooseUsBanners
        ->pluck('title')
        ->map(fn ($title) => \Illuminate\Support\Str::lower(trim((string) $title)))
        ->filter()
        ->values();

    $whyChooseUsBanners = $whyChooseUsBanners
        ->merge(
            $whyChooseUsFromList->reject(function (array $item) use ($whyChooseUsExistingTitles) {
                return $whyChooseUsExistingTitles->contains(
                    \Illuminate\Support\Str::lower(trim((string) ($item['title'] ?? '')))
                );
            })
        )
        ->merge(
            $whyChooseUsDefaults->reject(function (array $item) use ($whyChooseUsExistingTitles) {
                return $whyChooseUsExistingTitles->contains(
                    \Illuminate\Support\Str::lower(trim((string) ($item['title'] ?? '')))
                );
            })
        )
        ->take(6)
        ->values();

    $teachersMarqueeItems = collect($publicPage['teachers_marquee'] ?? [])
        ->map(function ($item) {
            return [
                'image' => trim((string) ($item['image'] ?? ($item['path'] ?? ''))),
                'name' => trim((string) ($item['name'] ?? '')),
                'role' => trim((string) ($item['role'] ?? '')),
            ];
        })
        ->filter(function (array $item) {
            return $item['image'] !== '' || $item['name'] !== '' || $item['role'] !== '';
        })
        ->take(6)
        ->values();

    if ($teachersMarqueeItems->isEmpty()) {
        $teachersMarqueeItems = collect([
            ['image' => '', 'name' => 'Rosy Janner', 'role' => 'Senior Finance Lecturer'],
            ['image' => '', 'name' => 'Mike Hussy', 'role' => 'Senior Finance Lecturer'],
            ['image' => '', 'name' => 'Daziy Millar', 'role' => 'Senior Finance Lecturer'],
            ['image' => '', 'name' => 'Kazi Fahim', 'role' => 'Senior Finance Lecturer'],
        ]);
    }

    $aboutBanners = collect($publicPage['about_banners'] ?? [])
        ->map(function ($item) {
            return [
                'image' => trim((string) ($item['image'] ?? ($item['path'] ?? ''))),
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        })
        ->filter(function (array $item) {
            return $item['title'] !== '' || $item['description'] !== '' || $item['image'] !== '';
        })
        ->values();

    if ($aboutBanners->isEmpty()) {
        $aboutBanners = collect($aboutItems)
            ->map(function ($item) {
                return [
                    'image' => '',
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return $item['title'] !== '' || $item['description'] !== '';
            })
            ->values();
    }

    $aboutItems = $aboutBanners
        ->map(fn (array $item) => ['title' => $item['title'], 'description' => $item['description']])
        ->filter(fn (array $item) => $item['title'] !== '')
        ->values()
        ->all();
    $parentsBanners = collect($publicPage['parents_banners'] ?? [])
        ->map(function ($item) {
            return [
                'image' => trim((string) ($item['image'] ?? ($item['path'] ?? ''))),
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        })
        ->filter(function (array $item) {
            return $item['title'] !== '' || $item['description'] !== '' || $item['image'] !== '';
        })
        ->values();

    if ($parentsBanners->isEmpty()) {
        $parentsBanners = collect($parentsItems)
            ->map(function ($item) {
                return [
                    'image' => '',
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return $item['title'] !== '' || $item['description'] !== '';
            })
            ->values();
    }

    $parentsItems = $parentsBanners
        ->map(fn (array $item) => ['title' => $item['title'], 'description' => $item['description']])
        ->filter(fn (array $item) => $item['title'] !== '')
        ->values()
        ->all();

    $studentLifeItems = collect($studentLifeItems)
        ->map(function ($item) {
            if (is_array($item)) {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            }

            return [
                'title' => trim((string) $item),
                'description' => '',
            ];
        })
        ->filter(fn (array $item) => $item['title'] !== '' || $item['description'] !== '')
        ->values();

    $studentLifeDefaults = collect([
        ['title' => 'Sports and Athletics', 'description' => 'Inter-house sports, fitness programs, and teamwork-focused competitions for all age groups.'],
        ['title' => 'Arts and Creativity', 'description' => 'Music, drama, visual arts, and creative showcases that build confidence and expression.'],
        ['title' => 'Leadership and Mentorship', 'description' => 'Student leadership opportunities, peer support systems, and character-building activities.'],
        ['title' => 'STEM and Innovation', 'description' => 'Hands-on science, coding, robotics, and practical innovation projects beyond the classroom.'],
        ['title' => 'Debate and Communication', 'description' => 'Public speaking, debate clubs, and communication practice for confident self-expression.'],
        ['title' => 'Community and Service', 'description' => 'Service initiatives that foster empathy, responsibility, and positive social impact.'],
    ]);

    $existingStudentLifeTitles = $studentLifeItems
        ->pluck('title')
        ->map(fn ($title) => \Illuminate\Support\Str::lower(trim((string) $title)))
        ->filter()
        ->values();

    $studentLifeItems = $studentLifeItems
        ->merge(
            $studentLifeDefaults->reject(function (array $item) use ($existingStudentLifeTitles) {
                return $existingStudentLifeTitles->contains(
                    \Illuminate\Support\Str::lower(trim((string) ($item['title'] ?? '')))
                );
            })
        )
        ->take(6)
        ->values()
        ->all();

    $heroSlides = collect($publicPage['hero_slides'] ?? [])
        ->filter(fn ($slide) => !empty($slide['path']))
        ->map(fn ($slide) => [
            'url' => asset('storage/' . ltrim($slide['path'], '/')),
            'caption' => $slide['caption'] ?? '',
        ])
        ->values();
    $academicsCollection = collect($academics)->values();
    $academicsSupportText = trim((string) ($publicPage['academics_support_text'] ?? 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.'));

    $academicHighlights = collect($publicPage['academic_highlights'] ?? [])
        ->map(function ($item) {
            return [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        })
        ->filter(function (array $item) {
            return $item['title'] !== '' || $item['description'] !== '';
        })
        ->values()
        ->take(2);

    if ($academicHighlights->isEmpty()) {
        $academicHighlights = $academicsCollection
            ->take(2)
            ->map(function ($item) {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return $item['title'] !== '' || $item['description'] !== '';
            })
            ->values();
    }

    if ($academicHighlights->count() < 2) {
        $academicHighlights = $academicHighlights
            ->merge([
                ['title' => 'STEM-First Curriculum', 'description' => 'Coding, robotics, and science labs integrated into junior and senior classes.'],
                ['title' => 'Student Leadership', 'description' => 'Public speaking, media, and entrepreneurship clubs with measurable outcomes.'],
            ])
            ->take(2)
            ->values();
    }

    $academicsUploadedVisuals = collect($publicPage['academics_visuals'] ?? [])
        ->map(function ($item) {
            if (is_array($item)) {
                return trim((string) ($item['image'] ?? ($item['path'] ?? '')));
            }

            return trim((string) $item);
        })
        ->filter()
        ->map(function (string $imagePath) {
            return \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : asset('storage/' . ltrim($imagePath, '/'));
        })
        ->values();

    $academicsVisuals = $academicsUploadedVisuals
        ->merge($heroSlides->pluck('url'))
        ->merge($aboutBanners
            ->pluck('image')
            ->filter()
            ->map(fn ($imagePath) => \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : asset('storage/' . ltrim($imagePath, '/'))))
        ->merge($whyChooseUsBanners
            ->pluck('image')
            ->filter()
            ->map(fn ($imagePath) => \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : asset('storage/' . ltrim($imagePath, '/'))))
        ->filter()
        ->unique()
        ->take(2)
        ->values();

    // Returns the first-item submenu URL for a section, or null if the section has no items.
    $firstSubmenuLink = function (string $section, array $items): ?string {
        $first = collect($items)->first();
        if ($first === null) { return null; }
        $title = is_array($first) ? trim((string) data_get($first, 'title', '')) : trim((string) $first);
        $slug  = \Illuminate\Support\Str::slug($title);
        return $slug !== '' ? route('public.submenu', ['section' => $section, 'slug' => $slug]) : null;
    };

    $menuSections = [
        ['label' => 'Home', 'id' => 'home', 'link' => route('public.home'), 'items' => []],
        ['label' => ($programsLabel !== '' ? $programsLabel : 'Programs'), 'id' => 'programs', 'link' => $firstSubmenuLink('programs', $programs) ?? route('public.home'), 'items' => collect($programs)->pluck('title')->filter()->values()->all()],
        ['label' => ($admissionsLabel !== '' ? $admissionsLabel : 'Admissions'), 'id' => 'admissions', 'link' => $firstSubmenuLink('admissions', $admissions) ?? route('public.home'), 'items' => collect($admissions)->pluck('title')->filter()->values()->all()],
        ['label' => ($academicsLabel !== '' ? $academicsLabel : 'Academics'), 'id' => 'academics', 'link' => $firstSubmenuLink('academics', $academics) ?? route('public.home'), 'items' => collect($academics)->pluck('title')->filter()->values()->all()],
        ['label' => ($facilitiesLabel !== '' ? $facilitiesLabel : 'Facilities'), 'id' => 'facilities', 'link' => $firstSubmenuLink('facilities', $facilities) ?? route('public.home'), 'items' => collect($facilities)->filter()->values()->all()],
        ['label' => ($aboutLabel !== '' ? $aboutLabel : 'About Us'), 'id' => 'about', 'link' => $firstSubmenuLink('about', $aboutItems) ?? route('public.home'), 'items' => collect($aboutItems)->pluck('title')->filter()->values()->all()],
        ['label' => ($studentLifeLabel !== '' ? $studentLifeLabel : 'Student Life'), 'id' => 'student-life', 'link' => $firstSubmenuLink('student-life', $studentLifeItems) ?? route('public.home'), 'items' => collect($studentLifeItems)->pluck('title')->filter()->values()->all()],
        ['label' => ($contactLabel !== '' ? $contactLabel : 'Contact'), 'id' => 'contact', 'link' => route('public.contact'), 'items' => []],
    ];
    $siteBackgroundColor = $theme['site_background'];
    $headerBgColor = $theme['header'];
    $submenuPrimaryColor = $theme['primary']['500'];
    $submenuSecondaryColor = $theme['secondary']['500'];
    $submenuHoverTextColor = $theme['primary_text_on_secondary'];
    $themeHeadingColor = $theme['ink'];
    $themeBodyColor = $theme['muted'];
    $themeSurfaceColor = $theme['surface'];
    $themeSoftSurfaceColor = $theme['soft_surface'];
    $themeStyle = $theme['theme_style'];
?>
<body class="site-body text-ink antialiased" style="background-color: {{ $siteBackgroundColor ?? ($theme['site_background'] ?? '#F8FAFC') }}; color: {{ $themeBodyColor ?? ($theme['muted'] ?? '#475569') }}; --submenu-primary: {{ $submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#25333E') }}; --submenu-secondary: {{ $submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753') }}; --submenu-hover-text: {{ $submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#25333E') }}; --theme-heading: {{ $themeHeadingColor ?? ($theme['ink'] ?? '#0F172A') }}; --theme-body: {{ $themeBodyColor ?? ($theme['muted'] ?? '#475569') }}; --theme-surface: {{ $themeSurfaceColor ?? ($theme['surface'] ?? '#FFFFFF') }}; --theme-soft-surface: {{ $themeSoftSurfaceColor ?? ($theme['soft_surface'] ?? '#EEF6FF') }}; {{ $tailwindThemeVars }};">
    @include('public.partials.page-loader', ['school' => $school, 'primary' => $submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#25333E')])
    <div class="site-bg relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>
        @include('public.partials.nav', ['school' => $school, 'publicPage' => $publicPage, 'theme' => $theme, 'activeSection' => 'home'])

<main class="relative z-0 bg-pattern-grid">
            <x-hero-slider :school="$school" :public-page="$publicPage" />

            <section class="why-enhance-section py-14" data-reveal>
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="why-enhance-shell p-5 lg:p-7" data-reveal="up">
                        <div class="relative z-10 mb-6">
                            <p class="why-enhance-kicker text-base">{{ $whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us' }}</p>
                            <h2 class="mt-2 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">What Sets Us Apart</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600 sm:text-base">A clear learning philosophy, disciplined delivery, and values-driven mentorship that shapes confident, capable learners.</p>
                        </div>

                        <div class="relative z-10 grid gap-6 lg:grid-cols-12">
                            <div class="lg:col-span-5" data-reveal="left">
                                @if($whyChooseUsIntro !== '')
                                    <div class="why-enhance-intro rich-text-content text-sm">{!! \App\Support\RichText::render($whyChooseUsIntro) !!}</div>
                                @endif
                            </div>

                            <div class="lg:col-span-7" data-reveal="right">
                                <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($whyChooseUsBanners as $item)
                            @php
                                $bannerId = \Illuminate\Support\Str::slug($item['title'] ?: ('why-choose-us-' . $loop->index));
                                $imagePath = $item['image'] ?? '';
                                $hasImage = $imagePath !== '';
                                $imageUrl = $hasImage
                                    ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . ltrim($imagePath, '/')))
                                    : null;
                            @endphp
                            <article id="why-choose-us-{{ $bannerId }}" class="why-enhance-card group relative" data-reveal>
                                @if($hasImage)
                                    <img src="{{ $imageUrl }}" alt="{{ $item['title'] ?: (($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us') . ' Banner') }}" class="absolute inset-0 h-full w-full object-cover">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-600 to-secondary-500"></div>
                                    <div class="absolute -right-10 -top-8 h-36 w-36 rounded-full border border-white/10 bg-white/10 blur-sm"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/35 to-transparent"></div>
                                <div class="why-enhance-card-body">
                                    <h3 class="text-lg">{{ $item['title'] ?: ($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us') }}</h3>
                                    @if(!empty($item['description']))
                                        <div class="rich-text-content rich-text-content-inverse mt-1 text-sm font-semibold leading-relaxed text-white/95">{!! \App\Support\RichText::render($item['description']) !!}</div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div id="admissions" class="sr-only" aria-hidden="true"></div>

            <section id="academics" class="relative overflow-hidden bg-[#eef6ff] py-10" data-reveal>
                <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(45, 29, 92, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45, 29, 92, 0.08) 1px, transparent 1px); background-size: 34px 34px;"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.68),_transparent_38%)]"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.4),_transparent_34%)]"></div>
                <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="rounded-3xl bg-white p-5 shadow-sm lg:p-6" data-reveal="up">
                        <div class="grid gap-4 lg:grid-cols-12 lg:items-stretch">
                            <div class="lg:col-span-6" data-reveal="left">
                                <p class="text-base font-bold uppercase tracking-[0.24em] text-blue-700">
                                        {{ $academicsLabel !== '' ? $academicsLabel : 'Academic Excellence' }}
                                </p>
                                <div class="academics-heading rich-text-content rich-text-display mt-2 text-slate-900 font-[Georgia,serif]">{!! \App\Support\RichText::render($publicPage['academics_intro'] ?? 'A Structured Learning Culture With Mentorship At The Center.') !!}</div>
                                <div class="rich-text-content mt-3 text-base leading-relaxed text-slate-700">{!! \App\Support\RichText::render($academicsSupportText !== '' ? $academicsSupportText : 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.') !!}</div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach($academicHighlights as $item)
                                        <article id="academics-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('academic-highlight-'.$loop->index)) }}" class="academics-primary-card rounded-2xl p-3.5">
                                            <h3 class="text-xl font-bold">{{ $item['title'] ?? '' }}</h3>
                                            <div class="rich-text-content mt-1.5 text-base leading-relaxed">{!! \App\Support\RichText::render($item['description'] ?? '') !!}</div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>

                            <div class="lg:col-span-6" data-reveal="right">
                                <div class="grid h-full grid-cols-2 gap-4">
                                    @for($i = 0; $i < 2; $i++)
                                        @php $visual = $academicsVisuals->get($i); @endphp
                                        <div class="min-h-[240px] lg:min-h-[260px] overflow-hidden rounded-2xl bg-slate-900">
                                            @if($visual)
                                                <img src="{{ $visual }}" alt="Academic Excellence Visual {{ $i + 1 }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full items-center justify-center bg-gradient-to-b from-slate-800 to-slate-900 p-4 text-center text-sm font-semibold text-slate-200">
                                                    Upload academic image from admin banners.
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div id="facilities" class="sr-only" aria-hidden="true"></div>

            <div id="about" class="sr-only" aria-hidden="true"></div>

            <section id="student-life" class="bg-slate-50 py-16" data-reveal>
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 mx-auto max-w-3xl text-center">
                        <p class="section-kicker-unified">{{ $studentLifeLabel !== '' ? $studentLifeLabel : 'Student Life' }}</p>
                        <h2 class="section-heading-unified mt-3">{{ $studentLifeHeading }}</h2>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($studentLifeItems as $item)
                            <div id="student-life-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('student-life-'.$loop->index)) }}" class="student-life-fx-card rounded-2xl bg-white p-5 shadow-sm" data-reveal>
                                <h3 class="student-life-fx-title text-lg font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                                <div class="student-life-fx-text rich-text-content mt-2 text-sm text-slate-600">{!! \App\Support\RichText::render($item['description'] ?? '') !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="parents" class="testimonials-section py-14 sm:py-16" data-reveal>
                <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
                    <div id="testimonials" class="testimonials-shell rounded-[2rem] p-5 sm:p-7 lg:p-8" data-reveal="up">
                        @php
                            $testimonialsCount = $testimonials->count();
                            $testimonialsAverage = $testimonialsCount > 0
                                ? number_format((float) $testimonials->avg('rating'), 1)
                                : '0.0';
                        @endphp
                        <div class="mx-auto max-w-4xl text-center">
                            @if($testimonialsBadgeText !== '')
                                <p class="section-kicker-unified">{{ $testimonialsBadgeText }}</p>
                            @endif
                            @if($testimonialsHeading !== '')
                                <h2 class="section-heading-unified mt-3">{{ $testimonialsHeading }}</h2>
                            @endif
                            @if($testimonialsSubheading !== '')
                                <p class="mx-auto mt-4 max-w-3xl text-center text-base leading-relaxed text-slate-600">{{ $testimonialsSubheading }}</p>
                            @endif
                            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                <span class="testimonials-metric-chip">
                                    <span class="testimonials-metric-chip-label">Rated</span>
                                    <span class="testimonials-metric-chip-value">{{ $testimonialsAverage }}/5</span>
                                </span>
                                <span class="testimonials-metric-chip">
                                    <span class="testimonials-metric-chip-label">Approved</span>
                                    <span class="testimonials-metric-chip-value">{{ number_format($testimonialsCount) }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="testimonials-stage mt-8 rounded-3xl p-4 sm:p-6">
                            <div class="mb-5 flex flex-wrap items-center justify-end gap-3">
                                @if($testimonials->count() > 1)
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-testimonial-prev class="testimonials-nav-btn inline-flex h-10 w-10 items-center justify-center rounded-full text-lg font-bold" aria-label="Previous testimonial">&lt;</button>
                                        <button type="button" data-testimonial-next class="testimonials-nav-btn inline-flex h-10 w-10 items-center justify-center rounded-full text-lg font-bold" aria-label="Next testimonial">&gt;</button>
                                    </div>
                                @endif
                            </div>

                            @if($testimonials->isEmpty())
                                <div class="testimonials-empty rounded-2xl px-5 py-10 text-center text-base font-medium">
                                    {{ $testimonialsEmptyText }}
                                </div>
                            @else
                                <div data-testimonial-slider class="testimonials-slider relative overflow-hidden rounded-2xl">
                                    <div data-testimonial-track class="flex transition-transform duration-500 ease-out">
                                        @foreach($testimonials as $testimonial)
                                            @php
                                                $tInitials = \Illuminate\Support\Str::upper(
                                                    collect(preg_split('/\s+/', trim($testimonial->full_name)))
                                                        ->filter()->take(2)
                                                        ->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))
                                                        ->implode('')
                                                ) ?: '?';
                                                $tStars = max(1, min(5, (int) $testimonial->rating));
                                            @endphp
                                            <article class="w-full shrink-0 p-4 sm:p-6" data-reveal>
                                                <div class="testimonials-card h-full rounded-2xl p-5 sm:p-6">
                                                    <div class="mb-4 flex items-center gap-1">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <svg class="h-4 w-4 {{ $s <= $tStars ? 'text-amber-400' : 'text-slate-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                        <span class="ml-1 text-xs font-semibold text-slate-500">{{ $tStars }}/5</span>
                                                    </div>

                                                    <blockquote class="testimonials-quote pl-4 text-xl font-black leading-relaxed sm:text-2xl">
                                                        {{ $testimonial->message }}
                                                    </blockquote>

                                                    <div class="mt-6 flex items-center gap-3">
                                                        @if($testimonial->student?->photo)
                                                            <img src="{{ asset('storage/' . $testimonial->student->photo) }}"
                                                                 alt="{{ $testimonial->full_name }}"
                                                                 class="h-11 w-11 shrink-0 rounded-full border-2 border-white object-cover shadow-sm">
                                                        @else
                                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-extrabold text-white shadow-sm"
                                                                 style="background:{{ $submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#25333E') }};">
                                                                {{ $tInitials }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-bold text-slate-800">{{ $testimonial->full_name }}</p>
                                                            @if(!empty($testimonial->role_title))
                                                                <p class="testimonials-author-role text-xs font-medium">{{ $testimonial->role_title }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                                @if($testimonials->count() > 1)
                                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2" data-testimonial-dots>
                                        @foreach($testimonials as $testimonial)
                                            <button
                                                type="button"
                                                data-testimonial-dot
                                                data-index="{{ $loop->index }}"
                                                class="testimonials-dot h-2.5 w-8 rounded-full {{ $loop->first ? 'bg-brand-700' : 'bg-slate-300' }}"
                                                aria-label="Go to testimonial {{ $loop->iteration }}"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="teacher-marquee-section py-10" data-reveal>
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="teacher-marquee-shell rounded-3xl p-5 lg:p-6" data-reveal="up">
                        <div class="mb-5 text-center">
                            <h2 class="section-heading-unified">
                                {{ $teachersMarqueeHeading !== '' ? $teachersMarqueeHeading : 'Our Qualified Teachers' }}
                            </h2>
                            @if($teachersMarqueeIntro !== '')
                                <p class="mx-auto mt-2 max-w-3xl text-center text-sm leading-relaxed text-slate-600 sm:text-base">{{ $teachersMarqueeIntro }}</p>
                            @endif
                        </div>

                        @php
                            $teacherMarqueeOriginalCount = $teachersMarqueeItems->count();
                            $teacherMarqueeShouldScroll = $teacherMarqueeOriginalCount > 4;
                            $teacherMarqueeColumns = max(1, min(4, $teacherMarqueeOriginalCount));
                            $teacherMarqueeRenderItems = $teacherMarqueeShouldScroll
                                ? $teachersMarqueeItems->concat($teachersMarqueeItems)->values()
                                : $teachersMarqueeItems;
                        @endphp

                        <div class="teacher-marquee-window rounded-2xl">
                            <div
                                class="teacher-marquee-track {{ $teacherMarqueeShouldScroll ? 'is-scrolling' : 'is-static' }}"
                                style="--teacher-columns: {{ $teacherMarqueeColumns }};"
                            >
                                @foreach($teacherMarqueeRenderItems as $item)
                                    @php
                                        $isCloneCard = $teacherMarqueeShouldScroll && $loop->index >= $teacherMarqueeOriginalCount;
                                    @endphp
                                    @php
                                        $name = trim((string) ($item['name'] ?? ''));
                                        $role = trim((string) ($item['role'] ?? ''));
                                        $imagePath = trim((string) ($item['image'] ?? ''));
                                        $hasImage = $imagePath !== '';
                                        $normalizedImagePath = str_replace('\\', '/', ltrim($imagePath, '/'));
                                        if (\Illuminate\Support\Str::startsWith($normalizedImagePath, 'storage/')) {
                                            $normalizedImagePath = \Illuminate\Support\Str::after($normalizedImagePath, 'storage/');
                                        }
                                        $imageUrl = null;
                                        if ($hasImage) {
                                            if (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])) {
                                                $imageUrl = $imagePath;
                                            } elseif ($normalizedImagePath !== '') {
                                                $imageUrl = \App\Support\MediaAsset::url($normalizedImagePath);
                                            }
                                        }
                                        $initials = \Illuminate\Support\Str::upper(
                                            collect(preg_split('/\s+/', $name !== '' ? $name : 'Teacher'))
                                                ->filter()
                                                ->take(2)
                                                ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
                                                ->implode('')
                                        );
                                    @endphp
                                    <article class="teacher-marquee-card" data-reveal @if($isCloneCard) aria-hidden="true" @endif>
                                        <div class="teacher-marquee-media">
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" alt="{{ $name !== '' ? $name : 'Teacher profile' }}">
                                            @else
                                                <div class="teacher-marquee-media-fallback">
                                                    <span>{{ $initials !== '' ? $initials : 'T' }}</span>
                                                </div>
                                            @endif
                                            <div class="teacher-marquee-hover">Profile Preview</div>
                                        </div>
                                        <div class="teacher-marquee-meta">
                                            <p class="teacher-marquee-name">{{ $name !== '' ? $name : 'Teacher' }}</p>
                                            @if($role !== '')
                                                <p class="teacher-marquee-role">{{ $role }}</p>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage])
    </div>
<script>
    (function () {
        const revealItems = Array.from(document.querySelectorAll('[data-reveal]'));
        if (!revealItems.length) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            revealItems.forEach((item) => item.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, io) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            });
        }, {
            root: null,
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.14,
        });

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 30, 240)}ms`;
            observer.observe(item);
        });
    })();
</script>
@if($testimonials->count() > 1)
    <script>
        (function () {
            const slider = document.querySelector('[data-testimonial-slider]');
            const track = document.querySelector('[data-testimonial-track]');
            const slides = Array.from(track ? track.children : []);
            const prevButton = document.querySelector('[data-testimonial-prev]');
            const nextButton = document.querySelector('[data-testimonial-next]');
            const dots = Array.from(document.querySelectorAll('[data-testimonial-dot]'));

            if (!slider || !track || slides.length < 2) {
                return;
            }

            let current = 0;
            let autoTimer = null;

            const render = (index) => {
                current = (index + slides.length) % slides.length;
                track.style.transform = `translateX(-${current * 100}%)`;

                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('bg-brand-700', dotIndex === current);
                    dot.classList.toggle('bg-slate-300', dotIndex !== current);
                });
            };

            const move = (direction) => render(current + direction);

            const startAuto = () => {
                if (autoTimer) {
                    clearInterval(autoTimer);
                }
                autoTimer = setInterval(() => move(1), 6000);
            };

            const stopAuto = () => {
                if (autoTimer) {
                    clearInterval(autoTimer);
                    autoTimer = null;
                }
            };

            if (prevButton) {
                prevButton.addEventListener('click', () => {
                    move(-1);
                    startAuto();
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    move(1);
                    startAuto();
                });
            }

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    const targetIndex = Number.parseInt(dot.getAttribute('data-index') || '0', 10);
                    if (Number.isInteger(targetIndex)) {
                        render(targetIndex);
                        startAuto();
                    }
                });
            });

            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);
            slider.addEventListener('focusin', stopAuto);
            slider.addEventListener('focusout', startAuto);

            render(0);
            startAuto();
        })();
    </script>
    @endif
</body>
</html>













