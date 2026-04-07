<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($item['title']); ?> | <?php echo e($schoolName); ?></title>
    <?php
        $faviconPath = data_get($school?->settings, 'branding.favicon');
        $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    ?>
    <?php if($faviconPath): ?>
        <link rel="icon" type="image/png" href="<?php echo e(asset('storage/' . ltrim($faviconPath, '/'))); ?>">
    <?php endif; ?>
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

    /* FAQ pill filter buttons */
    .faq-pill {
        background-color: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .faq-pill:hover {
        border-color: var(--submenu-primary, #2D1D5C);
        color: var(--submenu-primary, #2D1D5C);
        background-color: #f8fafc;
    }
    .faq-pill .faq-pill-count {
        background-color: #e2e8f0;
        color: #475569;
    }
    .faq-pill-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        border-color: var(--submenu-primary, #2D1D5C) !important;
        color: #ffffff !important;
    }
    .faq-pill-active .faq-pill-count {
        background-color: rgba(255,255,255,0.25) !important;
        color: #ffffff !important;
    }
    .faq-pill span:last-child {
        background-color: #e2e8f0;
        color: #475569;
        transition: background-color 0.2s, color 0.2s;
    }
    .faq-pill-active span:last-child {
        background-color: rgba(255,255,255,0.22) !important;
        color: #fff !important;
    }
    /* FAQ accordion open state */
    .faq-item.faq-open .faq-chevron {
        transform: rotate(180deg);
    }
    .faq-item.faq-open .faq-trigger {
        background-color: #f8fafc;
    }
</style>
</head>
<?php
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

    $activeSectionItems = collect($menuSections)
        ->firstWhere('id', $sectionKey)['items'] ?? [];
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
    $submenuHighlightOneTitle = trim((string) ($publicPage['submenu_highlight_one_title'] ?? 'What Students Gain'));
    $submenuHighlightOneText = trim((string) ($publicPage['submenu_highlight_one_text'] ?? 'Learners receive practical support, clear expectations, and measurable progress across this focus area.'));
    $submenuHighlightTwoTitle = trim((string) ($publicPage['submenu_highlight_two_title'] ?? 'How We Deliver'));
    $submenuHighlightTwoText = trim((string) ($publicPage['submenu_highlight_two_text'] ?? 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.'));
    $submenuPrimaryButtonText = trim((string) ($publicPage['submenu_primary_button_text'] ?? 'Start Admission'));
    $submenuBackButtonPrefix = trim((string) ($publicPage['submenu_back_button_prefix'] ?? 'Back to'));
    $submenuMoreInPrefix = trim((string) ($publicPage['submenu_more_in_prefix'] ?? 'More In'));
?>
<body class="text-ink antialiased" style="background-color: <?php echo e($siteBackgroundColor); ?>; color: <?php echo e($themeBodyColor); ?>; --submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>; --theme-heading: <?php echo e($themeHeadingColor); ?>; --theme-body: <?php echo e($themeBodyColor); ?>; --theme-surface: <?php echo e($themeSurfaceColor); ?>; --theme-soft-surface: <?php echo e($themeSoftSurfaceColor); ?>;">
    <?php echo $__env->make('public.partials.page-loader', ['school' => $school, 'primary' => $submenuPrimaryColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="relative min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>

        <header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur" style="background-color: <?php echo e($headerBgColor); ?>;">
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
                <nav class="hidden items-center justify-center gap-1 rounded-2xl border border-slate-200/90 bg-white/95 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex" style="--submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                    <?php $__currentLoopData = $menuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $alignClass = ($loop->last || $loop->iteration >= count($menuSections) - 1) ? 'right-0' : 'left-0';
                            $isCurrentSection = $section['id'] === $sectionKey;
                        ?>
                        <div class="relative shrink-0" data-menu="<?php echo e($section['id']); ?>">
                            <?php if(!empty($section['items'])): ?>
                                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="submenu-<?php echo e($section['id']); ?>" class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 <?php echo e($isCurrentSection ? 'theme-nav-link-active' : ''); ?>">
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
                                            <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']])); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200 <?php echo e($section['id'] === $sectionKey && $menuItem['slug'] === $item['slug'] ? 'theme-submenu-link-active' : ''); ?>">
                                                <?php echo e($menuItem['title']); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e($section['link']); ?>" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 <?php echo e($isCurrentSection ? 'theme-nav-link-active' : ''); ?>">
                                    <?php echo e($section['label']); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
                <div class="flex items-center justify-end gap-2 sm:gap-3" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
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
                    <div class="mb-4 flex flex-col gap-2" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                        <a href="<?php echo e(route('admission.apply')); ?>" class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobileApplyText !== '' ? $mobileApplyText : 'Apply Now'); ?></a>
                        <a href="<?php echo e(route('portal.login')); ?>" class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200"><?php echo e($mobilePortalLoginText !== '' ? $mobilePortalLoginText : 'Portal Login'); ?></a>
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
                                                <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']])); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200 <?php echo e($section['id'] === $sectionKey && $menuItem['slug'] === $item['slug'] ? 'theme-mobile-submenu-link-active' : ''); ?>"><?php echo e($menuItem['title']); ?></a>
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
            
            <div class="relative w-full overflow-hidden" style="min-height:420px;display:flex;flex-direction:column;justify-content:flex-end;">
                <?php if(!empty($item['image'])): ?>
                    <img src="<?php echo e(asset('storage/' . ltrim($item['image'], '/'))); ?>" alt="<?php echo e($item['title']); ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.62) 100%);"></div>
                <?php else: ?>
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg, <?php echo e($submenuPrimaryColor); ?> 0%, rgba(45,29,92,0.88) 60%, <?php echo e($submenuSecondaryColor); ?>33 100%);"></div>
                    <div class="pointer-events-none" style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px);background-size:36px 36px;"></div>
                <?php endif; ?>
                <div class="relative px-6 pb-14 pt-28 lg:px-8 lg:pb-16 lg:pt-32" style="max-width:80rem;margin:0 auto;width:100%;">
                    <span class="inline-flex rounded-full px-4 py-1 text-xs font-bold uppercase tracking-[0.18em]" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);color:#ffffff;"><?php echo e($sectionLabel); ?></span>
                    <h1 class="mt-5 font-display font-bold leading-tight text-white" style="font-size:clamp(2rem,5vw,3.5rem);text-shadow:0 2px 16px rgba(0,0,0,0.45);max-width:42rem;"><?php echo e($item['title']); ?></h1>
                </div>
            </div>

            <section class="mx-auto max-w-7xl px-6 pb-14 pt-10 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-3">

                <?php if($sectionKey === 'admissions' && $item['slug'] === 'faqs'): ?>
                
                <?php
                // Category icons map (keyed by category id)
                $catIcons = [
                    'admissions'  => 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766z',
                    'fees'        => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                    'academics'   => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
                    'school-life' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z',
                    'results'     => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z',
                    'general'     => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z',
                ];
                $defaultIcon = 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z';

                $faqCategories = collect($publicPage['faqs'] ?? [])
                    ->filter(fn($cat) => !empty($cat['items']))
                    ->map(fn($cat) => array_merge($cat, [
                        'icon' => $catIcons[$cat['id']] ?? $defaultIcon,
                    ]))
                    ->values()
                    ->all();
                $totalFaqs = collect($faqCategories)->sum(fn($c) => count($c['items'] ?? []));
                ?>

                <div class="lg:col-span-2 space-y-6">

                    
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="11" cy="11" r="7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                        </span>
                        <input id="faq-search" type="text" placeholder="Search frequently asked questions…"
                               class="w-full rounded-2xl border border-slate-200 bg-white py-4 pl-14 pr-5 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 placeholder:text-slate-400">
                        <span id="faq-search-clear" class="absolute inset-y-0 right-0 hidden cursor-pointer items-center pr-5 text-slate-400 hover:text-slate-600" style="display:none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </span>
                    </div>

                    
                    <div class="flex flex-wrap gap-2" id="faq-category-pills">
                        <button type="button" data-faq-cat="all"
                                class="faq-pill faq-pill-active inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-bold transition duration-200">
                            All Questions
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-extrabold" id="faq-count-all"><?php echo e(collect($faqCategories)->sum(fn($c) => count($c['items']))); ?></span>
                        </button>
                        <?php $__currentLoopData = $faqCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" data-faq-cat="<?php echo e($cat['id']); ?>"
                                class="faq-pill inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-bold transition duration-200">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($cat['icon']); ?>"/>
                            </svg>
                            <?php echo e($cat['label']); ?>

                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-extrabold"><?php echo e(count($cat['items'])); ?></span>
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <div id="faq-no-results" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 h-9 w-9 text-slate-300"><circle cx="11" cy="11" r="7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                        <p class="text-sm font-semibold text-slate-500">No matching questions found.</p>
                        <p class="mt-1 text-xs text-slate-400">Try a different keyword or browse all categories.</p>
                    </div>

                    
                    <?php $__currentLoopData = $faqCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="faq-category-group" data-cat-group="<?php echo e($cat['id']); ?>">
                        <div class="mb-3 flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-white"
                                  style="background:<?php echo e($submenuPrimaryColor); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($cat['icon']); ?>"/>
                                </svg>
                            </span>
                            <h2 class="text-base font-bold text-slate-900"><?php echo e($cat['label']); ?></h2>
                            <div class="h-px flex-1 bg-slate-100"></div>
                        </div>

                        <div class="space-y-2">
                            <?php $__currentLoopData = $cat['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="faq-item rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all duration-200"
                                 data-question="<?php echo e(strtolower($faq['q'])); ?>"
                                 data-cat="<?php echo e($cat['id']); ?>">
                                <button type="button"
                                        onclick="toggleFaq(this)"
                                        class="faq-trigger flex w-full items-start justify-between gap-4 px-5 py-4 text-left text-sm font-semibold text-slate-900 transition duration-200 hover:bg-slate-50/60">
                                    <span class="flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-[11px] font-extrabold"
                                              style="background:<?php echo e($submenuSecondaryColor); ?>;color:<?php echo e($submenuPrimaryColor); ?>">
                                            <?php echo e($idx + 1); ?>

                                        </span>
                                        <span class="leading-relaxed"><?php echo e($faq['q']); ?></span>
                                    </span>
                                    <svg class="faq-chevron mt-0.5 h-5 w-5 shrink-0 transition-transform duration-300 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                                    </svg>
                                </button>
                                <div class="faq-answer hidden overflow-hidden">
                                    <div class="border-t border-slate-100 px-5 pb-5 pt-4">
                                        <div class="flex gap-3">
                                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-xs font-extrabold text-white"
                                                 style="background:<?php echo e($submenuPrimaryColor); ?>">A</div>
                                            <div class="text-sm leading-relaxed text-slate-600 rich-text-content space-y-1"><?php echo $faq['a']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <div class="relative overflow-hidden rounded-3xl p-8 text-center shadow-lg"
                         style="background:linear-gradient(135deg, <?php echo e($submenuPrimaryColor); ?> 0%, #3a2872 60%, #1a0f3a 100%);">
                        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.06) 1px,transparent 1px);background-size:20px 20px;pointer-events:none;"></div>
                        <div class="relative z-10">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white/80">
                                Still have questions?
                            </span>
                            <h3 class="mt-4 text-2xl font-extrabold text-white">We're happy to help</h3>
                            <p class="mt-2 text-sm text-white/65 max-w-sm mx-auto">Our admissions team is available Monday–Friday to answer any question not covered here.</p>
                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                <a href="<?php echo e(route('public.contact')); ?>"
                                   class="inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-bold transition duration-200 hover:-translate-y-0.5"
                                   style="background:<?php echo e($submenuSecondaryColor); ?>;color:<?php echo e($submenuPrimaryColor); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0-9.75 6.75-9.75-6.75"/></svg>
                                    Contact Us
                                </a>
                                <a href="<?php echo e(route('admission.apply')); ?>"
                                   class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-white/20">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                                    Start Application
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <?php else: ?>
                
                <?php
                    $displayDescription = !empty($item['rich_description'])
                        ? $item['rich_description']
                        : $item['description'];

                    $h1Title = !empty($item['highlight_one_title'])
                        ? $item['highlight_one_title']
                        : ($submenuHighlightOneTitle !== '' ? $submenuHighlightOneTitle : 'What Students Gain');
                    $h1Text = !empty($item['highlight_one_text'])
                        ? $item['highlight_one_text']
                        : ($submenuHighlightOneText !== '' ? $submenuHighlightOneText : 'Learners receive practical support, clear expectations, and measurable progress across this focus area.');
                    $h2Title = !empty($item['highlight_two_title'])
                        ? $item['highlight_two_title']
                        : ($submenuHighlightTwoTitle !== '' ? $submenuHighlightTwoTitle : 'How We Deliver');
                    $h2Text = !empty($item['highlight_two_text'])
                        ? $item['highlight_two_text']
                        : ($submenuHighlightTwoText !== '' ? $submenuHighlightTwoText : 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.');
                ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="rich-text-content mt-1 max-w-3xl text-base leading-relaxed text-muted sm:text-lg"><?php echo \App\Support\RichText::render($displayDescription); ?></div>

                    <?php
                        $pageImgOne = trim((string) ($item['image_one'] ?? ''));
                        $pageImgTwo = trim((string) ($item['image_two'] ?? ''));
                    ?>
                    <?php if($pageImgOne !== '' || $pageImgTwo !== ''): ?>
                    <div class="mt-6 grid gap-4 <?php echo e(($pageImgOne !== '' && $pageImgTwo !== '') ? 'sm:grid-cols-2' : 'sm:grid-cols-1 max-w-lg'); ?>">
                        <?php if($pageImgOne !== ''): ?>
                        <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                            <img src="<?php echo e(asset('storage/' . ltrim($pageImgOne, '/'))); ?>"
                                 alt="<?php echo e($item['title']); ?>"
                                 class="h-52 w-full object-cover transition duration-300 hover:scale-105">
                        </div>
                        <?php endif; ?>
                        <?php if($pageImgTwo !== ''): ?>
                        <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                            <img src="<?php echo e(asset('storage/' . ltrim($pageImgTwo, '/'))); ?>"
                                 alt="<?php echo e($item['title']); ?>"
                                 class="h-52 w-full object-cover transition duration-300 hover:scale-105">
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mt-7 grid gap-4 sm:grid-cols-2">
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-brand-700"><?php echo e($h1Title); ?></h2>
                            <div class="rich-text-content mt-2 text-sm leading-relaxed text-slate-600"><?php echo \App\Support\RichText::render($h1Text); ?></div>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-brand-700"><?php echo e($h2Title); ?></h2>
                            <div class="rich-text-content mt-2 text-sm leading-relaxed text-slate-600"><?php echo \App\Support\RichText::render($h2Text); ?></div>
                        </article>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="<?php echo e(route('admission.apply')); ?>" class="theme-cta-solid inline-flex rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5"><?php echo e($submenuPrimaryButtonText !== '' ? $submenuPrimaryButtonText : 'Start Admission'); ?></a>
                        <a href="<?php echo e(route('public.home')); ?>#<?php echo e($sectionKey); ?>" class="theme-cta-outline inline-flex rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5"><?php echo e($submenuBackButtonPrefix !== '' ? $submenuBackButtonPrefix : 'Back to'); ?> <?php echo e($sectionLabel); ?></a>
                    </div>
                </div>
                <?php endif; ?>

                    <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="font-display text-xl font-semibold text-slate-900"><?php echo e($submenuMoreInPrefix !== '' ? $submenuMoreInPrefix : 'More In'); ?> <?php echo e($sectionLabel); ?></h2>
                        <div class="mt-4 space-y-2" style="--submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>;">
                            <?php $__currentLoopData = $activeSectionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('public.submenu', ['section' => $sectionKey, 'slug' => $sectionItem['slug']])); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200 <?php echo e($sectionItem['slug'] === $item['slug'] ? 'theme-mobile-submenu-link-active font-semibold' : ''); ?>">
                                    <?php echo e($sectionItem['title']); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </aside>
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

    
    <?php if($sectionKey === 'admissions' && $item['slug'] === 'faqs'): ?>
    <script>
    (function () {
        // ── Accordion ───────────────────────────────────────────────────
        window.toggleFaq = function (btn) {
            const item   = btn.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const isOpen = item.classList.contains('faq-open');

            // Close all open items in the same group for a single-open accordion
            const group = item.closest('.faq-category-group');
            if (group) {
                group.querySelectorAll('.faq-item.faq-open').forEach(function (openItem) {
                    if (openItem !== item) {
                        openItem.classList.remove('faq-open');
                        openItem.querySelector('.faq-answer').classList.add('hidden');
                    }
                });
            }

            if (isOpen) {
                item.classList.remove('faq-open');
                answer.classList.add('hidden');
            } else {
                item.classList.add('faq-open');
                answer.classList.remove('hidden');
                // Smooth scroll into view if partly off-screen
                setTimeout(function () {
                    const rect = item.getBoundingClientRect();
                    if (rect.top < 80) {
                        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }, 50);
            }
        };

        // ── Search ──────────────────────────────────────────────────────
        const searchInput  = document.getElementById('faq-search');
        const searchClear  = document.getElementById('faq-search-clear');
        const noResults    = document.getElementById('faq-no-results');
        const allItems     = Array.from(document.querySelectorAll('.faq-item'));
        const allGroups    = Array.from(document.querySelectorAll('.faq-category-group'));

        let activeCategory = 'all';

        function applyFilters() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            allGroups.forEach(function (group) {
                const catId = group.getAttribute('data-cat-group');
                const catMatch = activeCategory === 'all' || activeCategory === catId;
                let groupVisible = 0;

                group.querySelectorAll('.faq-item').forEach(function (item) {
                    const questionText = item.getAttribute('data-question') || '';
                    const answerText   = item.querySelector('.faq-answer')
                                            ? item.querySelector('.faq-answer').textContent.toLowerCase()
                                            : '';
                    const textMatch    = !query || questionText.includes(query) || answerText.includes(query);
                    const show         = catMatch && textMatch;

                    item.style.display = show ? '' : 'none';
                    if (show) { groupVisible++; visibleCount++; }
                });

                group.style.display = groupVisible > 0 ? '' : 'none';
            });

            if (noResults) {
                noResults.classList.toggle('hidden', visibleCount > 0);
            }

            // Show / hide search clear button
            if (searchClear) {
                searchClear.style.display = query ? 'flex' : 'none';
            }

            // Auto-open first item when searching
            if (query) {
                allItems.forEach(function (item) {
                    if (item.style.display !== 'none') {
                        const answer = item.querySelector('.faq-answer');
                        item.classList.add('faq-open');
                        if (answer) answer.classList.remove('hidden');
                    }
                });
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        if (searchClear) {
            searchClear.addEventListener('click', function () {
                searchInput.value = '';
                applyFilters();
                searchInput.focus();
            });
        }

        // ── Category pills ──────────────────────────────────────────────
        const pills = Array.from(document.querySelectorAll('#faq-category-pills [data-faq-cat]'));

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                activeCategory = pill.getAttribute('data-faq-cat');

                pills.forEach(function (p) {
                    p.classList.remove('faq-pill-active');
                });
                pill.classList.add('faq-pill-active');

                applyFilters();
            });
        });

        // ── Keyboard shortcut: "/" to focus search ──────────────────────
        document.addEventListener('keydown', function (e) {
            if (e.key === '/' && document.activeElement !== searchInput
                && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>




<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/public/submenu.blade.php ENDPATH**/ ?>