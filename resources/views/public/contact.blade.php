<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trim((string) ($publicPage['contact_page_browser_title'] ?? 'Contact Us')) }} | {{ $schoolName }}</title>
    @php
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    @endphp
    @if($faviconPath)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ltrim($faviconPath, '/')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '{{ $theme['primary']['50'] }}',
                            100: '{{ $theme['primary']['100'] }}',
                            200: '{{ $theme['primary']['200'] }}',
                            300: '{{ $theme['primary']['300'] }}',
                            400: '{{ $theme['primary']['400'] }}',
                            500: '{{ $theme['primary']['500'] }}',
                            600: '{{ $theme['primary']['600'] }}',
                            700: '{{ $theme['primary']['700'] }}'
                        },
                        secondary: {
                            50: '{{ $theme['secondary']['50'] }}',
                            100: '{{ $theme['secondary']['100'] }}',
                            200: '{{ $theme['secondary']['200'] }}',
                            300: '{{ $theme['secondary']['300'] }}',
                            400: '{{ $theme['secondary']['400'] }}',
                            500: '{{ $theme['secondary']['500'] }}',
                            600: '{{ $theme['secondary']['600'] }}',
                            700: '{{ $theme['secondary']['700'] }}'
                        },
                        accent: {
                            300: '{{ $theme['accent']['300'] }}',
                            400: '{{ $theme['accent']['400'] }}',
                            500: '{{ $theme['accent']['500'] }}'
                        },
                        ink: '{{ $theme['ink'] }}',
                        muted: '{{ $theme['muted'] }}'
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

    .rich-text-content p + p,
    .rich-text-content ul + p,
    .rich-text-content ol + p,
    .rich-text-content p + ul,
    .rich-text-content p + ol,
    .rich-text-content figure,
    .rich-text-content blockquote {
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
        padding-left: 0.9rem;
        font-style: italic;
    }
    .rich-text-content a {
        color: var(--submenu-primary, #2D1D5C);
        font-weight: 700;
        text-decoration: underline;
    }
    .rich-text-content img {
        display: block;
        max-width: 100%;
        border-radius: 1rem;
        box-shadow: 0 18px 38px -28px rgba(15, 23, 42, 0.45);
    }
    .rich-text-content figcaption {
        margin-top: 0.6rem;
        color: #64748b;
        font-size: 0.875rem;
    }
</style>
</head>
@php
    $sectionKey = 'contact';

    $menuSections = collect($menuCatalog ?? [])
        ->map(function ($section, $id) {
            $items = collect($section['items'] ?? [])
                ->map(function ($menuItem) {
                    return [
                        'title' => $menuItem['title'] ?? '',
                        'slug' => $menuItem['slug'] ?? '',
                    ];
                })
                ->filter(fn ($menuItem) => !empty($menuItem['title']) && !empty($menuItem['slug']))
                ->values()
                ->all();

            $isContact   = $id === 'contact';
            $firstSlug   = collect($items)->first()['slug'] ?? null;
            $sectionLink = $isContact
                ? route('public.contact')
                : ($firstSlug ? route('public.submenu', ['section' => $id, 'slug' => $firstSlug]) : route('public.home'));

            return [
                'id'    => $id,
                'label' => $section['label'] ?? ucfirst(str_replace('-', ' ', $id)),
                'link'  => $sectionLink,
                'items' => $isContact ? [] : $items,
            ];
        })
        ->prepend(['id' => 'home', 'label' => 'Home', 'link' => route('public.home'), 'items' => []])
        ->values()
        ->all();
    $siteBackgroundColor = $theme['site_background'];
    $headerBgColor = $theme['header'];
    $submenuPrimaryColor = $theme['primary']['500'];
    $submenuSecondaryColor = $theme['secondary']['500'];
    $submenuHoverTextColor = $theme['primary_text_on_secondary'];
    $themeHeadingColor = $theme['ink'];
    $themeBodyColor = $theme['muted'];
    $themeSurfaceColor = $theme['surface'];
    $themeSoftSurfaceColor = $theme['soft_surface'];
    $headerApplyText = trim((string) ($publicPage['header_apply_text'] ?? 'Apply'));
    $headerPortalLoginText = trim((string) ($publicPage['header_portal_login_text'] ?? 'Portal Login'));
    $mobileApplyText = trim((string) ($publicPage['mobile_apply_text'] ?? 'Apply Now'));
    $mobilePortalLoginText = trim((string) ($publicPage['mobile_portal_login_text'] ?? 'Portal Login'));
    $mobileMenuTitle = trim((string) ($publicPage['mobile_menu_title'] ?? 'Menu'));
    $menuOverviewSuffix = trim((string) ($publicPage['menu_overview_suffix'] ?? 'Overview'));
    $contactPageBadgeText = trim((string) ($publicPage['contact_page_badge_text'] ?? 'Contact Us'));
    $contactPageHeading = trim((string) ($publicPage['contact_page_heading'] ?? 'We are here to help you'));
    $contactPageSubheading = trim((string) ($publicPage['contact_page_subheading'] ?? 'Send us a message and our admissions or support team will respond as soon as possible.'));
    $contactFormTitle = trim((string) ($publicPage['contact_form_title'] ?? 'Contact Us Form'));
    $contactFormFullNameLabel = trim((string) ($publicPage['contact_form_full_name_label'] ?? 'Full Name'));
    $contactFormFullNamePlaceholder = trim((string) ($publicPage['contact_form_full_name_placeholder'] ?? 'Enter your full name'));
    $contactFormEmailLabel = trim((string) ($publicPage['contact_form_email_label'] ?? 'Email'));
    $contactFormEmailPlaceholder = trim((string) ($publicPage['contact_form_email_placeholder'] ?? 'you@example.com'));
    $contactFormPhoneLabel = trim((string) ($publicPage['contact_form_phone_label'] ?? 'Phone Number'));
    $contactFormPhonePlaceholder = trim((string) ($publicPage['contact_form_phone_placeholder'] ?? '+234...'));
    $contactFormSubjectLabel = trim((string) ($publicPage['contact_form_subject_label'] ?? 'Subject'));
    $contactFormSubjectPlaceholder = trim((string) ($publicPage['contact_form_subject_placeholder'] ?? 'How can we help?'));
    $contactFormMessageLabel = trim((string) ($publicPage['contact_form_message_label'] ?? 'Message'));
    $contactFormMessagePlaceholder = trim((string) ($publicPage['contact_form_message_placeholder'] ?? 'Write your message...'));
    $contactFormSubmitText = trim((string) ($publicPage['contact_form_submit_text'] ?? 'Send Message'));
    $contactInfoTitle = trim((string) ($publicPage['contact_info_title'] ?? 'Contact Information'));
    $contactNotProvidedText = trim((string) ($publicPage['contact_not_provided_text'] ?? 'Not provided yet'));
    $contactMoreDetailsTitle = trim((string) ($publicPage['contact_more_details_title'] ?? 'More Contact Details'));
    $contactAddressLabel = trim((string) ($publicPage['contact_address_label'] ?? 'Address'));
    $contactPhoneLabel = trim((string) ($publicPage['contact_phone_label'] ?? 'Phone number'));
    $contactWhatsappLabel = trim((string) ($publicPage['contact_whatsapp_label'] ?? 'WhatsApp'));
    $contactEmailLabel = trim((string) ($publicPage['contact_email_label'] ?? 'Email'));
@endphp
<body class="text-ink antialiased" style="background-color: {{ $siteBackgroundColor }}; color: {{ $themeBodyColor }}; --submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }}; --theme-heading: {{ $themeHeadingColor }}; --theme-body: {{ $themeBodyColor }}; --theme-surface: {{ $themeSurfaceColor }}; --theme-soft-surface: {{ $themeSoftSurfaceColor }};">
    @include('public.partials.page-loader', ['school' => $school, 'primary' => $submenuPrimaryColor])
    <div class="relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>

        <header class="sticky top-0 z-50 backdrop-blur" style="background-color: {{ $headerBgColor }};">
            <div class="mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-4 px-6 py-3 lg:px-8">
                <a href="{{ route('public.home') }}" class="flex shrink-0 items-center transition duration-200 hover:opacity-90">
                    @if($school?->logo)
                        <img src="{{ asset('storage/' . ltrim($school->logo, '/')) }}" alt="{{ $schoolName }} Logo" style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;object-fit:cover;border:1px solid rgba(255,255,255,0.2);background:#fff;">
                    @else
                        <div style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;letter-spacing:0.1em;color:#fff;">
                            {{ \Illuminate\Support\Str::upper(collect(preg_split('/\s+/', trim($schoolName)))->filter()->take(2)->map(fn($w) => \Illuminate\Support\Str::substr($w,0,1))->implode('')) }}
                        </div>
                    @endif
                </a>
                <nav class="hidden items-center justify-center gap-1 rounded-2xl bg-white/95 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex" style="--submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                    @foreach($menuSections as $section)
                        @php
                            $alignClass = ($loop->last || $loop->iteration >= count($menuSections) - 1) ? 'right-0' : 'left-0';
                            $isCurrentSection = $section['id'] === $sectionKey;
                        @endphp
                        <div class="relative shrink-0" data-menu="{{ $section['id'] }}">
                            @if(!empty($section['items']))
                                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="submenu-{{ $section['id'] }}" class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 {{ $isCurrentSection ? 'theme-nav-link-active' : '' }}">
                                    {{ $section['label'] }}
                                </button>
                                <div id="submenu-{{ $section['id'] }}" data-menu-panel class="absolute {{ $alignClass }} top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                                    <div
                                        class="theme-submenu-panel rounded-2xl p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur"
                                        style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};"
                                    >
                                        <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]">{{ $section['label'] }}</p>
                                        <a href="{{ $section['link'] }}" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                            {{ $section['label'] }} {{ $menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview' }}
                                        </a>
                                        @foreach($section['items'] as $menuItem)
                                            <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']]) }}" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200">
                                                {{ $menuItem['title'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $section['link'] }}" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 {{ $isCurrentSection ? 'theme-nav-link-active' : '' }}">
                                    {{ $section['label'] }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </nav>
                <div class="flex items-center justify-end gap-2 sm:gap-3" style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                    <a href="{{ route('admission.apply') }}" class="theme-header-action-outline hidden items-center whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex">{{ $headerApplyText !== '' ? $headerApplyText : 'Apply' }}</a>
                    <a href="{{ route('portal.login') }}" class="theme-header-action-solid inline-flex items-center whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">{{ $headerPortalLoginText !== '' ? $headerPortalLoginText : 'Portal Login' }}</a>
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
                <div class="sticky top-0 flex items-center justify-between bg-white/95 px-5 py-4 backdrop-blur">
                    <p class="font-display text-lg font-semibold text-slate-900">{{ $mobileMenuTitle !== '' ? $mobileMenuTitle : 'Menu' }}</p>
                    <button type="button" data-mobile-menu-close class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition duration-200 hover:bg-slate-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 py-5">
                    <div class="mb-4 flex flex-col gap-2" style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                        <a href="{{ route('admission.apply') }}" class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200">{{ $mobileApplyText !== '' ? $mobileApplyText : 'Apply Now' }}</a>
                        <a href="{{ route('portal.login') }}" class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200">{{ $mobilePortalLoginText !== '' ? $mobilePortalLoginText : 'Portal Login' }}</a>
                    </div>

                    <div class="space-y-2" style="--submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                        @foreach($menuSections as $section)
                            <div class="rounded-xl bg-white shadow-sm">
                                @if(!empty($section['items']))
                                    <button type="button" data-mobile-submenu-toggle data-target="mobile-submenu-{{ $section['id'] }}" aria-expanded="false" class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200 {{ $section['id'] === $sectionKey ? 'theme-nav-link-active' : '' }}">
                                        <span>{{ $section['label'] }}</span>
                                        <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                                    </button>
                                    <div id="mobile-submenu-{{ $section['id'] }}" data-mobile-submenu-panel class="hidden px-4 pb-4" style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                                        <div class="space-y-1 border-l border-slate-200 pl-4">
                                            <a href="{{ $section['link'] }}" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">{{ $section['label'] }} {{ $menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview' }}</a>
                                            @foreach($section['items'] as $menuItem)
                                                <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']]) }}" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200">{{ $menuItem['title'] }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $section['link'] }}" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200 {{ $section['id'] === $sectionKey ? 'theme-nav-link-active' : '' }}">{{ $section['label'] }}</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </header>

        <main class="relative z-0 bg-pattern-grid">
            @php
                $contactHeroImage = trim((string) ($publicPage['contact_hero_image'] ?? ''));
            @endphp
            {{-- ── Full-width hero ────────────────────────────────── --}}
            <div class="relative w-full overflow-hidden" style="min-height:440px;display:flex;flex-direction:column;justify-content:flex-end;">
                @if($contactHeroImage !== '')
                    <img src="{{ asset('storage/' . ltrim($contactHeroImage, '/')) }}"
                        alt="{{ $contactPageHeading !== '' ? $contactPageHeading : 'Contact Us' }}"
                        style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(0,0,0,0.12) 0%, rgba(0,0,0,0.65) 100%);"></div>
                @else
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg, {{ $submenuPrimaryColor }} 0%, rgba(45,29,92,0.85) 55%, {{ $submenuSecondaryColor }}44 100%);"></div>
                    <div class="pointer-events-none" style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:38px 38px;"></div>
                    <div class="pointer-events-none" style="position:absolute;inset:0;background:radial-gradient(circle at 80% 20%, {{ $submenuSecondaryColor }}22 0%, transparent 50%);"></div>
                @endif
                <div class="relative px-6 pb-16 pt-28 lg:px-8 lg:pb-20 lg:pt-36" style="max-width:80rem;margin:0 auto;width:100%;">
                    <span class="inline-flex rounded-full px-4 py-1 text-xs font-bold uppercase tracking-[0.2em]"
                        style="background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.28);color:#ffffff;backdrop-filter:blur(4px);">
                        {{ $contactPageBadgeText !== '' ? $contactPageBadgeText : 'Contact Us' }}
                    </span>
                    <h1 class="mt-5 font-display font-bold leading-tight text-white"
                        style="font-size:clamp(2rem,5vw,3.5rem);text-shadow:0 2px 20px rgba(0,0,0,0.4);max-width:44rem;">
                        {{ $contactPageHeading !== '' ? $contactPageHeading : 'We are here to help you' }}
                    </h1>
                    @if($contactPageSubheading !== '')
                        <div class="rich-text-content mt-4 max-w-2xl text-base font-medium leading-relaxed"
                            style="color:rgba(255,255,255,0.82);text-shadow:0 1px 8px rgba(0,0,0,0.3);">
                            {!! \App\Support\RichText::render($contactPageSubheading) !!}
                        </div>
                    @endif
                </div>
            </div>

            <section class="mx-auto max-w-7xl px-6 pb-14 pt-10 lg:px-8">
                @if(session('contact_success'))
                    <div class="mb-6 rounded-xl border border-secondary-200 bg-secondary-50 px-4 py-3 text-sm font-semibold text-secondary-700">
                        {{ session('contact_success') }}
                    </div>
                @endif
                @if($errors->has('contact_form'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        {{ $errors->first('contact_form') }}
                    </div>
                @endif

                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-soft">
                        <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $contactFormTitle !== '' ? $contactFormTitle : 'Contact Us Form' }}</h2>
                        <form action="{{ route('public.contact.submit') }}" method="POST" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label for="full_name" class="mb-1 block text-sm font-semibold text-slate-700">{{ $contactFormFullNameLabel !== '' ? $contactFormFullNameLabel : 'Full Name' }}</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="{{ $contactFormFullNamePlaceholder !== '' ? $contactFormFullNamePlaceholder : 'Enter your full name' }}" required>
                                @error('full_name')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">{{ $contactFormEmailLabel !== '' ? $contactFormEmailLabel : 'Email' }}</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="{{ $contactFormEmailPlaceholder !== '' ? $contactFormEmailPlaceholder : 'you@example.com' }}" required>
                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone_number" class="mb-1 block text-sm font-semibold text-slate-700">{{ $contactFormPhoneLabel !== '' ? $contactFormPhoneLabel : 'Phone Number' }}</label>
                                <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="{{ $contactFormPhonePlaceholder !== '' ? $contactFormPhonePlaceholder : '+234...' }}" >
                                @error('phone_number')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject" class="mb-1 block text-sm font-semibold text-slate-700">{{ $contactFormSubjectLabel !== '' ? $contactFormSubjectLabel : 'Subject' }}</label>
                                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="{{ $contactFormSubjectPlaceholder !== '' ? $contactFormSubjectPlaceholder : 'How can we help?' }}" required>
                                @error('subject')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="mb-1 block text-sm font-semibold text-slate-700">{{ $contactFormMessageLabel !== '' ? $contactFormMessageLabel : 'Message' }}</label>
                                <textarea id="message" name="message" rows="5" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="{{ $contactFormMessagePlaceholder !== '' ? $contactFormMessagePlaceholder : 'Write your message...' }}" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="theme-cta-solid inline-flex rounded-full px-6 py-3 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">
                                {{ $contactFormSubmitText !== '' ? $contactFormSubmitText : 'Send Message' }}
                            </button>
                        </form>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-soft">
                        <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $contactInfoTitle !== '' ? $contactInfoTitle : 'Contact Information' }}</h2>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            <p><span class="font-semibold">{{ $contactAddressLabel !== '' ? $contactAddressLabel : 'Address' }}:</span> {{ $school?->address ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                            <p><span class="font-semibold">{{ $contactPhoneLabel !== '' ? $contactPhoneLabel : 'Phone number' }}:</span> {{ $school?->phone ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                            <p><span class="font-semibold">{{ $contactWhatsappLabel !== '' ? $contactWhatsappLabel : 'WhatsApp' }}:</span> {{ ($publicPage['whatsapp'] ?? '') ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                            <p><span class="font-semibold">{{ $contactEmailLabel !== '' ? $contactEmailLabel : 'Email' }}:</span> {{ $school?->email ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                        </div>

                        @if(!empty($contactItems))
                            <div class="mt-7 rounded-xl bg-slate-50 p-4">
                                <h3 class="text-sm font-bold uppercase tracking-wide text-brand-700">{{ $contactMoreDetailsTitle !== '' ? $contactMoreDetailsTitle : 'More Contact Details' }}</h3>
                                <div class="mt-3 space-y-2">
                                    @foreach($contactItems as $contactItem)
                                        <div class="rounded-lg bg-white px-3 py-2">
                                            <p class="text-sm font-semibold text-slate-900">{{ $contactItem['title'] }}</p>
                                            <div class="rich-text-content mt-1 text-sm text-slate-600">{!! \App\Support\RichText::render($contactItem['description']) !!}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </section>
        </main>
        @include('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage])
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
</body>
</html>




