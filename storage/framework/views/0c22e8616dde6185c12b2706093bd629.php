<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($school?->name ?? 'ChrizFasa Academy'); ?> | <?php echo e(trim((string) ($publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School'))); ?></title>
    <?php
        $faviconPath = data_get($school?->settings, 'branding.favicon');
    ?>
    <?php if($faviconPath): ?>
        <link rel="icon" type="image/png" href="<?php echo e(asset('storage/' . ltrim($faviconPath, '/'))); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php
        $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    ?>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '<?php echo e($theme['primary']['50']); ?>',
                            100: '<?php echo e($theme['primary']['100']); ?>',
                            200: '<?php echo e($theme['primary']['200']); ?>',
                            300: '<?php echo e($theme['primary']['300']); ?>',
                            400: '<?php echo e($theme['primary']['400']); ?>',
                            500: '<?php echo e($theme['primary']['500']); ?>',
                            600: '<?php echo e($theme['primary']['600']); ?>',
                            700: '<?php echo e($theme['primary']['700']); ?>'
                        },
                        secondary: {
                            50: '<?php echo e($theme['secondary']['50']); ?>',
                            100: '<?php echo e($theme['secondary']['100']); ?>',
                            200: '<?php echo e($theme['secondary']['200']); ?>',
                            300: '<?php echo e($theme['secondary']['300']); ?>',
                            400: '<?php echo e($theme['secondary']['400']); ?>',
                            500: '<?php echo e($theme['secondary']['500']); ?>',
                            600: '<?php echo e($theme['secondary']['600']); ?>',
                            700: '<?php echo e($theme['secondary']['700']); ?>'
                        },
                        accent: {
                            300: '<?php echo e($theme['accent']['300']); ?>',
                            400: '<?php echo e($theme['accent']['400']); ?>',
                            500: '<?php echo e($theme['accent']['500']); ?>'
                        },
                        ink: '<?php echo e($theme['ink']); ?>',
                        muted: '<?php echo e($theme['muted']); ?>'
                    },
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        display: ['Outfit', 'sans-serif']
                    },
                    boxShadow: {
                        soft: '0 12px 40px -18px rgba(15, 23, 42, 0.25)'
                    }
                }
            }
        };
    </script>
<style>
    :root {
        --theme-focus: rgba(15, 118, 110, 0.25);
    }

    body {
        text-align: justify;
        text-justify: inter-word;
    }

    body * {
        border-color: var(--submenu-primary, var(--primary, #2D1D5C)) !important;
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
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-nav-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
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
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-header-action-solid {
        background-color: #ffffff;
        color: var(--submenu-primary, #2D1D5C);
    }

    .theme-header-action-solid:hover,
    .theme-header-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-mobile-action-outline {
        border-color: var(--submenu-primary, #2D1D5C);
        color: var(--submenu-primary, #2D1D5C);
    }

    .theme-mobile-action-outline:hover,
    .theme-mobile-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-mobile-action-solid {
        background-color: var(--submenu-primary, #2D1D5C);
        color: #ffffff;
    }

    .theme-mobile-action-solid:hover,
    .theme-mobile-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-cta-solid {
        background-color: var(--submenu-primary, #2D1D5C);
        border: 1px solid var(--submenu-primary, #2D1D5C);
        color: #ffffff;
    }

    .theme-cta-outline {
        background-color: #ffffff;
        border: 1px solid var(--submenu-primary, #2D1D5C);
        color: var(--submenu-primary, #2D1D5C);
    }

    .theme-cta-solid:hover,
    .theme-cta-solid:focus-visible,
    .theme-cta-outline:hover,
    .theme-cta-outline:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        border-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-submenu-panel {
        background-color: var(--submenu-primary, #2D1D5C);
        border-color: var(--submenu-primary, #2D1D5C);
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
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-submenu-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        color: #ffffff !important;
    }

    .theme-mobile-submenu-link {
        color: var(--submenu-primary);
    }

    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-mobile-submenu-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
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
        color: var(--submenu-primary, #2D1D5C);
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
        font-size: clamp(1.25rem, 2.2vw, 1.5rem) !important;
        line-height: 1.35 !important;
        font-weight: 700 !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
    }
    .rich-text-section-intro h2,
    .rich-text-section-intro h3,
    .rich-text-section-intro h4,
    .rich-text-section-intro p:first-child {
        font-size: clamp(1.9rem, 3vw, 2.4rem);
        font-weight: 600;
        line-height: 1.12;
    }
    .rich-text-section-intro p:not(:first-child) {
        margin-top: 0.6rem;
        font-size: 1rem;
        line-height: 1.7;
        color: var(--theme-body, #475569);
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

    @keyframes fx-student-life-bg-flash {
        0%, 100% {
            opacity: 0.72;
        }
        50% {
            opacity: 0.96;
        }
    }

    @keyframes fx-student-life-border-blink {
        0%, 49% {
            border-color: #2D1D5C;
            box-shadow: 0 0 0 1px rgba(45, 29, 92, 0.65);
        }
        50%, 100% {
            border-color: rgba(45, 29, 92, 0.22);
            box-shadow: 0 0 0 1px rgba(45, 29, 92, 0.16);
        }
    }

    @keyframes fx-student-life-title-neon {
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

    .student-life-fx-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: var(--submenu-secondary, #DFE753) !important;
        border-color: var(--submenu-secondary, #DFE753) !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
    }

    .student-life-fx-card::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: #2D1D5C;
        opacity: 0;
        pointer-events: none;
        z-index: 0;
    }

    .student-life-fx-card > * {
        position: relative;
        z-index: 1;
    }

    .student-life-fx-card .student-life-fx-title {
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .student-life-fx-card .student-life-fx-text {
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .student-life-fx-card:hover,
    .student-life-fx-card:focus-within {
        transform: translateY(-10px);
        animation: fx-student-life-border-blink 0.12s steps(2, end) infinite;
    }

    .student-life-fx-card:hover::after,
    .student-life-fx-card:focus-within::after {
        opacity: 0.86;
        animation: fx-student-life-bg-flash 0.22s ease-in-out 2;
    }

    .student-life-fx-card:hover .student-life-fx-title,
    .student-life-fx-card:focus-within .student-life-fx-title {
        color: #ffffff !important;
        animation: fx-student-life-title-neon 0.7s ease-in-out infinite;
    }

    .student-life-fx-card:hover .student-life-fx-text,
    .student-life-fx-card:focus-within .student-life-fx-text {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .contact-primary-card {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        border-color: var(--submenu-primary, #2D1D5C) !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
    }

    .contact-primary-title {
        color: #ffffff !important;
    }

    .contact-primary-text,
    .contact-primary-text p,
    .contact-primary-text span,
    .contact-primary-text a,
    .contact-primary-text strong,
    .contact-primary-card .rich-text-content,
    .contact-primary-card .rich-text-content * {
        color: rgba(255, 255, 255, 0.92) !important;
    }

    #academics [class*="border"] {
        border-color: var(--submenu-primary, #2D1D5C) !important;
    }

    #parents [class*="border"] {
        border-color: var(--submenu-primary, #2D1D5C) !important;
    }

    .academics-primary-card {
        position: relative;
        overflow: hidden;
        background-color: var(--submenu-secondary, #DFE753) !important;
        border-color: var(--submenu-secondary, #DFE753) !important;
        box-shadow: 0 14px 34px -22px rgba(15, 23, 42, 0.55);
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
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
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .academics-primary-card:hover,
    .academics-primary-card:focus-within {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        border-color: var(--submenu-primary, #2D1D5C) !important;
    }

    #academics .academics-primary-card {
        border-color: var(--submenu-secondary, #DFE753) !important;
    }

    #academics .academics-primary-card:hover,
    #academics .academics-primary-card:focus-within {
        border-color: var(--submenu-primary, #2D1D5C) !important;
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
            border-color: #DFE753;
            box-shadow: 0 0 0 1px rgba(223, 231, 83, 0.72);
        }
        50%, 100% {
            border-color: rgba(223, 231, 83, 0.18);
            box-shadow: 0 0 0 1px rgba(223, 231, 83, 0.12);
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

    .teacher-marquee-section {
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.12), transparent 35%),
            radial-gradient(circle at bottom left, rgba(45, 29, 92, 0.08), transparent 44%),
            linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
    }

    .teacher-marquee-shell {
        border: 1px solid var(--submenu-primary, #2D1D5C) !important;
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.92));
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.9),
            0 26px 62px -44px rgba(15, 23, 42, 0.45);
    }

    .teacher-marquee-kicker {
        color: var(--submenu-primary, #2D1D5C);
        font-weight: 800;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .teacher-marquee-window {
        overflow: hidden;
        border-color: var(--submenu-primary, #2D1D5C) !important;
        background: var(--submenu-primary, #2D1D5C) !important;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    @keyframes teacher-marquee-scroll {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    .teacher-marquee-track {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        width: max-content;
        padding: 1rem 1.1rem;
        animation: teacher-marquee-scroll 34s linear infinite;
        will-change: transform;
    }

    .teacher-marquee-track:hover {
        animation-play-state: paused;
    }

    .teacher-marquee-track.is-static {
        width: 100%;
        justify-content: center;
        animation: none;
    }

    .teacher-marquee-card {
        display: flex;
        min-width: 260px;
        align-items: center;
        gap: 0.8rem;
        border: 1px solid rgba(223, 231, 83, 0.38) !important;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.55rem 0.8rem;
    }

    .teacher-marquee-avatar {
        display: flex;
        height: 3.3rem;
        width: 3.3rem;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid rgba(223, 231, 83, 0.7) !important;
        border-radius: 999px;
        background: linear-gradient(135deg, #2D1D5C, #4c3892);
        color: #f8fafc;
        font-weight: 800;
        letter-spacing: 0.08em;
    }

    .teacher-marquee-meta {
        min-width: 0;
    }

    .teacher-marquee-name {
        color: #ffffff !important;
        font-size: 0.98rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .teacher-marquee-role {
        color: rgba(255, 255, 255, 0.82) !important;
        font-size: 0.78rem;
        white-space: nowrap;
    }

    @media (max-width: 767px) {
        .teacher-marquee-card {
            min-width: 220px;
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
        border: 1px solid var(--submenu-primary, #2D1D5C) !important;
        border-radius: 2rem;
        background: linear-gradient(165deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.86));
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.88),
            0 28px 72px -42px rgba(15, 23, 42, 0.45);
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
        color: var(--submenu-primary, #2D1D5C);
        font-weight: 800;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .why-enhance-intro {
        border: 1px solid var(--submenu-primary, #2D1D5C) !important;
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
        border: 1px solid rgba(45, 29, 92, 0.26);
        border-radius: 0.9rem;
        background: var(--submenu-secondary, #DFE753);
        padding: 0.72rem 0.86rem 0.72rem 2.18rem;
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .why-enhance-intro li strong {
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .why-enhance-intro li::before {
        content: "";
        position: absolute;
        left: 0.72rem;
        top: 0.9rem;
        width: 0.82rem;
        height: 0.82rem;
        border-radius: 999px;
        background: var(--submenu-primary, #2D1D5C);
        box-shadow: 0 0 0 3px rgba(223, 231, 83, 0.4);
    }

    .why-enhance-card {
        min-height: 12.5rem;
        border: 1px solid var(--submenu-primary, #2D1D5C) !important;
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
</style>
</head>
<?php
    $schoolName = $school?->name ?? 'ChrizFasa Academy';
    $metrics = $publicPage['metrics'] ?? [];
    $whyChooseUs = $publicPage['why_choose_us'] ?? [];
    $whyChooseUsLabel = trim((string) ($publicPage['why_choose_us_label'] ?? 'Why Choose Us'));
    $whyChooseUsIntro = trim((string) ($publicPage['why_choose_us_intro'] ?? ''));
    $teachersMarqueeLabel = trim((string) ($publicPage['teachers_marquee_label'] ?? 'Our Teachers'));
    $teachersMarqueeHeading = trim((string) ($publicPage['teachers_marquee_heading'] ?? 'Meet Our Teaching Team'));
    $teachersMarqueeIntro = trim((string) ($publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.'));
    $programsLabel = trim((string) ($publicPage['programs_label'] ?? 'Programs'));
    $admissionsLabel = trim((string) ($publicPage['admissions_label'] ?? 'Admissions'));
    $admissionsProcessLabel = trim((string) ($publicPage['admissions_process_label'] ?? 'Admissions Process'));
    $academicsLabel = trim((string) ($publicPage['academics_label'] ?? 'Academics'));
    $facilitiesLabel = trim((string) ($publicPage['facilities_label'] ?? 'Facilities'));
    $aboutLabel = trim((string) ($publicPage['about_label'] ?? 'About Us'));
    $studentLifeLabel = trim((string) ($publicPage['student_life_label'] ?? 'Student Life'));
    $parentsLabel = trim((string) ($publicPage['parents_label'] ?? 'Parents'));
    $contactLabel = trim((string) ($publicPage['contact_label'] ?? 'Contact'));
    $headerApplyText = trim((string) ($publicPage['header_apply_text'] ?? 'Apply'));
    $headerPortalLoginText = trim((string) ($publicPage['header_portal_login_text'] ?? 'Portal Login'));
    $mobileApplyText = trim((string) ($publicPage['mobile_apply_text'] ?? 'Apply Now'));
    $mobilePortalLoginText = trim((string) ($publicPage['mobile_portal_login_text'] ?? 'Portal Login'));
    $heroSliderPlaceholderText = trim((string) ($publicPage['hero_slider_placeholder_text'] ?? 'Upload hero slider images from Admin Settings to personalize this section.'));
    $parentsPortalButtonText = trim((string) ($publicPage['parents_portal_button_text'] ?? 'Parent Portal Login'));
    $testimonialsBadgeText = trim((string) ($publicPage['testimonials_badge_text'] ?? 'Testimonials'));
    $testimonialsHeading = trim((string) ($publicPage['testimonials_heading'] ?? 'What Parents and Student Say'));
    $testimonialsSubheading = trim((string) ($publicPage['testimonials_subheading'] ?? 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.'));
    $testimonialsFormTitle = trim((string) ($publicPage['testimonials_form_title'] ?? 'Share Your Testimonial'));
    $testimonialsFormNameLabel = trim((string) ($publicPage['testimonials_form_name_label'] ?? 'Full Name'));
    $testimonialsFormNamePlaceholder = trim((string) ($publicPage['testimonials_form_name_placeholder'] ?? 'Enter your full name'));
    $testimonialsFormRoleLabel = trim((string) ($publicPage['testimonials_form_role_label'] ?? 'Role or Context'));
    $testimonialsFormRolePlaceholder = trim((string) ($publicPage['testimonials_form_role_placeholder'] ?? 'Parent, student, alumni, guardian, etc.'));
    $testimonialsFormRatingLabel = trim((string) ($publicPage['testimonials_form_rating_label'] ?? 'Rating'));
    $testimonialsFormMessageLabel = trim((string) ($publicPage['testimonials_form_message_label'] ?? 'Your Testimonial'));
    $testimonialsFormMessagePlaceholder = trim((string) ($publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...'));
    $testimonialsFormSubmitText = trim((string) ($publicPage['testimonials_form_submit_text'] ?? 'Submit Testimonial'));
    $testimonialsSliderTitle = trim((string) ($publicPage['testimonials_slider_title'] ?? 'Approved Testimonials'));
    $testimonialsEmptyText = trim((string) ($publicPage['testimonials_empty_text'] ?? 'No testimonials have been approved yet. Be the first to share your experience.'));
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

    $teachersMarqueeLoopItems = $teachersMarqueeItems->count() > 1
        ? $teachersMarqueeItems->concat($teachersMarqueeItems)->values()
        : $teachersMarqueeItems;

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
        ['label' => ($parentsLabel !== '' ? $parentsLabel : 'Parents'), 'id' => 'parents', 'link' => $firstSubmenuLink('parents', $parentsItems) ?? route('public.home'), 'items' => collect($parentsItems)->pluck('title')->filter()->values()->all()],
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
<body class="text-ink antialiased" style="background-color: <?php echo e($siteBackgroundColor ?? ($theme['site_background'] ?? '#F8FAFC')); ?>; color: <?php echo e($themeBodyColor ?? ($theme['muted'] ?? '#475569')); ?>; --submenu-primary: <?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>; --theme-heading: <?php echo e($themeHeadingColor ?? ($theme['ink'] ?? '#0F172A')); ?>; --theme-body: <?php echo e($themeBodyColor ?? ($theme['muted'] ?? '#475569')); ?>; --theme-surface: <?php echo e($themeSurfaceColor ?? ($theme['surface'] ?? '#FFFFFF')); ?>; --theme-soft-surface: <?php echo e($themeSoftSurfaceColor ?? ($theme['soft_surface'] ?? '#EEF6FF')); ?>;">
    <?php echo $__env->make('public.partials.page-loader', ['school' => $school, 'primary' => $submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>

        <header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur" style="background-color: <?php echo e($headerBgColor ?? ($theme['header'] ?? '#2D1D5C')); ?>;">
            <div class="mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-4 px-6 py-3 lg:px-8">
                <a href="<?php echo e(route('public.home')); ?>" class="flex shrink-0 items-center transition duration-200 hover:opacity-90">
                    <?php if($school?->logo): ?>
                        <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="<?php echo e($schoolName); ?> Logo" style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;object-fit:cover;border:1px solid rgba(255,255,255,0.2);background:#fff;">
                    <?php else: ?>
                        <div style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;letter-spacing:0.1em;color:#fff;">
                            <?php echo e(\Illuminate\Support\Str::upper(collect(preg_split('/\s+/', trim($schoolName)))->filter()->take(2)->map(fn($w) => \Illuminate\Support\Str::substr($w,0,1))->implode(''))); ?>

                        </div>
                    <?php endif; ?>
                </a>
                <nav class="hidden items-center justify-center gap-1 rounded-2xl border border-slate-200/90 bg-white/95 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex" style="--submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;">
                    <?php $__currentLoopData = $menuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $alignClass = ($loop->last || $loop->iteration >= count($menuSections) - 1) ? 'right-0' : 'left-0';
                        ?>
                        <div class="relative shrink-0" data-menu="<?php echo e($section['id']); ?>">
                            <?php if(!empty($section['items'])): ?>
                                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="submenu-<?php echo e($section['id']); ?>" class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    <?php echo e($section['label']); ?>

                                </button>
                                <div id="submenu-<?php echo e($section['id']); ?>" data-menu-panel class="absolute <?php echo e($alignClass); ?> top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                                    <div
                                        class="theme-submenu-panel rounded-2xl border p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur"
                                        style="--submenu-primary: <?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;"
                                    >
                                        <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]"><?php echo e($section['label']); ?></p>
                                        <a href="<?php echo e($section['link'] ?? ('#' . $section['id'])); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                            <?php echo e($section['label']); ?> <?php echo e($menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview'); ?>

                                        </a>
                                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)])); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200">
                                                <?php echo e($menuItem); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e($section['link'] ?? ('#' . $section['id'])); ?>" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    <?php echo e($section['label']); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
                <div class="flex items-center justify-end gap-2 sm:gap-3" style="--submenu-primary: <?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;">
                    <a href="<?php echo e(route('admission.apply')); ?>" class="theme-header-action-outline hidden items-center whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex"><?php echo e($headerApplyText !== '' ? $headerApplyText : 'Apply'); ?></a>
                    <a href="<?php echo e(route('portal.login')); ?>" class="theme-header-action-solid inline-flex items-center whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5"><?php echo e($headerPortalLoginText !== '' ? $headerPortalLoginText : 'Portal Login'); ?></a>
                    <button type="button" data-mobile-menu-toggle aria-expanded="false" aria-controls="mobile-menu" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/40 text-white transition duration-200 hover:bg-white/10 xl:hidden">
                        <svg data-mobile-menu-open-icon class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg data-mobile-menu-close-icon class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div data-mobile-menu-backdrop class="pointer-events-none fixed inset-0 z-40 bg-slate-950/40 opacity-0 transition duration-300 xl:hidden"></div>

            <aside id="mobile-menu" data-mobile-menu class="pointer-events-none fixed inset-y-0 right-0 z-50 w-full max-w-sm translate-x-full overflow-y-auto border-l border-slate-200 bg-white shadow-2xl transition duration-300 xl:hidden">
                <div class="sticky top-0 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">
                    <p class="font-display text-lg font-semibold text-slate-900"><?php echo e($mobileMenuTitle !== '' ? $mobileMenuTitle : 'Menu'); ?></p>
                    <button type="button" data-mobile-menu-close class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition duration-200 hover:bg-slate-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 py-5">
                    <div class="mb-4 flex flex-col gap-2" style="--submenu-primary: <?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;">
                        <a href="<?php echo e(route('admission.apply')); ?>" class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobileApplyText !== '' ? $mobileApplyText : 'Apply Now'); ?></a>
                        <a href="<?php echo e(route('portal.login')); ?>" class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobilePortalLoginText !== '' ? $mobilePortalLoginText : 'Portal Login'); ?></a>
                    </div>

                    <div class="space-y-2" style="--submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;">
                        <?php $__currentLoopData = $menuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                                <?php if(!empty($section['items'])): ?>
                                    <button type="button" data-mobile-submenu-toggle data-target="mobile-submenu-<?php echo e($section['id']); ?>" aria-expanded="false" class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200">
                                        <span><?php echo e($section['label']); ?></span>
                                        <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                                    </button>
                                    <div id="mobile-submenu-<?php echo e($section['id']); ?>" data-mobile-submenu-panel class="hidden px-4 pb-4" style="--submenu-primary: <?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor ?? ($theme['secondary']['500'] ?? '#DFE753')); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor ?? ($theme['primary_text_on_secondary'] ?? '#2D1D5C')); ?>;">
                                        <div class="space-y-1 border-l border-slate-200 pl-4">
                                            <a href="<?php echo e($section['link'] ?? ('#' . $section['id'])); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200"><?php echo e($section['label']); ?> <?php echo e($menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview'); ?></a>
                                            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)])); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200"><?php echo e($menuItem); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo e($section['link'] ?? ('#' . $section['id'])); ?>" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200"><?php echo e($section['label']); ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </aside>
        </header>

        <main class="relative z-0">
            <?php if (isset($component)) { $__componentOriginale74ef38c4f718abe5610e24f5e2f3fa8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale74ef38c4f718abe5610e24f5e2f3fa8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hero-slider','data' => ['school' => $school,'publicPage' => $publicPage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('hero-slider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['school' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($school),'public-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($publicPage)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale74ef38c4f718abe5610e24f5e2f3fa8)): ?>
<?php $attributes = $__attributesOriginale74ef38c4f718abe5610e24f5e2f3fa8; ?>
<?php unset($__attributesOriginale74ef38c4f718abe5610e24f5e2f3fa8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale74ef38c4f718abe5610e24f5e2f3fa8)): ?>
<?php $component = $__componentOriginale74ef38c4f718abe5610e24f5e2f3fa8; ?>
<?php unset($__componentOriginale74ef38c4f718abe5610e24f5e2f3fa8); ?>
<?php endif; ?>

            <?php if($teachersMarqueeItems->isNotEmpty()): ?>
                <section class="teacher-marquee-section border-t border-slate-200 py-10">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="teacher-marquee-shell rounded-3xl p-5 lg:p-6">
                            <div class="mb-4">
                                <p class="teacher-marquee-kicker text-xs"><?php echo e($teachersMarqueeLabel !== '' ? $teachersMarqueeLabel : 'Our Teachers'); ?></p>
                                <h2 class="mt-2 font-display text-2xl font-extrabold text-slate-900 sm:text-3xl">
                                    <?php echo e($teachersMarqueeHeading !== '' ? $teachersMarqueeHeading : 'Meet Our Teaching Team'); ?>

                                </h2>
                                <?php if($teachersMarqueeIntro !== ''): ?>
                                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600 sm:text-base"><?php echo e($teachersMarqueeIntro); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="teacher-marquee-window rounded-2xl border">
                                <div class="teacher-marquee-track <?php echo e($teachersMarqueeItems->count() > 1 ? '' : 'is-static'); ?>">
                                    <?php $__currentLoopData = $teachersMarqueeLoopItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $name = trim((string) ($item['name'] ?? ''));
                                            $role = trim((string) ($item['role'] ?? ''));
                                            $imagePath = trim((string) ($item['image'] ?? ''));
                                            $hasImage = $imagePath !== '';
                                            $imageUrl = $hasImage
                                                ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . ltrim($imagePath, '/')))
                                                : null;
                                            $initials = \Illuminate\Support\Str::upper(
                                                collect(preg_split('/\s+/', $name !== '' ? $name : 'Teacher'))
                                                    ->filter()
                                                    ->take(2)
                                                    ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
                                                    ->implode('')
                                            );
                                        ?>
                                        <article class="teacher-marquee-card">
                                            <div class="teacher-marquee-avatar">
                                                <?php if($hasImage): ?>
                                                    <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($name !== '' ? $name : 'Teacher profile'); ?>" class="h-full w-full object-cover">
                                                <?php else: ?>
                                                    <span><?php echo e($initials !== '' ? $initials : 'T'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="teacher-marquee-meta">
                                                <p class="teacher-marquee-name"><?php echo e($name !== '' ? $name : 'Teacher'); ?></p>
                                                <?php if($role !== ''): ?>
                                                    <p class="teacher-marquee-role"><?php echo e($role); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </article>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="why-enhance-section border-t border-slate-200 py-14">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="why-enhance-shell p-5 lg:p-7">
                        <div class="relative z-10 mb-6">
                            <p class="why-enhance-kicker text-xs"><?php echo e($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us'); ?></p>
                            <h2 class="mt-2 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">What Sets Us Apart</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600 sm:text-base">A clear learning philosophy, disciplined delivery, and values-driven mentorship that shapes confident, capable learners.</p>
                        </div>

                        <div class="relative z-10 grid gap-6 lg:grid-cols-12">
                            <div class="lg:col-span-5">
                                <?php if($whyChooseUsIntro !== ''): ?>
                                    <div class="why-enhance-intro rich-text-content text-sm"><?php echo \App\Support\RichText::render($whyChooseUsIntro); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="lg:col-span-7">
                                <div class="grid gap-4 sm:grid-cols-2">
                        <?php $__currentLoopData = $whyChooseUsBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $bannerId = \Illuminate\Support\Str::slug($item['title'] ?: ('why-choose-us-' . $loop->index));
                                $imagePath = $item['image'] ?? '';
                                $hasImage = $imagePath !== '';
                                $imageUrl = $hasImage
                                    ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . ltrim($imagePath, '/')))
                                    : null;
                            ?>
                            <article id="why-choose-us-<?php echo e($bannerId); ?>" class="why-enhance-card group relative">
                                <?php if($hasImage): ?>
                                    <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($item['title'] ?: (($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us') . ' Banner')); ?>" class="absolute inset-0 h-full w-full object-cover">
                                <?php else: ?>
                                    <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-600 to-secondary-500"></div>
                                    <div class="absolute -right-10 -top-8 h-36 w-36 rounded-full border border-white/10 bg-white/10 blur-sm"></div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/35 to-transparent"></div>
                                <div class="why-enhance-card-body">
                                    <h3 class="text-lg"><?php echo e($item['title'] ?: ($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us')); ?></h3>
                                    <?php if(!empty($item['description'])): ?>
                                        <div class="rich-text-content rich-text-content-inverse mt-1 text-sm font-semibold leading-relaxed text-white/95"><?php echo \App\Support\RichText::render($item['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div id="admissions" class="sr-only" aria-hidden="true"></div>

            <section id="academics" class="relative overflow-hidden border-t border-slate-200 bg-[#eef6ff] py-10">
                <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(45, 29, 92, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45, 29, 92, 0.08) 1px, transparent 1px); background-size: 34px 34px;"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.68),_transparent_38%)]"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.4),_transparent_34%)]"></div>
                <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
                        <div class="grid gap-4 lg:grid-cols-12 lg:items-stretch">
                            <div class="lg:col-span-6">
                                <p class="text-base font-bold uppercase tracking-[0.24em] text-blue-700">
                                    <?php echo e($academicsLabel !== '' ? $academicsLabel : 'Academic Excellence'); ?>

                                </p>
                                <div class="academics-heading rich-text-content rich-text-display mt-2 text-slate-900 font-[Georgia,serif]"><?php echo \App\Support\RichText::render($publicPage['academics_intro'] ?? 'A Structured Learning Culture With Mentorship At The Center.'); ?></div>
                                <div class="rich-text-content mt-3 text-base leading-relaxed text-slate-700"><?php echo \App\Support\RichText::render($academicsSupportText !== '' ? $academicsSupportText : 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.'); ?></div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <?php $__currentLoopData = $academicHighlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <article id="academics-<?php echo e(\Illuminate\Support\Str::slug($item['title'] ?? ('academic-highlight-'.$loop->index))); ?>" class="academics-primary-card rounded-2xl border p-3.5">
                                            <h3 class="text-xl font-bold"><?php echo e($item['title'] ?? ''); ?></h3>
                                            <div class="rich-text-content mt-1.5 text-base leading-relaxed"><?php echo \App\Support\RichText::render($item['description'] ?? ''); ?></div>
                                        </article>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div class="lg:col-span-6">
                                <div class="grid h-full grid-cols-2 gap-4">
                                    <?php for($i = 0; $i < 2; $i++): ?>
                                        <?php $visual = $academicsVisuals->get($i); ?>
                                        <div class="min-h-[240px] lg:min-h-[260px] overflow-hidden rounded-2xl border border-slate-200 bg-slate-900">
                                            <?php if($visual): ?>
                                                <img src="<?php echo e($visual); ?>" alt="Academic Excellence Visual <?php echo e($i + 1); ?>" class="h-full w-full object-cover">
                                            <?php else: ?>
                                                <div class="flex h-full items-center justify-center bg-gradient-to-b from-slate-800 to-slate-900 p-4 text-center text-sm font-semibold text-slate-200">
                                                    Upload academic image from admin banners.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div id="facilities" class="sr-only" aria-hidden="true"></div>

            <div id="about" class="sr-only" aria-hidden="true"></div>

            <section id="student-life" class="border-t border-slate-200 bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700"><?php echo e($studentLifeLabel !== '' ? $studentLifeLabel : 'Student Life'); ?></p>
                        <div class="rich-text-content rich-text-section-intro mt-3 text-slate-900"><?php echo \App\Support\RichText::render($publicPage['student_life_intro'] ?? ''); ?></div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <?php $__currentLoopData = $studentLifeItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div id="student-life-<?php echo e(\Illuminate\Support\Str::slug($item['title'] ?? ('student-life-'.$loop->index))); ?>" class="student-life-fx-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <h3 class="student-life-fx-title text-lg font-semibold text-slate-900"><?php echo e($item['title'] ?? ''); ?></h3>
                                <div class="student-life-fx-text rich-text-content mt-2 text-sm text-slate-600"><?php echo \App\Support\RichText::render($item['description'] ?? ''); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </section>

            <section id="parents" class="relative overflow-hidden border-y border-slate-200 bg-[#eef6ff] py-14 sm:py-16">
                <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(45, 29, 92, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45, 29, 92, 0.08) 1px, transparent 1px); background-size: 34px 34px;"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.68),_transparent_38%)]"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.4),_transparent_34%)]"></div>
                <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                    <div id="testimonials" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
                        <div class="mx-auto max-w-4xl text-center">
                            <p class="text-base font-bold uppercase tracking-[0.24em] text-blue-700">
                                <?php echo e($testimonialsBadgeText !== '' ? $testimonialsBadgeText : 'Testimonials'); ?>

                            </p>
                            <h2 class="mt-3 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl">
                                <?php echo e($testimonialsHeading !== '' ? $testimonialsHeading : 'What Parents and Student Say'); ?>

                            </h2>
                            <p class="mx-auto mt-4 max-w-3xl text-base leading-relaxed text-slate-600">
                                <?php echo e($testimonialsSubheading !== '' ? $testimonialsSubheading : 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.'); ?>

                            </p>
                        </div>

                        <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6">
                            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                                <h3 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">
                                    <?php echo e($testimonialsSliderTitle !== '' ? $testimonialsSliderTitle : 'Approved Testimonials'); ?>

                                </h3>
                                <?php if($testimonials->count() > 1): ?>
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-testimonial-prev class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-lg font-bold text-slate-700 transition hover:border-brand-300 hover:bg-slate-100" aria-label="Previous testimonial">&lt;</button>
                                        <button type="button" data-testimonial-next class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-lg font-bold text-slate-700 transition hover:border-brand-300 hover:bg-slate-100" aria-label="Next testimonial">&gt;</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if($testimonials->isEmpty()): ?>
                                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-10 text-center text-base font-medium text-slate-600">
                                    <?php echo e($testimonialsEmptyText !== '' ? $testimonialsEmptyText : 'No testimonials have been approved yet. Be the first to share your experience.'); ?>

                                </div>
                            <?php else: ?>
                                <div data-testimonial-slider class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                    <div data-testimonial-track class="flex transition-transform duration-500 ease-out">
                                        <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $tInitials = \Illuminate\Support\Str::upper(
                                                    collect(preg_split('/\s+/', trim($testimonial->full_name)))
                                                        ->filter()->take(2)
                                                        ->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))
                                                        ->implode('')
                                                ) ?: '?';
                                                $tStars = max(1, min(5, (int) $testimonial->rating));
                                            ?>
                                            <article class="w-full shrink-0 p-5 sm:p-7">
                                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm sm:p-6">

                                                    
                                                    <div class="mb-4 flex items-center gap-1">
                                                        <?php for($s = 1; $s <= 5; $s++): ?>
                                                            <svg class="h-4 w-4 <?php echo e($s <= $tStars ? 'text-amber-400' : 'text-slate-200'); ?>" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        <?php endfor; ?>
                                                        <span class="ml-1 text-xs text-slate-400"><?php echo e($tStars); ?>/5</span>
                                                    </div>

                                                    
                                                    <blockquote class="text-xl font-black leading-relaxed text-[#2D1D5C] sm:text-2xl">
                                                        "<?php echo e($testimonial->message); ?>"
                                                    </blockquote>

                                                    
                                                    <div class="mt-5 flex items-center gap-3">
                                                        <?php if($testimonial->student?->photo): ?>
                                                            <img src="<?php echo e(asset('storage/' . $testimonial->student->photo)); ?>"
                                                                 alt="<?php echo e($testimonial->full_name); ?>"
                                                                 class="h-11 w-11 shrink-0 rounded-full border-2 border-white object-cover shadow-sm">
                                                        <?php else: ?>
                                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-extrabold text-white shadow-sm"
                                                                 style="background:<?php echo e($submenuPrimaryColor ?? ($theme['primary']['500'] ?? '#2D1D5C')); ?>;">
                                                                <?php echo e($tInitials); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <p class="text-sm font-bold text-slate-800"><?php echo e($testimonial->full_name); ?></p>
                                                            <?php if(!empty($testimonial->role_title)): ?>
                                                            <p class="text-xs text-slate-400"><?php echo e($testimonial->role_title); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <?php if($testimonials->count() > 1): ?>
                                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2" data-testimonial-dots>
                                        <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button
                                                type="button"
                                                data-testimonial-dot
                                                data-index="<?php echo e($loop->index); ?>"
                                                class="h-2.5 w-8 rounded-full <?php echo e($loop->first ? 'bg-brand-700' : 'bg-slate-300'); ?> transition duration-200"
                                                aria-label="Go to testimonial <?php echo e($loop->iteration); ?>"
                                            ></button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="border-t border-slate-200 bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700"><?php echo e($contactLabel !== '' ? $contactLabel : 'Contact'); ?></p>
                        <div class="rich-text-content rich-text-section-intro mt-3 text-slate-900"><?php echo \App\Support\RichText::render($publicPage['contact_intro'] ?? ''); ?></div>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php $__currentLoopData = $contactItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div id="contact-<?php echo e(\Illuminate\Support\Str::slug($item['title'] ?? ('contact-'.$loop->index))); ?>" class="contact-primary-card rounded-xl border p-4 transition duration-300 hover:-translate-y-0.5 hover:shadow-sm">
                                    <h3 class="contact-primary-title text-sm font-bold uppercase tracking-wide"><?php echo e($item['title'] ?? ''); ?></h3>
                                    <div class="contact-primary-text rich-text-content mt-2 text-sm"><?php echo \App\Support\RichText::render($item['description'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="contact-primary-card rounded-2xl border p-5 shadow-sm">
                            <h3 class="contact-primary-title font-semibold"><?php echo e($quickContactLabel !== '' ? $quickContactLabel : 'Quick Contact'); ?></h3>
                            <div class="contact-primary-text mt-3 space-y-2 text-sm">
                                <p><span class="font-semibold"><?php echo e($contactPhoneLabel !== '' ? $contactPhoneLabel : 'Phone'); ?>:</span> <?php echo e($school?->phone ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                                <p><span class="font-semibold"><?php echo e($contactWhatsappLabel !== '' ? $contactWhatsappLabel : 'WhatsApp'); ?>:</span> <?php echo e(($publicPage['whatsapp'] ?? '') ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                                <p><span class="font-semibold"><?php echo e($contactEmailLabel !== '' ? $contactEmailLabel : 'Email'); ?>:</span> <?php echo e($school?->email ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                                <p><span class="font-semibold"><?php echo e($contactAddressLabel !== '' ? $contactAddressLabel : 'Address'); ?>:</span> <?php echo e($school?->address ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-3">
                                <?php if(!empty($publicPage['visit_booking_url'])): ?>
                                    <a href="<?php echo e($publicPage['visit_booking_url']); ?>" target="_blank" rel="noopener" class="theme-cta-solid rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5"><?php echo e($visitBookingButtonText !== '' ? $visitBookingButtonText : 'Visit Booking'); ?></a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admission.apply')); ?>" class="theme-cta-outline rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5"><?php echo e($quickApplyButtonText !== '' ? $quickApplyButtonText : 'Apply Now'); ?></a>
                            </div>
                            <?php if(!empty($publicPage['map_embed_url'])): ?>
                                <div class="mt-5 overflow-hidden rounded-xl border border-slate-200">
                                    <iframe src="<?php echo e($publicPage['map_embed_url']); ?>" title="<?php echo e($mapEmbedTitleText !== '' ? $mapEmbedTitleText : 'School map'); ?>" class="h-60 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <?php echo $__env->make('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <script>
        (function () {
            const menus = Array.from(document.querySelectorAll('[data-menu]'));
            const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
            const mobileMenuOpenIcon = document.querySelector('[data-mobile-menu-open-icon]');
            const mobileMenuCloseIcon = document.querySelector('[data-mobile-menu-close-icon]');
            const mobileMenu = document.querySelector('[data-mobile-menu]');
            const mobileMenuBackdrop = document.querySelector('[data-mobile-menu-backdrop]');
            const mobileMenuCloseButton = document.querySelector('[data-mobile-menu-close]');
            const mobileSubmenuToggles = Array.from(document.querySelectorAll('[data-mobile-submenu-toggle]'));
            let desktopCloseTimeout = null;

            const isDesktopMenu = () => window.innerWidth >= 1280;

            const closeAll = (exceptId = null) => {
                menus.forEach((menu) => {
                    const menuId = menu.getAttribute('data-menu');
                    const toggle = menu.querySelector('[data-menu-toggle]');
                    const panel = menu.querySelector('[data-menu-panel]');

                    if (!toggle || !panel) {
                        return;
                    }

                    const shouldOpen = exceptId !== null && menuId === exceptId;
                    panel.classList.toggle('hidden', !shouldOpen);
                    toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                    toggle.classList.toggle('bg-slate-100', shouldOpen);
                    toggle.classList.toggle('text-brand-700', shouldOpen);
                });
            };

            const queueDesktopClose = (delay = 90) => {
                clearTimeout(desktopCloseTimeout);
                desktopCloseTimeout = window.setTimeout(() => {
                    closeAll();
                }, delay);
            };

            if (menus.length) {
                menus.forEach((menu) => {
                    const menuId = menu.getAttribute('data-menu');
                    const toggle = menu.querySelector('[data-menu-toggle]');
                    const panel = menu.querySelector('[data-menu-panel]');

                    if (!toggle || !panel) {
                        return;
                    }

                    toggle.addEventListener('click', function (event) {
                        event.preventDefault();
                        const expanded = toggle.getAttribute('aria-expanded') === 'true';
                        closeAll(expanded ? null : menuId);
                    });

                    menu.addEventListener('mouseenter', function () {
                        if (!isDesktopMenu()) {
                            return;
                        }
                        clearTimeout(desktopCloseTimeout);
                        closeAll(menuId);
                    });

                    menu.addEventListener('mouseleave', function () {
                        if (!isDesktopMenu()) {
                            return;
                        }
                        queueDesktopClose();
                    });

                    menu.addEventListener('focusin', function () {
                        if (!isDesktopMenu()) {
                            return;
                        }
                        clearTimeout(desktopCloseTimeout);
                        closeAll(menuId);
                    });

                    menu.addEventListener('focusout', function (event) {
                        if (!isDesktopMenu()) {
                            return;
                        }
                        const nextElement = event.relatedTarget;
                        if (!nextElement || !menu.contains(nextElement)) {
                            queueDesktopClose(70);
                        }
                    });

                    panel.querySelectorAll('a').forEach((link) => {
                        link.addEventListener('click', function () {
                            closeAll();
                        });
                    });
                });
            }

            const closeMobileSubmenus = (exceptTarget = null) => {
                mobileSubmenuToggles.forEach((toggle) => {
                    const targetId = toggle.getAttribute('data-target');
                    const panel = targetId ? document.getElementById(targetId) : null;
                    const indicator = toggle.querySelector('[data-mobile-submenu-indicator]');
                    const open = exceptTarget !== null && targetId === exceptTarget;

                    if (!panel) {
                        return;
                    }

                    panel.classList.toggle('hidden', !open);
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    toggle.classList.toggle('text-brand-700', open);
                    if (indicator) {
                        indicator.textContent = open ? '-' : '+';
                        indicator.classList.toggle('text-brand-700', open);
                    }
                });
            };

            const setMobileMenuState = (open) => {
                if (!mobileMenu || !mobileMenuToggle || !mobileMenuBackdrop) {
                    return;
                }

                mobileMenu.classList.toggle('translate-x-full', !open);
                mobileMenu.classList.toggle('pointer-events-none', !open);
                mobileMenuBackdrop.classList.toggle('opacity-0', !open);
                mobileMenuBackdrop.classList.toggle('pointer-events-none', !open);
                mobileMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.classList.toggle('overflow-hidden', open);

                if (mobileMenuOpenIcon && mobileMenuCloseIcon) {
                    mobileMenuOpenIcon.classList.toggle('hidden', open);
                    mobileMenuCloseIcon.classList.toggle('hidden', !open);
                }

                if (!open) {
                    closeMobileSubmenus();
                }
            };

            const closeMobileMenu = () => setMobileMenuState(false);

            if (mobileMenu && mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function () {
                    const expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                    setMobileMenuState(!expanded);
                });
            }

            if (mobileMenuCloseButton) {
                mobileMenuCloseButton.addEventListener('click', function () {
                    closeMobileMenu();
                });
            }

            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.addEventListener('click', function () {
                    closeMobileMenu();
                });
            }

            mobileSubmenuToggles.forEach((toggle) => {
                toggle.addEventListener('click', function () {
                    const targetId = toggle.getAttribute('data-target');
                    if (!targetId) {
                        return;
                    }
                    const expanded = toggle.getAttribute('aria-expanded') === 'true';
                    closeMobileSubmenus(expanded ? null : targetId);
                });
            });

            if (mobileMenu) {
                mobileMenu.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', function () {
                        closeMobileMenu();
                    });
                });
            }

            document.addEventListener('click', function (event) {
                if (!event.target.closest('[data-menu]')) {
                    closeAll();
                }
                if (mobileMenu && !event.target.closest('[data-mobile-menu]') && !event.target.closest('[data-mobile-menu-toggle]') && !event.target.closest('[data-mobile-menu-backdrop]')) {
                    closeMobileMenu();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAll();
                    closeMobileMenu();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1280) {
                    closeMobileMenu();
                }
                if (!isDesktopMenu()) {
                    closeAll();
                }
            });
        })();
    </script>

    <?php if($testimonials->count() > 1): ?>
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
    <?php endif; ?>
</body>
</html>










<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/welcome.blade.php ENDPATH**/ ?>