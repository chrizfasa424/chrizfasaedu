<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(trim((string) ($publicPage['contact_page_browser_title'] ?? 'Contact Us'))); ?> | <?php echo e($schoolName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php($theme = \App\Support\ThemePalette::fromPublicPage($publicPage))
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
    .theme-nav-link {
        color: #475569;
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible,
    .theme-nav-link-active {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
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
        background-color: var(--submenu-primary);
        border-color: var(--submenu-secondary);
    }

    .theme-submenu-heading {
        color: rgba(255, 255, 255, 0.72);
    }

    .theme-submenu-link {
        color: #ffffff;
    }

    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible,
    .theme-submenu-link-active {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-mobile-submenu-link {
        color: var(--submenu-primary);
    }

    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible,
    .theme-mobile-submenu-link-active {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
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

            $isContact = $id === 'contact';

            return [
                'id' => $id,
                'label' => $section['label'] ?? ucfirst(str_replace('-', ' ', $id)),
                'link' => $isContact ? route('public.contact') : route('public.home') . "#{$id}",
                'items' => $isContact ? [] : $items,
            ];
        })
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
?>
<body class="text-ink antialiased" style="background-color: <?php echo e($siteBackgroundColor); ?>; color: <?php echo e($themeBodyColor); ?>; --submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>; --theme-heading: <?php echo e($themeHeadingColor); ?>; --theme-body: <?php echo e($themeBodyColor); ?>; --theme-surface: <?php echo e($themeSurfaceColor); ?>; --theme-soft-surface: <?php echo e($themeSoftSurfaceColor); ?>;">
    <div class="relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>

        <header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur" style="background-color: <?php echo e($headerBgColor); ?>;">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <a href="<?php echo e(route('public.home')); ?>" class="flex items-center gap-3 transition duration-200 hover:opacity-90">
                    <?php if($school?->logo): ?>
                        <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="<?php echo e($schoolName); ?> Logo" class="h-10 w-10 rounded-full border border-slate-200 bg-white object-cover">
                    <?php endif; ?>
                    <span class="font-display text-xl font-semibold tracking-tight text-white whitespace-nowrap"><?php echo e($schoolName); ?></span>
                </a>

                <nav class="hidden items-center gap-1.5 rounded-full border border-slate-200/90 bg-white/90 p-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex" style="--submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                    <?php $__currentLoopData = $menuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $alignClass = ($loop->last || $loop->iteration >= count($menuSections) - 1) ? 'right-0' : 'left-0';
                            $isCurrentSection = $section['id'] === $sectionKey;
                        ?>
                        <div class="relative" data-menu="<?php echo e($section['id']); ?>">
                            <?php if(!empty($section['items'])): ?>
                                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="submenu-<?php echo e($section['id']); ?>" class="theme-nav-link inline-flex cursor-pointer items-center rounded-full px-4 py-2.5 transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 <?php echo e($isCurrentSection ? 'theme-nav-link-active' : ''); ?>">
                                    <?php echo e($section['label']); ?>

                                </button>
                                <div id="submenu-<?php echo e($section['id']); ?>" data-menu-panel class="absolute <?php echo e($alignClass); ?> top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                                    <div
                                        class="theme-submenu-panel rounded-2xl border p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur"
                                        style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;"
                                    >
                                        <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]"><?php echo e($section['label']); ?></p>
                                        <a href="<?php echo e($section['link']); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                            <?php echo e($section['label']); ?> <?php echo e($menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview'); ?>

                                        </a>
                                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']])); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200">
                                                <?php echo e($menuItem['title']); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e($section['link']); ?>" class="theme-nav-link inline-flex items-center rounded-full px-4 py-2.5 transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 <?php echo e($isCurrentSection ? 'theme-nav-link-active' : ''); ?>">
                                    <?php echo e($section['label']); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>

                <div class="flex items-center gap-2 sm:gap-3" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                    <a href="<?php echo e(route('admission.apply')); ?>" class="theme-header-action-outline hidden rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex"><?php echo e($headerApplyText !== '' ? $headerApplyText : 'Apply'); ?></a>
                    <a href="<?php echo e(route('login')); ?>" class="theme-header-action-solid rounded-full px-3 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:px-4"><?php echo e($headerPortalLoginText !== '' ? $headerPortalLoginText : 'Portal Login'); ?></a>
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
                    <div class="mb-4 flex flex-col gap-2" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                        <a href="<?php echo e(route('admission.apply')); ?>" class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobileApplyText !== '' ? $mobileApplyText : 'Apply Now'); ?></a>
                        <a href="<?php echo e(route('login')); ?>" class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobilePortalLoginText !== '' ? $mobilePortalLoginText : 'Portal Login'); ?></a>
                    </div>

                    <div class="space-y-2" style="--submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                        <?php $__currentLoopData = $menuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                                <?php if(!empty($section['items'])): ?>
                                    <button type="button" data-mobile-submenu-toggle data-target="mobile-submenu-<?php echo e($section['id']); ?>" aria-expanded="false" class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200 <?php echo e($section['id'] === $sectionKey ? 'theme-nav-link-active' : ''); ?>">
                                        <span><?php echo e($section['label']); ?></span>
                                        <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                                    </button>
                                    <div id="mobile-submenu-<?php echo e($section['id']); ?>" data-mobile-submenu-panel class="hidden px-4 pb-4" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                                        <div class="space-y-1 border-l border-slate-200 pl-4">
                                            <a href="<?php echo e($section['link']); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200"><?php echo e($section['label']); ?> <?php echo e($menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview'); ?></a>
                                            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']])); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200"><?php echo e($menuItem['title']); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo e($section['link']); ?>" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200 <?php echo e($section['id'] === $sectionKey ? 'theme-nav-link-active' : ''); ?>"><?php echo e($section['label']); ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </aside>
        </header>

        <main class="relative z-0">
            <section class="mx-auto max-w-7xl px-6 pb-14 pt-16 lg:px-8 lg:pt-20">
                <div class="mb-8 max-w-3xl">
                    <p class="inline-flex rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-brand-700"><?php echo e($contactPageBadgeText !== '' ? $contactPageBadgeText : 'Contact Us'); ?></p>
                    <h1 class="mt-4 font-display text-3xl font-semibold leading-tight text-slate-900 sm:text-4xl"><?php echo e($contactPageHeading !== '' ? $contactPageHeading : 'We are here to help you'); ?></h1>
                    <p class="mt-4 text-base leading-relaxed text-muted sm:text-lg"><?php echo e($contactPageSubheading !== '' ? $contactPageSubheading : 'Send us a message and our admissions or support team will respond as soon as possible.'); ?></p>
                </div>

                <?php if(session('contact_success')): ?>
                    <div class="mb-6 rounded-xl border border-secondary-200 bg-secondary-50 px-4 py-3 text-sm font-semibold text-secondary-700">
                        <?php echo e(session('contact_success')); ?>

                    </div>
                <?php endif; ?>
                <?php if($errors->has('contact_form')): ?>
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        <?php echo e($errors->first('contact_form')); ?>

                    </div>
                <?php endif; ?>

                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
                        <h2 class="font-display text-2xl font-semibold text-slate-900"><?php echo e($contactFormTitle !== '' ? $contactFormTitle : 'Contact Us Form'); ?></h2>
                        <form action="<?php echo e(route('public.contact.submit')); ?>" method="POST" class="mt-5 space-y-4">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label for="full_name" class="mb-1 block text-sm font-semibold text-slate-700"><?php echo e($contactFormFullNameLabel !== '' ? $contactFormFullNameLabel : 'Full Name'); ?></label>
                                <input id="full_name" name="full_name" type="text" value="<?php echo e(old('full_name')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="<?php echo e($contactFormFullNamePlaceholder !== '' ? $contactFormFullNamePlaceholder : 'Enter your full name'); ?>" required>
                                <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-sm font-semibold text-slate-700"><?php echo e($contactFormEmailLabel !== '' ? $contactFormEmailLabel : 'Email'); ?></label>
                                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="<?php echo e($contactFormEmailPlaceholder !== '' ? $contactFormEmailPlaceholder : 'you@example.com'); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="phone_number" class="mb-1 block text-sm font-semibold text-slate-700"><?php echo e($contactFormPhoneLabel !== '' ? $contactFormPhoneLabel : 'Phone Number'); ?></label>
                                <input id="phone_number" name="phone_number" type="text" value="<?php echo e(old('phone_number')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="<?php echo e($contactFormPhonePlaceholder !== '' ? $contactFormPhonePlaceholder : '+234...'); ?>" >
                                <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="subject" class="mb-1 block text-sm font-semibold text-slate-700"><?php echo e($contactFormSubjectLabel !== '' ? $contactFormSubjectLabel : 'Subject'); ?></label>
                                <input id="subject" name="subject" type="text" value="<?php echo e(old('subject')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="<?php echo e($contactFormSubjectPlaceholder !== '' ? $contactFormSubjectPlaceholder : 'How can we help?'); ?>" required>
                                <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="message" class="mb-1 block text-sm font-semibold text-slate-700"><?php echo e($contactFormMessageLabel !== '' ? $contactFormMessageLabel : 'Message'); ?></label>
                                <textarea id="message" name="message" rows="5" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="<?php echo e($contactFormMessagePlaceholder !== '' ? $contactFormMessagePlaceholder : 'Write your message...'); ?>" required><?php echo e(old('message')); ?></textarea>
                                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <button type="submit" class="theme-cta-solid inline-flex rounded-full px-6 py-3 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">
                                <?php echo e($contactFormSubmitText !== '' ? $contactFormSubmitText : 'Send Message'); ?>

                            </button>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
                        <h2 class="font-display text-2xl font-semibold text-slate-900"><?php echo e($contactInfoTitle !== '' ? $contactInfoTitle : 'Contact Information'); ?></h2>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            <p><span class="font-semibold"><?php echo e($contactAddressLabel !== '' ? $contactAddressLabel : 'Address'); ?>:</span> <?php echo e($school?->address ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                            <p><span class="font-semibold"><?php echo e($contactPhoneLabel !== '' ? $contactPhoneLabel : 'Phone number'); ?>:</span> <?php echo e($school?->phone ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                            <p><span class="font-semibold"><?php echo e($contactWhatsappLabel !== '' ? $contactWhatsappLabel : 'WhatsApp'); ?>:</span> <?php echo e(($publicPage['whatsapp'] ?? '') ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                            <p><span class="font-semibold"><?php echo e($contactEmailLabel !== '' ? $contactEmailLabel : 'Email'); ?>:</span> <?php echo e($school?->email ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet')); ?></p>
                        </div>

                        <?php if(!empty($contactItems)): ?>
                            <div class="mt-7 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <h3 class="text-sm font-bold uppercase tracking-wide text-brand-700"><?php echo e($contactMoreDetailsTitle !== '' ? $contactMoreDetailsTitle : 'More Contact Details'); ?></h3>
                                <div class="mt-3 space-y-2">
                                    <?php $__currentLoopData = $contactItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contactItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                            <p class="text-sm font-semibold text-slate-900"><?php echo e($contactItem['title']); ?></p>
                                            <p class="mt-1 text-sm text-slate-600"><?php echo e($contactItem['description']); ?></p>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

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
</body>
</html>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\public\contact.blade.php ENDPATH**/ ?>