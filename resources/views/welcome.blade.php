<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $school?->name ?? 'ChrizFasa Academy' }} | {{ trim((string) ($publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php($theme = \App\Support\ThemePalette::fromPublicPage($publicPage))
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
</style>
</head>
@php
    $schoolName = $school?->name ?? 'ChrizFasa Academy';
    $metrics = $publicPage['metrics'] ?? [];
    $whyChooseUs = $publicPage['why_choose_us'] ?? [];
    $whyChooseUsLabel = trim((string) ($publicPage['why_choose_us_label'] ?? 'Why Choose Us'));
    $whyChooseUsIntro = trim((string) ($publicPage['why_choose_us_intro'] ?? ''));
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

    $menuSections = [
        ['label' => ($programsLabel !== '' ? $programsLabel : 'Programs'), 'id' => 'programs', 'link' => '#programs', 'items' => collect($programs)->pluck('title')->filter()->values()->all()],
        ['label' => ($admissionsLabel !== '' ? $admissionsLabel : 'Admissions'), 'id' => 'admissions', 'link' => '#admissions', 'items' => collect($admissions)->pluck('title')->filter()->values()->all()],
        ['label' => ($academicsLabel !== '' ? $academicsLabel : 'Academics'), 'id' => 'academics', 'link' => '#academics', 'items' => collect($academics)->pluck('title')->filter()->values()->all()],
        ['label' => ($facilitiesLabel !== '' ? $facilitiesLabel : 'Facilities'), 'id' => 'facilities', 'link' => '#facilities', 'items' => collect($facilities)->filter()->values()->all()],
        ['label' => ($aboutLabel !== '' ? $aboutLabel : 'About Us'), 'id' => 'about', 'link' => '#about', 'items' => collect($aboutItems)->pluck('title')->filter()->values()->all()],
        ['label' => ($studentLifeLabel !== '' ? $studentLifeLabel : 'Student Life'), 'id' => 'student-life', 'link' => '#student-life', 'items' => collect($studentLifeItems)->pluck('title')->filter()->values()->all()],
        ['label' => ($parentsLabel !== '' ? $parentsLabel : 'Parents'), 'id' => 'parents', 'link' => '#parents', 'items' => collect($parentsItems)->pluck('title')->filter()->values()->all()],
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
@endphp
<body class="text-ink antialiased" style="background-color: {{ $siteBackgroundColor }}; color: {{ $themeBodyColor }}; --submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }}; --theme-heading: {{ $themeHeadingColor }}; --theme-body: {{ $themeBodyColor }}; --theme-surface: {{ $themeSurfaceColor }}; --theme-soft-surface: {{ $themeSoftSurfaceColor }};">
    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute -top-20 -left-28 h-80 w-80 rounded-full bg-brand-100 blur-3xl"></div>
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-72 rounded-full bg-secondary-100 blur-3xl"></div>

        <header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur" style="background-color: {{ $headerBgColor }};">
            <div class="mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-4 px-6 py-3 lg:px-8">
                <a href="{{ route('public.home') }}" class="flex items-center gap-3 transition duration-200 hover:opacity-90">
                    @if($school?->logo)
                        <img src="{{ asset('storage/' . ltrim($school->logo, '/')) }}" alt="{{ $schoolName }} Logo" class="h-10 w-10 rounded-full object-cover border border-slate-200 bg-white">
                    @endif
                    <span class="font-display text-xl font-semibold tracking-tight text-white whitespace-nowrap">{{ $schoolName }}</span>
                </a>
                <nav class="hidden items-center justify-center gap-1 rounded-2xl border border-slate-200/90 bg-white/95 px-2 py-1.5 text-sm font-semibold text-slate-600 shadow-sm xl:flex" style="--submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                    @foreach($menuSections as $section)
                        @php
                            $alignClass = ($loop->last || $loop->iteration >= count($menuSections) - 1) ? 'right-0' : 'left-0';
                        @endphp
                        <div class="relative shrink-0" data-menu="{{ $section['id'] }}">
                            @if(!empty($section['items']))
                                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="submenu-{{ $section['id'] }}" class="theme-nav-link inline-flex cursor-pointer items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    {{ $section['label'] }}
                                </button>
                                <div id="submenu-{{ $section['id'] }}" data-menu-panel class="absolute {{ $alignClass }} top-full z-50 hidden w-[22rem] max-w-[calc(100vw-2rem)] pt-3">
                                    <div
                                        class="theme-submenu-panel rounded-2xl border p-3 shadow-2xl ring-1 ring-white/20 backdrop-blur"
                                        style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};"
                                    >
                                        <p class="theme-submenu-heading px-3 pb-1 text-xs font-bold uppercase tracking-[0.16em]">{{ $section['label'] }}</p>
                                        <a href="{{ $section['link'] ?? ('#' . $section['id']) }}" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">
                                            {{ $section['label'] }} {{ $menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview' }}
                                        </a>
                                        @foreach($section['items'] as $menuItem)
                                            <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)]) }}" class="theme-submenu-link block rounded-lg px-3 py-2 text-sm font-medium transition duration-200">
                                                {{ $menuItem }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $section['link'] ?? ('#' . $section['id']) }}" class="theme-nav-link inline-flex items-center whitespace-nowrap rounded-xl px-3.5 py-2 text-[15px] font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    {{ $section['label'] }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </nav>
                <div class="flex items-center justify-end gap-2 sm:gap-3" style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                    <a href="{{ route('admission.apply') }}" class="theme-header-action-outline hidden items-center whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5 sm:inline-flex">{{ $headerApplyText !== '' ? $headerApplyText : 'Apply' }}</a>
                    <a href="{{ route('login') }}" class="theme-header-action-solid inline-flex items-center whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">{{ $headerPortalLoginText !== '' ? $headerPortalLoginText : 'Portal Login' }}</a>
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
                        <a href="{{ route('login') }}" class="theme-mobile-action-solid inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200">{{ $mobilePortalLoginText !== '' ? $mobilePortalLoginText : 'Portal Login' }}</a>
                    </div>

                    <div class="space-y-2" style="--submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                        @foreach($menuSections as $section)
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                                @if(!empty($section['items']))
                                    <button type="button" data-mobile-submenu-toggle data-target="mobile-submenu-{{ $section['id'] }}" aria-expanded="false" class="theme-nav-link flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-semibold transition duration-200">
                                        <span>{{ $section['label'] }}</span>
                                        <span data-mobile-submenu-indicator class="text-lg font-medium leading-none text-slate-500">+</span>
                                    </button>
                                    <div id="mobile-submenu-{{ $section['id'] }}" data-mobile-submenu-panel class="hidden px-4 pb-4" style="--submenu-primary: {{ $submenuPrimaryColor }}; --submenu-secondary: {{ $submenuSecondaryColor }}; --submenu-hover-text: {{ $submenuHoverTextColor }};">
                                        <div class="space-y-1 border-l border-slate-200 pl-4">
                                            <a href="{{ $section['link'] ?? ('#' . $section['id']) }}" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm font-semibold transition duration-200">{{ $section['label'] }} {{ $menuOverviewSuffix !== '' ? $menuOverviewSuffix : 'Overview' }}</a>
                                            @foreach($section['items'] as $menuItem)
                                                <a href="{{ route('public.submenu', ['section' => $section['id'], 'slug' => \Illuminate\Support\Str::slug($menuItem)]) }}" class="theme-mobile-submenu-link block rounded-lg px-3 py-2 text-sm transition duration-200">{{ $menuItem }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $section['link'] ?? ('#' . $section['id']) }}" class="theme-nav-link block px-4 py-3.5 text-sm font-semibold transition duration-200">{{ $section['label'] }}</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </header>

        <main class="relative z-0">
            <x-hero-slider :school="$school" :public-page="$publicPage" />

            <section class="border-t border-slate-200 bg-white py-14">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700">{{ $whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us' }}</h2>
                    @if($whyChooseUsIntro !== '')
                        <p class="mt-2 max-w-3xl text-sm font-medium text-slate-600">{{ $whyChooseUsIntro }}</p>
                    @endif
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach($whyChooseUsBanners as $item)
                            @php
                                $bannerId = \Illuminate\Support\Str::slug($item['title'] ?: ('why-choose-us-' . $loop->index));
                                $imagePath = $item['image'] ?? '';
                                $hasImage = $imagePath !== '';
                                $imageUrl = $hasImage
                                    ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . ltrim($imagePath, '/')))
                                    : null;
                            @endphp
                            <article id="why-choose-us-{{ $bannerId }}" class="group relative min-h-44 overflow-hidden rounded-xl border border-slate-200 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md">
                                @if($hasImage)
                                    <img src="{{ $imageUrl }}" alt="{{ $item['title'] ?: (($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us') . ' Banner') }}" class="absolute inset-0 h-full w-full object-cover">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-600 to-secondary-500"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/35 to-transparent"></div>
                                <div class="relative flex h-full flex-col justify-end p-4">
                                    <h3 class="text-lg font-extrabold text-white">{{ $item['title'] ?: ($whyChooseUsLabel !== '' ? $whyChooseUsLabel : 'Why Choose Us') }}</h3>
                                    @if(!empty($item['description']))
                                        <p class="mt-1 text-sm font-semibold leading-relaxed text-white/95">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="programs" class="border-t border-slate-200 bg-white py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700">{{ $programsLabel !== '' ? $programsLabel : 'Programs' }}</p>
                        <h2 class="mt-3 font-display text-3xl font-semibold text-slate-900">{{ $publicPage['programs_intro'] ?? 'Learning pathways for every stage.' }}</h2>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-3">
                        @foreach($programs as $item)
                            <article id="programs-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('program-'.$loop->index)) }}" class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-200 hover:bg-white hover:shadow-md">
                                <h3 class="font-display text-2xl font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                                <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $item['description'] ?? '' }}</p>
                            </article>
                        @endforeach
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
                                    {{ $academicsLabel !== '' ? $academicsLabel : 'Academic Excellence' }}
                                </p>
                                <h2 class="mt-2 font-[Georgia,serif] text-3xl font-semibold leading-tight text-slate-900 lg:text-[40px]">
                                    {{ $publicPage['academics_intro'] ?? 'A Structured Learning Culture With Mentorship At The Center.' }}
                                </h2>
                                <p class="mt-3 text-base leading-relaxed text-slate-700">
                                    {{ $academicsSupportText !== '' ? $academicsSupportText : 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.' }}
                                </p>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach($academicHighlights as $item)
                                        <article id="academics-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('academic-highlight-'.$loop->index)) }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                                            <h3 class="text-xl font-bold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                                            <p class="mt-1.5 text-base leading-relaxed text-slate-700">{{ $item['description'] ?? '' }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </div>

                            <div class="lg:col-span-6">
                                <div class="grid h-full grid-cols-2 gap-4">
                                    @for($i = 0; $i < 2; $i++)
                                        @php $visual = $academicsVisuals->get($i); @endphp
                                        <div class="min-h-[240px] lg:min-h-[260px] overflow-hidden rounded-2xl border border-slate-200 bg-slate-900">
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

            <section id="student-life" class="border-t border-slate-200 bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700">{{ $studentLifeLabel !== '' ? $studentLifeLabel : 'Student Life' }}</p>
                        <h2 class="mt-3 font-display text-3xl font-semibold text-slate-900">{{ $publicPage['student_life_intro'] ?? '' }}</h2>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($studentLifeItems as $item)
                            <div id="student-life-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('student-life-'.$loop->index)) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $item['description'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="parents" class="relative w-full border-y border-slate-300 bg-gradient-to-r from-[#DFE753] via-[#f3f4ff] to-[#cfc7e8] py-16">
                <div id="testimonials" class="mx-auto max-w-6xl px-6">
                    <div class="mx-auto max-w-4xl text-center">
                        <h6 class="text-[30px] font-black uppercase tracking-[0.26em] text-[#2D1D5C]">
                            {{ $testimonialsBadgeText !== '' ? $testimonialsBadgeText : 'Testimonials' }}
                        </h6>
                        <h2 class="mt-3 font-display text-4xl font-extrabold text-slate-900">
                            {{ $testimonialsHeading !== '' ? $testimonialsHeading : 'What Parents and Student Say' }}
                        </h2>
                        <p class="mt-4 text-base leading-relaxed text-slate-600">
                            {{ $testimonialsSubheading !== '' ? $testimonialsSubheading : 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.' }}
                        </p>
                    </div>

                    <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        <div class="mb-6 flex items-center justify-between gap-3">
                            <h3 class="text-3xl font-extrabold text-slate-900">
                                {{ $testimonialsSliderTitle !== '' ? $testimonialsSliderTitle : 'Approved Testimonials' }}
                            </h3>
                            @if($testimonials->count() > 1)
                                <div class="flex items-center gap-2">
                                    <button type="button" data-testimonial-prev class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-lg font-bold text-slate-700 transition hover:bg-slate-100" aria-label="Previous testimonial">&lt;</button>
                                    <button type="button" data-testimonial-next class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-lg font-bold text-slate-700 transition hover:bg-slate-100" aria-label="Next testimonial">&gt;</button>
                                </div>
                            @endif
                        </div>

                        @if($testimonials->isEmpty())
                            <div class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center text-base font-medium text-slate-600">
                                {{ $testimonialsEmptyText !== '' ? $testimonialsEmptyText : 'No testimonials have been approved yet. Be the first to share your experience.' }}
                            </div>
                        @else
                            <div data-testimonial-slider class="relative overflow-hidden rounded-2xl border border-slate-200">
                                <div data-testimonial-track class="flex transition-transform duration-500 ease-out">
                                    @foreach($testimonials as $testimonial)
                                        <article class="w-full shrink-0 p-6 sm:p-8">
                                            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                                                <div class="mb-4 inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    <span>{{ max(1, min(5, (int) $testimonial->rating)) }}/5</span>
                                                    <span class="text-slate-400">&middot;</span>
                                                    <span>Approved</span>
                                                </div>
                                                <blockquote class="text-xl font-black leading-relaxed text-[#2D1D5C] sm:text-2xl">
                                                    "{{ $testimonial->message }}"
                                                </blockquote>
                                                <p class="mt-4 text-sm text-slate-600">
                                                    <span class="font-semibold text-slate-800">{{ $testimonial->full_name }}</span>
                                                    @if(!empty($testimonial->role_title))
                                                        <span class="text-slate-400">&middot;</span>
                                                        <span>{{ $testimonial->role_title }}</span>
                                                    @endif
                                                </p>
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
                                            class="h-2.5 w-8 rounded-full {{ $loop->first ? 'bg-[#2D1D5C]' : 'bg-slate-300' }} transition duration-200"
                                            aria-label="Go to testimonial {{ $loop->iteration }}"
                                        ></button>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </section>

            <section id="contact" class="border-t border-slate-200 bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-700">{{ $contactLabel !== '' ? $contactLabel : 'Contact' }}</p>
                        <h2 class="mt-3 font-display text-3xl font-semibold text-slate-900">{{ $publicPage['contact_intro'] ?? '' }}</h2>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($contactItems as $item)
                                <div id="contact-{{ \Illuminate\Support\Str::slug($item['title'] ?? ('contact-'.$loop->index)) }}" class="rounded-xl border border-slate-200 bg-white p-4 transition duration-300 hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-sm">
                                    <h3 class="text-sm font-bold uppercase tracking-wide text-brand-700">{{ $item['title'] ?? '' }}</h3>
                                    <p class="mt-2 text-sm text-slate-600">{{ $item['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 class="font-semibold text-slate-900">{{ $quickContactLabel !== '' ? $quickContactLabel : 'Quick Contact' }}</h3>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold">{{ $contactPhoneLabel !== '' ? $contactPhoneLabel : 'Phone' }}:</span> {{ $school?->phone ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                                <p><span class="font-semibold">{{ $contactWhatsappLabel !== '' ? $contactWhatsappLabel : 'WhatsApp' }}:</span> {{ ($publicPage['whatsapp'] ?? '') ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                                <p><span class="font-semibold">{{ $contactEmailLabel !== '' ? $contactEmailLabel : 'Email' }}:</span> {{ $school?->email ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                                <p><span class="font-semibold">{{ $contactAddressLabel !== '' ? $contactAddressLabel : 'Address' }}:</span> {{ $school?->address ?: ($contactNotProvidedText !== '' ? $contactNotProvidedText : 'Not provided yet') }}</p>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-3">
                                @if(!empty($publicPage['visit_booking_url']))
                                    <a href="{{ $publicPage['visit_booking_url'] }}" target="_blank" rel="noopener" class="theme-cta-solid rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">{{ $visitBookingButtonText !== '' ? $visitBookingButtonText : 'Visit Booking' }}</a>
                                @endif
                                <a href="{{ route('admission.apply') }}" class="theme-cta-outline rounded-full px-5 py-2.5 text-sm font-semibold transition duration-200 hover:-translate-y-0.5">{{ $quickApplyButtonText !== '' ? $quickApplyButtonText : 'Apply Now' }}</a>
                            </div>
                            @if(!empty($publicPage['map_embed_url']))
                                <div class="mt-5 overflow-hidden rounded-xl border border-slate-200">
                                    <iframe src="{{ $publicPage['map_embed_url'] }}" title="{{ $mapEmbedTitleText !== '' ? $mapEmbedTitleText : 'School map' }}" class="h-60 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            @endif
                        </div>
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



