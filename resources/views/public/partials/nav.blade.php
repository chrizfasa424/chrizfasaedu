{{--
    Public nav partial.
    Requires: $school, $publicPage, $theme (array from ThemePalette::fromPublicPage)
    Optional: $activeSection (home|programs|admissions|academics|facilities|about|student-life|contact)
--}}
@php
    use Illuminate\Support\Str;

    $navSchoolName = $school?->name ?? 'Our School';
    $navPrimary = data_get($theme, 'primary.500', '#25333E');
    $navSecondary = data_get($theme, 'secondary.500', '#DFE753');
    $navHeader = data_get($theme, 'header', '#25333E');
    $navHoverText = data_get($theme, 'primary_text_on_secondary', '#25333E');

    $navApplyText = trim((string) ($publicPage['header_apply_text'] ?? 'Apply'));
    $navLoginText = trim((string) ($publicPage['header_portal_login_text'] ?? 'Portal Login'));
    $navMobileApplyText = trim((string) ($publicPage['mobile_apply_text'] ?? 'Apply Now'));
    $navMobileLoginText = trim((string) ($publicPage['mobile_portal_login_text'] ?? 'Portal Login'));
    $navMobileMenuTitle = trim((string) ($publicPage['mobile_menu_title'] ?? 'Menu'));
    $navOverviewSuffix = trim((string) ($publicPage['menu_overview_suffix'] ?? 'Overview'));

    $navAboutSource = $publicPage['about'] ?? [];
    if (empty($navAboutSource) && !empty($publicPage['about_banners'])) {
        $navAboutSource = collect($publicPage['about_banners'])
            ->map(function ($item) {
                return [
                    'title' => trim((string) data_get($item, 'title', '')),
                    'description' => trim((string) data_get($item, 'description', '')),
                ];
            })
            ->filter(fn (array $item) => $item['title'] !== '')
            ->values()
            ->all();
    }

    $normalizeItems = static function (array $items): array {
        return collect($items)
            ->map(function ($item) {
                if (is_array($item)) {
                    $title = trim((string) ($item['title'] ?? ''));
                } else {
                    $title = trim((string) $item);
                }

                return [
                    'title' => $title,
                    'slug' => Str::slug($title),
                ];
            })
            ->filter(fn (array $item) => $item['title'] !== '' && $item['slug'] !== '')
            ->values()
            ->all();
    };

    $navSections = [
        [
            'label' => 'Home',
            'id' => 'home',
            'link' => route('public.home'),
            'items' => [],
        ],
        [
            'label' => trim((string) ($publicPage['programs_label'] ?? 'Programs')) ?: 'Programs',
            'id' => 'programs',
            'items' => $normalizeItems($publicPage['programs'] ?? []),
        ],
        [
            'label' => trim((string) ($publicPage['admissions_label'] ?? 'Admissions')) ?: 'Admissions',
            'id' => 'admissions',
            'items' => $normalizeItems($publicPage['admissions'] ?? []),
        ],
        [
            'label' => trim((string) ($publicPage['academics_label'] ?? 'Academics')) ?: 'Academics',
            'id' => 'academics',
            'items' => $normalizeItems($publicPage['academics'] ?? []),
        ],
        [
            'label' => trim((string) ($publicPage['facilities_label'] ?? 'Facilities')) ?: 'Facilities',
            'id' => 'facilities',
            'items' => $normalizeItems($publicPage['facilities'] ?? []),
        ],
        [
            'label' => trim((string) ($publicPage['about_label'] ?? 'About Us')) ?: 'About Us',
            'id' => 'about',
            'items' => $normalizeItems($navAboutSource),
        ],
        [
            'label' => trim((string) ($publicPage['student_life_label'] ?? 'Student Life')) ?: 'Student Life',
            'id' => 'student-life',
            'items' => $normalizeItems($publicPage['student_life'] ?? []),
        ],
        [
            'label' => trim((string) ($publicPage['contact_label'] ?? 'Contact')) ?: 'Contact',
            'id' => 'contact',
            'link' => route('public.contact'),
            'items' => [],
        ],
    ];

    $navSections = collect($navSections)->map(function (array $section) {
        if (!array_key_exists('link', $section)) {
            $firstSlug = $section['items'][0]['slug'] ?? null;
            $section['link'] = $firstSlug
                ? route('public.submenu', ['section' => $section['id'], 'slug' => $firstSlug])
                : route('public.home');
        }

        return $section;
    })->values()->all();

    $routeName = optional(request()->route())->getName();
    $routeSection = trim((string) request()->route('section', ''));
    $routeSlug = trim((string) request()->route('slug', ''));

    $resolvedActiveSection = trim((string) ($activeSection ?? ''));
    if ($resolvedActiveSection === '') {
        if ($routeName === 'public.home') {
            $resolvedActiveSection = 'home';
        } elseif ($routeName === 'public.contact') {
            $resolvedActiveSection = 'contact';
        } elseif ($routeName === 'public.submenu' && $routeSection !== '') {
            $resolvedActiveSection = $routeSection;
        }
    }
@endphp

<header class="public-site-header sticky top-0 z-50"
        style="background-color:{{ $navHeader }};--submenu-primary:{{ $navPrimary }};--submenu-secondary:{{ $navSecondary }};--submenu-hover-text:{{ $navHoverText }};">
    <div class="public-header-shell mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('public.home') }}" class="flex shrink-0 items-center transition duration-200 hover:opacity-90">
            @if($school?->logo)
                <img src="{{ asset('storage/' . ltrim($school->logo, '/')) }}" alt="{{ $navSchoolName }}"
                     class="h-11 w-11 min-w-[2.75rem] rounded-xl border border-white/20 bg-white object-cover shadow-sm">
            @else
                <div class="flex h-11 w-11 min-w-[2.75rem] items-center justify-center rounded-xl border border-white/20 bg-white/15 text-[0.8rem] font-extrabold tracking-[0.1em] text-white">
                    {{ Str::upper(collect(preg_split('/\s+/', trim($navSchoolName)))->filter()->take(2)->map(fn($w) => Str::substr($w, 0, 1))->implode('')) }}
                </div>
            @endif
        </a>

        <nav class="public-desktop-nav items-center justify-center gap-1 rounded-2xl bg-white/92 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm backdrop-blur">
            @foreach($navSections as $section)
                @php
                    $alignClass = ($loop->last || $loop->iteration >= count($navSections) - 1) ? 'right-0' : 'left-0';
                    $isActiveTop = $resolvedActiveSection === $section['id'];
                @endphp
                <div class="relative shrink-0" data-menu="{{ $section['id'] }}">
                    @if(!empty($section['items']))
                        <button type="button" data-menu-toggle aria-expanded="false" aria-controls="nav-submenu-{{ $section['id'] }}"
                                class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none {{ $isActiveTop ? 'theme-nav-link-active' : '' }}">
                            {{ $section['label'] }}
                        </button>
                        <div id="nav-submenu-{{ $section['id'] }}" data-menu-panel class="absolute {{ $alignClass }} top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                            <div class="theme-submenu-panel rounded-2xl p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur">
                                <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]">{{ $section['label'] }}</p>
                                <a href="{{ $section['link'] }}" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                    {{ $section['label'] }} {{ $navOverviewSuffix ?: 'Overview' }}
                                </a>
                                @foreach($section['items'] as $menuItem)
                                    @php
                                        $isActiveSubmenuItem = $section['id'] === $routeSection && $menuItem['slug'] === $routeSlug;
                                    @endphp
                                    <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']]) }}"
                                       class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200 {{ $isActiveSubmenuItem ? 'theme-submenu-link-active' : '' }}">
                                        {{ $menuItem['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $section['link'] }}" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none {{ $isActiveTop ? 'theme-nav-link-active' : '' }}">
                            {{ $section['label'] }}
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="flex items-center justify-end gap-2 sm:gap-3">
            <a href="{{ route('admission.apply') }}"
               class="theme-header-action-outline hidden items-center whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex">
                {{ $navApplyText ?: 'Apply' }}
            </a>
            <a href="{{ route('portal.login') }}"
               class="theme-header-action-solid inline-flex items-center whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">
                {{ $navLoginText ?: 'Portal Login' }}
            </a>
            <button type="button" data-mobile-menu-toggle aria-expanded="false" aria-controls="public-mobile-menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/40 text-white transition duration-200 hover:bg-white/10 lg:hidden">
                <svg data-mobile-menu-open-icon class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg data-mobile-menu-close-icon class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                </svg>
            </button>
        </div>
    </div>

    <div data-mobile-menu-backdrop class="pointer-events-none fixed inset-0 z-40 bg-slate-950/40 opacity-0 transition duration-300 lg:hidden"></div>

    <aside id="public-mobile-menu" data-mobile-menu
           class="pointer-events-none fixed inset-y-0 right-0 z-50 w-full max-w-sm translate-x-full overflow-y-auto border-l border-slate-200 bg-white shadow-2xl transition duration-300 lg:hidden">
        <div class="sticky top-0 flex items-center justify-between bg-white/95 px-5 py-4 backdrop-blur">
            <p class="text-lg font-semibold text-slate-900">{{ $navMobileMenuTitle ?: 'Menu' }}</p>
            <button type="button" data-mobile-menu-close
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition duration-200 hover:bg-slate-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-5" data-mobile-menu-body>
            <div class="mb-4 flex flex-col gap-2">
                <a href="{{ route('admission.apply') }}"
                   class="theme-mobile-action-outline inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition duration-200">
                    {{ $navMobileApplyText ?: 'Apply Now' }}
                </a>
                <a href="{{ route('portal.login') }}"
                   class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200">
                    {{ $navMobileLoginText ?: 'Portal Login' }}
                </a>
            </div>
            <div class="space-y-2">
                @foreach($navSections as $section)
                    @php
                        $isActiveTop = $resolvedActiveSection === $section['id'];
                    @endphp
                    <div class="rounded-xl bg-white shadow-sm">
                        @if(!empty($section['items']))
                            <button type="button" data-mobile-submenu-toggle data-target="nav-mobile-sub-{{ $section['id'] }}"
                                    aria-expanded="false"
                                    class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200 {{ $isActiveTop ? 'theme-nav-link-active' : '' }}">
                                <span>{{ $section['label'] }}</span>
                                <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                            </button>
                            <div id="nav-mobile-sub-{{ $section['id'] }}" data-mobile-submenu-panel class="hidden px-4 pb-4">
                                <div class="space-y-1 border-l border-slate-200 pl-4">
                                    <a href="{{ $section['link'] }}" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                        {{ $section['label'] }} {{ $navOverviewSuffix ?: 'Overview' }}
                                    </a>
                                    @foreach($section['items'] as $menuItem)
                                        @php
                                            $isActiveSubmenuItem = $section['id'] === $routeSection && $menuItem['slug'] === $routeSlug;
                                        @endphp
                                        <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => $menuItem['slug']]) }}"
                                           class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200 {{ $isActiveSubmenuItem ? 'theme-mobile-submenu-link-active' : '' }}">
                                            {{ $menuItem['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $section['link'] }}" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200 {{ $isActiveTop ? 'theme-nav-link-active' : '' }}">
                                {{ $section['label'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
</header>

@once
    <script>
    (function () {
        const menus = Array.from(document.querySelectorAll('[data-menu]'));
        const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenuOpenIcon = document.querySelector('[data-mobile-menu-open-icon]');
        const mobileMenuCloseIcon = document.querySelector('[data-mobile-menu-close-icon]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');
        const mobileMenuBackdrop = document.querySelector('[data-mobile-menu-backdrop]');
        const mobileMenuCloseBtn = document.querySelector('[data-mobile-menu-close]');
        const mobileSubToggles = Array.from(document.querySelectorAll('[data-mobile-submenu-toggle]'));
        const mobileMenuBody = mobileMenu?.querySelector('[data-mobile-menu-body]') ?? null;
        let desktopTimer = null;

        const isDesktop = () => window.innerWidth >= 1024;

        const closeAll = (exceptId = null) => {
            menus.forEach((menu) => {
                const id = menu.getAttribute('data-menu');
                const toggle = menu.querySelector('[data-menu-toggle]');
                const panel = menu.querySelector('[data-menu-panel]');
                if (!toggle || !panel) {
                    return;
                }

                const open = exceptId !== null && id === exceptId;
                panel.classList.toggle('hidden', !open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        };

        const queueClose = (delay = 90) => {
            clearTimeout(desktopTimer);
            desktopTimer = window.setTimeout(() => closeAll(), delay);
        };

        menus.forEach((menu) => {
            const id = menu.getAttribute('data-menu');
            const toggle = menu.querySelector('[data-menu-toggle]');
            const panel = menu.querySelector('[data-menu-panel]');
            if (!toggle || !panel) {
                return;
            }

            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                closeAll(toggle.getAttribute('aria-expanded') === 'true' ? null : id);
            });

            menu.addEventListener('mouseenter', () => {
                if (!isDesktop()) {
                    return;
                }
                clearTimeout(desktopTimer);
                closeAll(id);
            });

            menu.addEventListener('mouseleave', () => {
                if (!isDesktop()) {
                    return;
                }
                queueClose();
            });

            menu.addEventListener('focusin', () => {
                if (!isDesktop()) {
                    return;
                }
                clearTimeout(desktopTimer);
                closeAll(id);
            });

            menu.addEventListener('focusout', (event) => {
                if (!isDesktop()) {
                    return;
                }
                if (!event.relatedTarget || !menu.contains(event.relatedTarget)) {
                    queueClose(70);
                }
            });

            panel.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => closeAll()));
        });

        const closeMobileSubs = (exceptTarget = null) => {
            mobileSubToggles.forEach((toggle) => {
                const targetId = toggle.getAttribute('data-target');
                const panel = targetId ? document.getElementById(targetId) : null;
                const indicator = toggle.querySelector('[data-mobile-submenu-indicator]');
                const open = exceptTarget !== null && targetId === exceptTarget;

                if (!panel) {
                    return;
                }

                panel.classList.toggle('hidden', !open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

                if (indicator) {
                    indicator.textContent = open ? '-' : '+';
                }
            });
        };

        const enforceMobileDrawerStyles = () => {
            if (!mobileMenu || !mobileMenuBackdrop) {
                return;
            }

            const header = mobileMenu.querySelector(':scope > div:first-child');
            const headerHeight = header ? `${Math.ceil(header.getBoundingClientRect().height || 64)}px` : '64px';

            mobileMenu.style.setProperty('position', 'fixed', 'important');
            mobileMenu.style.setProperty('top', '0', 'important');
            mobileMenu.style.setProperty('right', '0', 'important');
            mobileMenu.style.setProperty('bottom', '0', 'important');
            mobileMenu.style.setProperty('left', 'auto', 'important');
            mobileMenu.style.setProperty('width', '100%', 'important');
            mobileMenu.style.setProperty('max-width', '22rem', 'important');
            mobileMenu.style.setProperty('height', '100vh', 'important');
            mobileMenu.style.setProperty('min-height', '100vh', 'important');
            mobileMenu.style.setProperty('display', 'flex', 'important');
            mobileMenu.style.setProperty('flex-direction', 'column', 'important');
            mobileMenu.style.setProperty('background', '#ffffff', 'important');
            mobileMenu.style.setProperty('z-index', '9999', 'important');
            mobileMenu.style.setProperty('overflow', 'hidden', 'important');

            mobileMenuBackdrop.style.setProperty('position', 'fixed', 'important');
            mobileMenuBackdrop.style.setProperty('inset', '0', 'important');
            mobileMenuBackdrop.style.setProperty('z-index', '9998', 'important');

            if (mobileMenuBody) {
                // Absolute fill below header avoids collapsed body on stale/colliding utility CSS.
                mobileMenuBody.style.setProperty('position', 'absolute', 'important');
                mobileMenuBody.style.setProperty('top', headerHeight, 'important');
                mobileMenuBody.style.setProperty('left', '0', 'important');
                mobileMenuBody.style.setProperty('right', '0', 'important');
                mobileMenuBody.style.setProperty('bottom', '0', 'important');
                mobileMenuBody.style.setProperty('display', 'block', 'important');
                mobileMenuBody.style.setProperty('width', '100%', 'important');
                mobileMenuBody.style.setProperty('height', `calc(100vh - ${headerHeight})`, 'important');
                mobileMenuBody.style.setProperty('min-height', `calc(100vh - ${headerHeight})`, 'important');
                mobileMenuBody.style.setProperty('overflow-y', 'auto', 'important');
                mobileMenuBody.style.setProperty('background', '#ffffff', 'important');
                mobileMenuBody.style.setProperty('padding-bottom', 'max(1.25rem, env(safe-area-inset-bottom))', 'important');
            }
        };

        const setMobileMenu = (open) => {
            if (!mobileMenu || !mobileMenuToggle || !mobileMenuBackdrop) {
                return;
            }

            enforceMobileDrawerStyles();
            mobileMenu.setAttribute('data-open', open ? 'true' : 'false');
            mobileMenu.style.transform = open ? 'translateX(0)' : 'translateX(100%)';
            mobileMenu.style.visibility = open ? 'visible' : 'hidden';
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
                closeMobileSubs();
            }
        };

        if (mobileMenu) {
            enforceMobileDrawerStyles();
            mobileMenu.setAttribute('data-open', 'false');
            mobileMenu.style.transform = 'translateX(100%)';
            mobileMenu.style.visibility = 'hidden';
        }

        mobileMenuToggle?.addEventListener('click', () => setMobileMenu(mobileMenuToggle.getAttribute('aria-expanded') !== 'true'));
        mobileMenuCloseBtn?.addEventListener('click', () => setMobileMenu(false));
        mobileMenuBackdrop?.addEventListener('click', () => setMobileMenu(false));

        mobileSubToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const targetId = toggle.getAttribute('data-target');
                if (!targetId) {
                    return;
                }

                closeMobileSubs(toggle.getAttribute('aria-expanded') === 'true' ? null : targetId);
            });
        });

        mobileMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setMobileMenu(false)));

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[data-menu]')) {
                closeAll();
            }

            if (
                mobileMenu &&
                !event.target.closest('[data-mobile-menu]') &&
                !event.target.closest('[data-mobile-menu-toggle]') &&
                !event.target.closest('[data-mobile-menu-backdrop]')
            ) {
                setMobileMenu(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeAll();
                setMobileMenu(false);
            }
        });

        window.addEventListener('resize', () => {
            enforceMobileDrawerStyles();
            if (window.innerWidth >= 1024) {
                setMobileMenu(false);
            }

            if (!isDesktop()) {
                closeAll();
            }
        });
    })();
    </script>
@endonce
