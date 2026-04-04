
<?php
    $navSchoolName        = $school?->name ?? 'Our School';
    $navPrimary           = data_get($theme, 'primary.500',          '#2D1D5C');
    $navSecondary         = data_get($theme, 'secondary.500',        '#DFE753');
    $navHeader            = data_get($theme, 'header',               '#2D1D5C');
    $navHoverText         = data_get($theme, 'primary_text_on_secondary', '#2D1D5C');

    $navApplyText         = trim((string) ($publicPage['header_apply_text']        ?? 'Apply'));
    $navLoginText         = trim((string) ($publicPage['header_portal_login_text'] ?? 'Portal Login'));
    $navMobileApplyText   = trim((string) ($publicPage['mobile_apply_text']        ?? 'Apply Now'));
    $navMobileLoginText   = trim((string) ($publicPage['mobile_portal_login_text'] ?? 'Portal Login'));
    $navMobileMenuTitle   = trim((string) ($publicPage['mobile_menu_title']        ?? 'Menu'));
    $navOverviewSuffix    = trim((string) ($publicPage['menu_overview_suffix']     ?? 'Overview'));

    $navPrograms      = $publicPage['programs']     ?? [];
    $navAdmissions    = $publicPage['admissions']   ?? [];
    $navAcademics     = $publicPage['academics']    ?? [];
    $navFacilities    = $publicPage['facilities']   ?? [];
    $navAbout         = $publicPage['about']        ?? [];
    $navStudentLife   = $publicPage['student_life'] ?? [];
    $navParents       = $publicPage['parents']      ?? [];

    $navFirstLink = function (string $sectionId, array $items) {
        $first = collect($items)->first();
        if (!$first) return null;
        $slug = \Illuminate\Support\Str::slug(is_array($first) ? ($first['title'] ?? '') : $first);
        return $slug ? route('public.submenu', ['section' => $sectionId, 'slug' => $slug]) : null;
    };

    $navSections = [
        ['label' => 'Home',       'id' => 'home',        'link' => route('public.home'),    'items' => []],
        ['label' => trim((string)($publicPage['programs_label']     ?? 'Programs')),    'id' => 'programs',     'link' => $navFirstLink('programs',     $navPrograms)    ?? route('public.home'), 'items' => collect($navPrograms)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['admissions_label']   ?? 'Admissions')),  'id' => 'admissions',   'link' => $navFirstLink('admissions',   $navAdmissions)  ?? route('public.home'), 'items' => collect($navAdmissions)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['academics_label']    ?? 'Academics')),   'id' => 'academics',    'link' => $navFirstLink('academics',    $navAcademics)   ?? route('public.home'), 'items' => collect($navAcademics)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['facilities_label']   ?? 'Facilities')),  'id' => 'facilities',   'link' => $navFirstLink('facilities',   $navFacilities)  ?? route('public.home'), 'items' => collect($navFacilities)->filter()->values()->all()],
        ['label' => trim((string)($publicPage['about_label']        ?? 'About Us')),    'id' => 'about',        'link' => $navFirstLink('about',        $navAbout)       ?? route('public.home'), 'items' => collect($navAbout)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['student_life_label'] ?? 'Student Life')),'id' => 'student-life', 'link' => $navFirstLink('student-life', $navStudentLife) ?? route('public.home'), 'items' => collect($navStudentLife)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['parents_label']      ?? 'Parents')),     'id' => 'parents',      'link' => $navFirstLink('parents',      $navParents)     ?? route('public.home'), 'items' => collect($navParents)->pluck('title')->filter()->values()->all()],
        ['label' => trim((string)($publicPage['contact_label']      ?? 'Contact')),     'id' => 'contact',      'link' => route('public.contact'),                                                'items' => []],
    ];
?>

<header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur"
        style="background-color:<?php echo e($navHeader); ?>;--submenu-primary:<?php echo e($navPrimary); ?>;--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">

    <div class="mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-4 px-6 py-3 lg:px-8">

        
        <a href="<?php echo e(route('public.home')); ?>" class="flex shrink-0 items-center transition duration-200 hover:opacity-90">
            <?php if($school?->logo): ?>
                <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="<?php echo e($navSchoolName); ?>"
                     style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;object-fit:cover;border:1px solid rgba(255,255,255,0.2);background:#fff;">
            <?php else: ?>
                <div style="height:2.75rem;width:2.75rem;min-width:2.75rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;letter-spacing:0.1em;color:#fff;">
                    <?php echo e(\Illuminate\Support\Str::upper(collect(preg_split('/\s+/', trim($navSchoolName)))->filter()->take(2)->map(fn($w) => \Illuminate\Support\Str::substr($w,0,1))->implode(''))); ?>

                </div>
            <?php endif; ?>
        </a>

        
        <nav class="hidden items-center justify-center gap-1 rounded-2xl border border-slate-200/90 bg-white/95 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex"
             style="--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
            <?php $__currentLoopData = $navSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $alignClass = ($loop->last || $loop->iteration >= count($navSections) - 1) ? 'right-0' : 'left-0'; ?>
            <div class="relative shrink-0" data-menu="<?php echo e($section['id']); ?>">
                <?php if(!empty($section['items'])): ?>
                    <button type="button" data-menu-toggle aria-expanded="false" aria-controls="nav-submenu-<?php echo e($section['id']); ?>"
                            class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none">
                        <?php echo e($section['label']); ?>

                    </button>
                    <div id="nav-submenu-<?php echo e($section['id']); ?>" data-menu-panel class="absolute <?php echo e($alignClass); ?> top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                        <div class="theme-submenu-panel rounded-2xl border p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur"
                             style="--submenu-primary:<?php echo e($navPrimary); ?>;--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
                            <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]"><?php echo e($section['label']); ?></p>
                            <a href="<?php echo e($section['link']); ?>" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                <?php echo e($section['label']); ?> <?php echo e($navOverviewSuffix ?: 'Overview'); ?>

                            </a>
                            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)])); ?>"
                               class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200">
                                <?php echo e($menuItem); ?>

                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e($section['link']); ?>" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none">
                        <?php echo e($section['label']); ?>

                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        
        <div class="flex items-center justify-end gap-2 sm:gap-3"
             style="--submenu-primary:<?php echo e($navPrimary); ?>;--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
            <a href="<?php echo e(route('admission.apply')); ?>"
               class="theme-header-action-outline hidden items-center whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex">
                <?php echo e($navApplyText ?: 'Apply'); ?>

            </a>
            <a href="<?php echo e(route('login')); ?>"
               class="theme-header-action-solid inline-flex items-center whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">
                <?php echo e($navLoginText ?: 'Portal Login'); ?>

            </a>
            <button type="button" data-mobile-menu-toggle aria-expanded="false" aria-controls="public-mobile-menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/40 text-white transition duration-200 hover:bg-white/10 xl:hidden">
                <svg data-mobile-menu-open-icon class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg data-mobile-menu-close-icon class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                </svg>
            </button>
        </div>
    </div>

    
    <div data-mobile-menu-backdrop class="pointer-events-none fixed inset-0 z-40 bg-slate-950/40 opacity-0 transition duration-300 xl:hidden"></div>

    
    <aside id="public-mobile-menu" data-mobile-menu
           class="pointer-events-none fixed inset-y-0 right-0 z-50 w-full max-w-sm translate-x-full overflow-y-auto border-l border-slate-200 bg-white shadow-2xl transition duration-300 xl:hidden">
        <div class="sticky top-0 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">
            <p class="text-lg font-semibold text-slate-900"><?php echo e($navMobileMenuTitle ?: 'Menu'); ?></p>
            <button type="button" data-mobile-menu-close
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition duration-200 hover:bg-slate-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-5">
            <div class="mb-4 flex flex-col gap-2"
                 style="--submenu-primary:<?php echo e($navPrimary); ?>;--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
                <a href="<?php echo e(route('admission.apply')); ?>"
                   class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200">
                    <?php echo e($navMobileApplyText ?: 'Apply Now'); ?>

                </a>
                <a href="<?php echo e(route('login')); ?>"
                   class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200">
                    <?php echo e($navMobileLoginText ?: 'Portal Login'); ?>

                </a>
            </div>
            <div class="space-y-2"
                 style="--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
                <?php $__currentLoopData = $navSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <?php if(!empty($section['items'])): ?>
                        <button type="button" data-mobile-submenu-toggle data-target="nav-mobile-sub-<?php echo e($section['id']); ?>"
                                aria-expanded="false"
                                class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200">
                            <span><?php echo e($section['label']); ?></span>
                            <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                        </button>
                        <div id="nav-mobile-sub-<?php echo e($section['id']); ?>" data-mobile-submenu-panel class="hidden px-4 pb-4"
                             style="--submenu-primary:<?php echo e($navPrimary); ?>;--submenu-secondary:<?php echo e($navSecondary); ?>;--submenu-hover-text:<?php echo e($navHoverText); ?>;">
                            <div class="space-y-1 border-l border-slate-200 pl-4">
                                <a href="<?php echo e($section['link']); ?>" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                    <?php echo e($section['label']); ?> <?php echo e($navOverviewSuffix ?: 'Overview'); ?>

                                </a>
                                <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)])); ?>"
                                   class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200">
                                    <?php echo e($menuItem); ?>

                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e($section['link']); ?>" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200">
                            <?php echo e($section['label']); ?>

                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </aside>

</header>

<script>
(function () {
    const menus = Array.from(document.querySelectorAll('[data-menu]'));
    const mobileMenuToggle    = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenuOpenIcon  = document.querySelector('[data-mobile-menu-open-icon]');
    const mobileMenuCloseIcon = document.querySelector('[data-mobile-menu-close-icon]');
    const mobileMenu          = document.querySelector('[data-mobile-menu]');
    const mobileMenuBackdrop  = document.querySelector('[data-mobile-menu-backdrop]');
    const mobileMenuCloseBtn  = document.querySelector('[data-mobile-menu-close]');
    const mobileSubToggles    = Array.from(document.querySelectorAll('[data-mobile-submenu-toggle]'));
    let desktopTimer = null;

    const isDesktop = () => window.innerWidth >= 1280;

    const closeAll = (exceptId = null) => {
        menus.forEach(menu => {
            const id     = menu.getAttribute('data-menu');
            const toggle = menu.querySelector('[data-menu-toggle]');
            const panel  = menu.querySelector('[data-menu-panel]');
            if (!toggle || !panel) return;
            const open = exceptId !== null && id === exceptId;
            panel.classList.toggle('hidden', !open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    };

    const queueClose = (delay = 90) => {
        clearTimeout(desktopTimer);
        desktopTimer = setTimeout(() => closeAll(), delay);
    };

    menus.forEach(menu => {
        const id     = menu.getAttribute('data-menu');
        const toggle = menu.querySelector('[data-menu-toggle]');
        const panel  = menu.querySelector('[data-menu-panel]');
        if (!toggle || !panel) return;

        toggle.addEventListener('click', e => {
            e.preventDefault();
            closeAll(toggle.getAttribute('aria-expanded') === 'true' ? null : id);
        });
        menu.addEventListener('mouseenter', () => { if (isDesktop()) { clearTimeout(desktopTimer); closeAll(id); } });
        menu.addEventListener('mouseleave', () => { if (isDesktop()) queueClose(); });
        menu.addEventListener('focusin',    () => { if (isDesktop()) { clearTimeout(desktopTimer); closeAll(id); } });
        menu.addEventListener('focusout',   e  => {
            if (isDesktop() && (!e.relatedTarget || !menu.contains(e.relatedTarget))) queueClose(70);
        });
        panel.querySelectorAll('a').forEach(a => a.addEventListener('click', () => closeAll()));
    });

    const closeMobileSubs = (exceptTarget = null) => {
        mobileSubToggles.forEach(t => {
            const tid       = t.getAttribute('data-target');
            const subPanel  = tid ? document.getElementById(tid) : null;
            const indicator = t.querySelector('[data-mobile-submenu-indicator]');
            const open      = exceptTarget !== null && tid === exceptTarget;
            if (!subPanel) return;
            subPanel.classList.toggle('hidden', !open);
            t.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (indicator) indicator.textContent = open ? '-' : '+';
        });
    };

    const setMobileMenu = open => {
        if (!mobileMenu || !mobileMenuToggle || !mobileMenuBackdrop) return;
        mobileMenu.classList.toggle('translate-x-full', !open);
        mobileMenu.classList.toggle('pointer-events-none', !open);
        mobileMenuBackdrop.classList.toggle('opacity-0', !open);
        mobileMenuBackdrop.classList.toggle('pointer-events-none', !open);
        mobileMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('overflow-hidden', open);
        if (mobileMenuOpenIcon)  mobileMenuOpenIcon.classList.toggle('hidden', open);
        if (mobileMenuCloseIcon) mobileMenuCloseIcon.classList.toggle('hidden', !open);
        if (!open) closeMobileSubs();
    };

    mobileMenuToggle?.addEventListener('click', () => setMobileMenu(mobileMenuToggle.getAttribute('aria-expanded') !== 'true'));
    mobileMenuCloseBtn?.addEventListener('click', () => setMobileMenu(false));
    mobileMenuBackdrop?.addEventListener('click', () => setMobileMenu(false));

    mobileSubToggles.forEach(t => t.addEventListener('click', () => {
        const tid = t.getAttribute('data-target');
        if (tid) closeMobileSubs(t.getAttribute('aria-expanded') === 'true' ? null : tid);
    }));

    mobileMenu?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMobileMenu(false)));
})();
</script>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/public/partials/nav.blade.php ENDPATH**/ ?>