<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $layoutSchool = auth()->check()
            ? (auth()->user()->school ?? \App\Support\SchoolContext::current(request()))
            : \App\Support\SchoolContext::current(request());
        $layoutFavicon = data_get($layoutSchool?->settings, 'branding.favicon');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EMS') - {{ config('app.name') }}</title>
    @if($layoutFavicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ltrim($layoutFavicon, '/')) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ui-bg: #f3f7fb;
            --ui-bg-accent: #eaf4ff;
            --ui-surface: #ffffff;
            --ui-border: #dbe5f0;
            --ui-ink: #0f172a;
            --ui-muted: #475569;
            --ui-brand: #0f766e;
            --ui-brand-soft: #dff7f4;
            --ui-focus: rgba(15, 118, 110, 0.28);
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
                scroll-behavior: auto !important;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ui-ink);
            background:
                radial-gradient(circle at 0% 0%, rgba(58, 141, 222, 0.12), transparent 32%),
                radial-gradient(circle at 100% 100%, rgba(15, 118, 110, 0.10), transparent 30%),
                linear-gradient(180deg, var(--ui-bg) 0%, #f8fbff 100%);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        h1,
        h2,
        h3,
        h4,
        .font-display {
            font-family: 'Sora', 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.01em;
        }

        ::selection {
            background: rgba(15, 118, 110, 0.2);
            color: var(--ui-ink);
        }

        a,
        button,
        input,
        select,
        textarea {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, transform 0.2s ease, color 0.2s ease;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--ui-focus);
        }

        input,
        select,
        textarea {
            border-color: #cdd9e7;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94a3b8;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead th {
            position: relative;
            background: #f7fbff;
            color: #475569;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        tbody td {
            vertical-align: middle;
        }

        .app-content-shell > * {
            animation: app-fade-up 0.28s ease both;
        }

        @keyframes app-fade-up {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fx-lift-card {
            from {
                transform: translateY(16px);
            }
            to {
                transform: translateY(-8px);
            }
        }

        @keyframes fx-menu-hover-blink {
            0%, 49% {
                box-shadow: none;
            }
            50%, 100% {
                box-shadow: 0 0 0 1px rgba(223, 231, 83, 0.85), 0 0 16px rgba(223, 231, 83, 0.42);
            }
        }

        .fx-menu-blink:hover,
        .fx-menu-blink:focus-visible {
            animation: fx-menu-hover-blink 0.14s steps(2, end) infinite;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F4F6FB] text-slate-900 antialiased">
    @php
        $sidebarOpen = false;
    @endphp
    <div class="min-h-screen">
        @auth
            @php
                $singleSchoolMode = \App\Support\SchoolContext::isSingleSchoolMode();
                $currentUser = auth()->user();
                $actsAsSchoolAdmin = $currentUser->isSuperAdmin() || $currentUser->isSchoolAdmin();
                $schoolBrandName = $currentUser->school?->name ?? ($layoutSchool?->name ?? 'ChrizFasa Academy');
                $schoolLogo = $currentUser->school?->logo ?? $layoutSchool?->logo;
                $schoolInitials = \Illuminate\Support\Str::upper(
                    collect(preg_split('/\s+/', trim($schoolBrandName)) ?: [])
                        ->filter()
                        ->take(2)
                        ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
                        ->implode('')
                ) ?: 'CA';
                $sidebarDashboardRoute = $currentUser->isTeacher()
                    ? route('teacher.dashboard')
                    : ($currentUser->isStudent()
                        ? route('student.dashboard')
                        : ($currentUser->isParent()
                            ? route('parent.dashboard')
                            : ($currentUser->isStaff() ? route('staff.dashboard') : route('dashboard'))));

                $navSections = [];

                if ($currentUser->isSuperAdmin() && !$singleSchoolMode) {
                    $navSections[] = [
                        'label' => 'SaaS Control',
                        'items' => [
                            ['label' => 'Dashboard', 'route' => $sidebarDashboardRoute, 'pattern' => ['multi-school.index'], 'icon' => 'home'],
                            ['label' => 'Multi-School', 'route' => route('multi-school.index'), 'pattern' => ['multi-school.index'], 'icon' => 'building'],
                            ['label' => 'Domain Mapping', 'route' => route('multi-school.domains'), 'pattern' => ['multi-school.domains*'], 'icon' => 'globe'],
                        ],
                    ];
                } else {
                    $navSections[] = [
                        'label' => 'Overview',
                        'items' => [
                            ['label' => 'Dashboard', 'route' => $sidebarDashboardRoute, 'pattern' => ['dashboard', 'teacher.dashboard', 'staff.dashboard', 'student.dashboard', 'parent.dashboard'], 'icon' => 'home'],
                        ],
                    ];

                    if ($currentUser->isStudent()) {
                        $navSections[] = [
                            'label' => 'My Academics',
                            'items' => [
                                ['label' => 'My Results',    'route' => route('portal.results.center'),    'pattern' => ['portal.results.center', 'portal.results.sheet.pdf'], 'icon' => 'chart'],
                                ['label' => 'Result Feedback', 'route' => route('portal.results.feedback.index'), 'pattern' => ['portal.results.feedback.*'], 'icon' => 'inbox'],
                                ['label' => 'My Timetable',  'route' => route('portal.timetable'),  'pattern' => ['portal.timetable'], 'icon' => 'clock'],
                                ['label' => 'My Attendance', 'route' => route('portal.attendance'), 'pattern' => ['portal.attendance'], 'icon' => 'checklist'],
                                ['label' => 'My Assignments', 'route' => route('portal.assignments'), 'pattern' => ['portal.assignments*'], 'icon' => 'book'],
                            ],
                        ];
                        $navSections[] = [
                            'label' => 'Finance',
                            'items' => [
                                ['label' => 'My Payments', 'route' => route('portal.payments.index'), 'pattern' => ['portal.payments.*'], 'icon' => 'credit-card'],
                                ['label' => 'My Invoices', 'route' => route('portal.invoices.index'), 'pattern' => ['portal.invoices.*'], 'icon' => 'receipt'],
                            ],
                        ];
                        $navSections[] = [
                            'label' => 'Communication',
                            'items' => [
                                ['label' => 'My Inbox',           'route' => route('portal.messages.index'),                      'pattern' => ['portal.messages.*'], 'icon' => 'inbox'],
                                ['label' => 'Submit Testimonial', 'route' => route('student.dashboard') . '#student-testimonial-form', 'pattern' => [], 'icon' => 'quote'],
                            ],
                        ];
                    }

                    if ($currentUser->isParent()) {
                        $navSections[] = [
                            'label' => "My Children",
                            'items' => [
                                ['label' => 'Academic Overview',  'route' => route('parent.academic-overview'), 'pattern' => ['parent.academic-overview'], 'icon' => 'chart'],
                                ['label' => 'Results & Grades',   'route' => route('parent.results-grades'),    'pattern' => ['parent.results-grades'], 'icon' => 'book'],
                                ['label' => 'Fees Summary',       'route' => route('parent.fees-summary'),      'pattern' => ['parent.fees-summary'], 'icon' => 'wallet'],
                                ['label' => 'My Inbox',           'route' => route('portal.messages.index'),                    'pattern' => ['portal.messages.*'], 'icon' => 'inbox'],
                            ],
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin', 'principal', 'vice_principal', 'teacher']) || $actsAsSchoolAdmin) {
                        $academicItems = [
                            ['label' => 'Sessions & Terms', 'route' => route('academic.sessions.index'), 'pattern' => ['academic.sessions.*'], 'icon' => 'calendar'],
                            ['label' => 'Classes', 'route' => route('academic.classes.index'), 'pattern' => ['academic.classes.*'], 'icon' => 'layers'],
                            ['label' => 'Subjects', 'route' => route('academic.subjects.index'), 'pattern' => ['academic.subjects.*'], 'icon' => 'book'],
                            ['label' => 'Students', 'route' => route('academic.students.index'), 'pattern' => ['academic.students.*'], 'icon' => 'users'],
                            ['label' => 'Attendance', 'route' => route('academic.attendance.index'), 'pattern' => ['academic.attendance.*'], 'icon' => 'checklist'],
                            ['label' => 'Timetable', 'route' => route('academic.timetable.index'), 'pattern' => ['academic.timetable.*'], 'icon' => 'clock'],
                            ['label' => 'Assignments', 'route' => route('academic.assignments.index'), 'pattern' => ['academic.assignments.*'], 'icon' => 'book'],
                        ];

                        if (in_array($currentUser->role->value, ['school_admin', 'principal', 'vice_principal', 'super_admin'], true) || $actsAsSchoolAdmin) {
                            $academicItems[] = [
                                'label' => 'Teacher Assignment',
                                'route' => route('academic.teaching-assignments.index'),
                                'pattern' => ['academic.teaching-assignments.*'],
                                'icon' => 'briefcase',
                            ];
                        }

                        $navSections[] = [
                            'label' => 'Academic',
                            'items' => $academicItems,
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin', 'principal']) || $actsAsSchoolAdmin) {
                        $navSections[] = [
                            'label' => 'Admissions',
                            'items' => [
                                ['label' => 'Applications', 'route' => route('admission.index'), 'pattern' => ['admission.*'], 'icon' => 'inbox'],
                            ],
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin', 'principal', 'vice_principal', 'teacher']) || $actsAsSchoolAdmin) {
                        $canRunOfficialResultImport = in_array($currentUser->role->value, ['school_admin', 'principal', 'vice_principal'], true) || $actsAsSchoolAdmin;
                        $assessmentItems = [
                            ['label' => 'Result Submissions', 'route' => route('examination.result-submissions.index'), 'pattern' => ['examination.result-submissions.*'], 'icon' => 'inbox'],
                            ['label' => 'View Student Result', 'route' => route('examination.results.index'), 'pattern' => ['examination.results.enter-scores', 'examination.results.store-scores', 'examination.results.index', 'examination.results.update', 'examination.results.destroy'], 'icon' => 'chart'],
                            ['label' => 'Update Student Result', 'route' => route('examination.results.enter-scores'), 'pattern' => ['examination.results.enter-scores', 'examination.results.store-scores', 'examination.results.index', 'examination.results.update', 'examination.results.destroy'], 'icon' => 'edit'],
                            ['label' => 'Comment', 'route' => route('examination.result-comments.index'), 'pattern' => ['examination.result-comments.*'], 'icon' => 'edit'],
                            ['label' => 'Result Feedback', 'route' => route('examination.result-feedback.index'), 'pattern' => ['examination.result-feedback.*'], 'icon' => 'inbox'],
                        ];

                        if ($canRunOfficialResultImport) {
                            array_splice($assessmentItems, 1, 0, [[
                                'label' => 'Official Result Import',
                                'route' => route('examination.result-sheets.import'),
                                'pattern' => [
                                    'examination.result-sheets.import',
                                    'examination.result-sheets.preview',
                                    'examination.result-sheets.preview.show',
                                    'examination.result-sheets.commit',
                                    'examination.result-sheets.template',
                                    'examination.result-sheets.history',
                                    'examination.result-sheets.errors',
                                    'examination.result-sheets.publishing',
                                    'examination.result-sheets.publish',
                                    'examination.result-sheets.unpublish',
                                    'examination.result-sheets.class-sheet',
                                    'examination.result-sheets.student',
                                    'examination.result-sheets.student.pdf',
                                    'examination.result-sheets.bulk-print',
                                ],
                                'icon' => 'inbox',
                            ]]);
                        }

                        $navSections[] = [
                            'label' => 'Assessment',
                            'items' => $assessmentItems,
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin', 'accountant']) || $actsAsSchoolAdmin) {
                        $navSections[] = [
                            'label' => 'Finance',
                            'items' => [
                                ['label' => 'Fee Structures', 'route' => route('financial.fees.index'), 'pattern' => ['financial.fees.*'], 'icon' => 'wallet'],
                                ['label' => 'Approve Payment', 'route' => route('financial.payments.index', ['status' => 'pending']), 'pattern' => [], 'icon' => 'checklist'],
                                ['label' => 'Invoices', 'route' => route('financial.invoices.index'), 'pattern' => ['financial.invoices.*'], 'icon' => 'receipt'],
                                ['label' => 'Payments', 'route' => route('financial.payments.index'), 'pattern' => ['financial.payments.*'], 'icon' => 'credit-card'],
                                ['label' => 'Bank Accounts', 'route' => route('financial.bank-accounts.index'), 'pattern' => ['financial.bank-accounts.*'], 'icon' => 'wallet'],
                                ['label' => 'Payment Methods', 'route' => route('financial.payment-methods.index'), 'pattern' => ['financial.payment-methods.*'], 'icon' => 'settings'],
                                ['label' => 'Assign Signatures', 'route' => route('financial.bursary-signatures.index'), 'pattern' => ['financial.bursary-signatures.*'], 'icon' => 'edit'],
                            ],
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin', 'principal']) || $actsAsSchoolAdmin) {
                        $navSections[] = [
                            'label' => 'Management',
                            'items' => [
                                ['label' => 'Staff', 'route' => route('staff.index'), 'pattern' => ['staff.*'], 'icon' => 'briefcase'],
                                ['label' => 'Announcements', 'route' => route('announcements.index'), 'pattern' => ['announcements.*'], 'icon' => 'megaphone'],
                                ['label' => 'Messages', 'route' => route('messages.index'), 'pattern' => ['messages.*'], 'icon' => 'inbox'],
                                ['label' => 'Library', 'route' => route('library.index'), 'pattern' => ['library.*'], 'icon' => 'library'],
                                ['label' => 'Hostel', 'route' => route('hostel.index'), 'pattern' => ['hostel.*'], 'icon' => 'home-2'],
                                ['label' => 'Health', 'route' => route('health.index'), 'pattern' => ['health.*'], 'icon' => 'shield'],
                                ['label' => 'Assets', 'route' => route('assets.index'), 'pattern' => ['assets.*'], 'icon' => 'box'],
                            ],
                        ];
                    }

                    if (in_array($currentUser->role->value, ['school_admin']) || $actsAsSchoolAdmin) {
                        $settingsLinks = [
                            ['label' => 'Site Settings', 'route' => route('settings.page', ['page' => 'site-settings']), 'page' => 'site-settings', 'icon' => 'building'],
                            ['label' => 'Hero Header', 'route' => route('settings.page', ['page' => 'hero-header']), 'page' => 'hero-header', 'icon' => 'image'],
                            ['label' => 'Site Theme', 'route' => route('settings.page', ['page' => 'site-theme']), 'page' => 'site-theme', 'icon' => 'palette'],
                            ['label' => 'Programs', 'route' => route('settings.page', ['page' => 'programs']), 'page' => 'programs', 'icon' => 'book'],
                            ['label' => 'Admissions', 'route' => route('settings.page', ['page' => 'admissions']), 'page' => 'admissions', 'icon' => 'inbox'],
                            ['label' => 'Academic Excellence', 'route' => route('settings.page', ['page' => 'academics']), 'page' => 'academics', 'icon' => 'analytics'],
                            ['label' => 'Facilities', 'route' => route('settings.page', ['page' => 'facilities']), 'page' => 'facilities', 'icon' => 'layers'],
                            ['label' => 'About Us', 'route' => route('settings.page', ['page' => 'about-us']), 'page' => 'about-us', 'icon' => 'users'],
                            ['label' => 'Student Life', 'route' => route('settings.page', ['page' => 'student-life']), 'page' => 'student-life', 'icon' => 'calendar'],
                            ['label' => 'Parents', 'route' => route('settings.page', ['page' => 'parents']), 'page' => 'parents', 'icon' => 'users'],
                            ['label' => 'Contact Us', 'route' => route('settings.page', ['page' => 'contact-us']), 'page' => 'contact-us', 'icon' => 'megaphone'],
                            ['label' => 'Testimonials', 'route' => route('settings.page', ['page' => 'testimonials']), 'page' => 'testimonials', 'icon' => 'quote'],
                            ['label' => 'FAQs', 'route' => route('settings.page', ['page' => 'faqs']), 'page' => 'faqs', 'icon' => 'checklist'],
                            ['label' => 'Footer', 'route' => route('settings.page', ['page' => 'footer']), 'page' => 'footer', 'icon' => 'home-2'],
                            ['label' => 'System Preferences', 'route' => route('settings.page', ['page' => 'system-preferences']), 'page' => 'system-preferences', 'icon' => 'settings'],
                        ];

                        $navSections[] = [
                            'label' => 'Reports & System',
                            'items' => [
                                ['label' => 'Financial Reports', 'route' => route('reports.financial'), 'pattern' => ['reports.financial'], 'icon' => 'report'],
                                ['label' => 'Academic Reports', 'route' => route('reports.academic'), 'pattern' => ['reports.academic', 'reports.attendance'], 'icon' => 'analytics'],
                                ['label' => 'Audit Logs', 'route' => route('system.audit-logs.index'), 'pattern' => ['system.audit-logs.*'], 'icon' => 'shield'],
                                ['label' => 'Hero Slides', 'route' => route('system.hero-slides.index'), 'pattern' => ['system.hero-slides.*'], 'icon' => 'image'],
                                ['label' => 'Testimonials', 'route' => route('system.testimonials.index'), 'pattern' => ['system.testimonials.*'], 'icon' => 'quote'],
                                ['label' => 'Settings', 'route' => route('settings.index'), 'pattern' => ['settings.*'], 'icon' => 'settings', 'children' => $settingsLinks],
                            ],
                        ];
                    }
                }

                $renderNavIcon = function (string $icon): string {
                    return match ($icon) {
                        'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9.75V20a.75.75 0 0 0 .75.75h4.5v-5.25c0-.414.336-.75.75-.75h1.5c.414 0 .75.336.75.75v5.25H18a.75.75 0 0 0 .75-.75V9.75"/></svg>',
                        'building' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 21V6.75A.75.75 0 0 1 6 6h4.5a.75.75 0 0 1 .75.75V21"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21V3.75A.75.75 0 0 1 14.25 3h3.75a.75.75 0 0 1 .75.75V21"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 9h.008v.008H7.5V9Zm0 3h.008v.008H7.5V12Zm0 3h.008v.008H7.5V15Zm8.25-6h.008v.008h-.008V9Zm0 3h.008v.008h-.008V12Zm0 3h.008v.008h-.008V15Z"/></svg>',
                        'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="12" cy="12" r="8.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5M12 3.75c2.35 2.26 3.75 5.15 3.75 8.25S14.35 18 12 20.25c-2.35-2.25-3.75-5.15-3.75-8.25S9.65 6.01 12 3.75Z"/></svg>',
                        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="3.75" y="5.25" width="16.5" height="15" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75v3M7.5 3.75v3M3.75 9h16.5"/></svg>',
                        'layers' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.25 4.5L12 12 3.75 7.5 12 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 12 8.25 4.5 8.25-4.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 16.5 8.25 4.5 8.25-4.5"/></svg>',
                        'book' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h11.25v16.5H6.75A2.25 2.25 0 0 0 4.5 21V5.25Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 19.5V3"/></svg>',
                        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5a4.5 4.5 0 1 0-9 0"/><circle cx="11.25" cy="9" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5a3.75 3.75 0 0 0-3-3.675"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 5.625a3 3 0 1 1 0 5.75"/></svg>',
                        'checklist' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h10.5M9 12h10.5M9 17.25h10.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 6.75 1.5 1.5 3-3M4.5 12l1.5 1.5 3-3M4.5 17.25l1.5 1.5 3-3"/></svg>',
                        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="12" cy="12" r="8.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v4.5l3 1.5"/></svg>',
                        'inbox' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25 5.4 5.325A1.5 1.5 0 0 1 6.705 4.5h10.59A1.5 1.5 0 0 1 18.6 5.325l1.65 2.925"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25h16.5V18a1.5 1.5 0 0 1-1.5 1.5h-3.621a1.5 1.5 0 0 1-1.342-.829l-.574-1.147a1.5 1.5 0 0 0-1.342-.829h-.742a1.5 1.5 0 0 0-1.342.829l-.574 1.147A1.5 1.5 0 0 1 8.871 19.5H5.25A1.5 1.5 0 0 1 3.75 18V8.25Z"/></svg>',
                        'edit' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.25 19.04 4.5 19.5l.46-3.75L16.862 4.487Z"/></svg>',
                        'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 16.5v-4.5M12 16.5V8.25M16.5 16.5v-6.75"/></svg>',
                        'wallet' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h12A2.25 2.25 0 0 1 20.25 7.5v9A2.25 2.25 0 0 1 18 18.75H6A2.25 2.25 0 0 1 3.75 16.5v-9Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12h3.75v3H16.5a1.5 1.5 0 1 1 0-3Z"/></svg>',
                        'receipt' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5L6 18.75V3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25h7.5M8.25 12h7.5"/></svg>',
                        'credit-card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="3.75" y="5.25" width="16.5" height="13.5" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5"/></svg>',
                        'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6V4.875A1.125 1.125 0 0 1 10.125 3.75h3.75A1.125 1.125 0 0 1 15 4.875V6"/><rect x="3.75" y="6" width="16.5" height="12.75" rx="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5"/></svg>',
                        'megaphone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 11.25v1.5A2.25 2.25 0 0 0 6 15h1.5l2.25 3.75h1.5L10.5 15h1.125a2.25 2.25 0 0 0 1.006-.237L18.75 11.7V4.95l-6.119 3.06a2.25 2.25 0 0 0-1.006-.237H6A2.25 2.25 0 0 0 3.75 11.25Z"/></svg>',
                        'library' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5h3v15h-3zM10.5 4.5h3.75v15H10.5zM16.5 4.5h2.25v15H16.5z"/></svg>',
                        'home-2' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 4.5l7.5 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 9.75V19.5h10.5V9.75"/></svg>',
                        'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.181c0 4.152 2.69 7.826 6.75 9.069 4.06-1.243 6.75-4.917 6.75-9.07V6L12 3.75Z"/></svg>',
                        'box' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3.75 7.5 4.125v8.25L12 20.25 4.5 16.125v-8.25L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12 4.5 7.875M12 12l7.5-4.125M12 12v8.25"/></svg>',
                        'report' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h7.5l3 3V20.25H6.75a1.5 1.5 0 0 1-1.5-1.5V5.25a1.5 1.5 0 0 1 1.5-1.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 3.75v3h3"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 11.25h7.5M8.25 15h5.25"/></svg>',
                        'analytics' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 16.5 10.5 12l3 2.25 3.75-5.25"/><circle cx="6.75" cy="16.5" r=".75" fill="currentColor" stroke="none"/><circle cx="10.5" cy="12" r=".75" fill="currentColor" stroke="none"/><circle cx="13.5" cy="14.25" r=".75" fill="currentColor" stroke="none"/><circle cx="17.25" cy="9" r=".75" fill="currentColor" stroke="none"/></svg>',
                        'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="3.75" y="5.25" width="16.5" height="13.5" rx="2.25"/><circle cx="8.25" cy="10.125" r="1.125"/><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 15-4.5-4.5L6 20.25"/></svg>',
                        'quote' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 8.25a3.75 3.75 0 0 0-3.75 3.75v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3A3.75 3.75 0 0 0 8.25 8.25h1.5Zm8.25 0a3.75 3.75 0 0 0-3.75 3.75v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3A3.75 3.75 0 0 0 16.5 8.25H18Z"/></svg>',
                        'palette' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75c-4.556 0-8.25 3.19-8.25 7.125 0 2.53 1.531 4.752 3.833 6.014.57.313.917.917.917 1.567 0 .852.691 1.544 1.543 1.544H12c4.556 0 8.25-3.19 8.25-7.125S16.556 3.75 12 3.75Z"/><circle cx="7.5" cy="11.25" r=".75" fill="currentColor" stroke="none"/><circle cx="10.5" cy="8.25" r=".75" fill="currentColor" stroke="none"/><circle cx="14.25" cy="8.25" r=".75" fill="currentColor" stroke="none"/><circle cx="16.5" cy="11.25" r=".75" fill="currentColor" stroke="none"/></svg>',
                        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1.724 1.724 0 0 1 3.35 0 1.724 1.724 0 0 0 2.573 1.066 1.724 1.724 0 0 1 2.351.64 1.724 1.724 0 0 0 2.573 1.066 1.724 1.724 0 0 1 1.287 2.886 1.724 1.724 0 0 0-.64 2.35 1.724 1.724 0 0 1 0 1.98 1.724 1.724 0 0 0 .64 2.35 1.724 1.724 0 0 1-1.287 2.887 1.724 1.724 0 0 0-2.573 1.065 1.724 1.724 0 0 1-2.35.641 1.724 1.724 0 0 0-2.574 1.065 1.724 1.724 0 0 1-3.35 0 1.724 1.724 0 0 0-2.573-1.065 1.724 1.724 0 0 1-2.351-.64 1.724 1.724 0 0 0-2.573-1.066 1.724 1.724 0 0 1-1.287-2.886 1.724 1.724 0 0 0 .64-2.351 1.724 1.724 0 0 1 0-1.98 1.724 1.724 0 0 0-.64-2.35A1.724 1.724 0 0 1 2.93 7.09a1.724 1.724 0 0 0 2.573-1.066 1.724 1.724 0 0 1 2.35-.64 1.724 1.724 0 0 0 2.573-1.067Z"/><circle cx="12" cy="12" r="3"/></svg>',
                        default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="12" cy="12" r="8.25"/></svg>',
                    };
                };
            @endphp

            <div class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden" id="mobile-admin-overlay" hidden></div>

            <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[290px] -translate-x-full flex-col overflow-hidden border-r border-[#43316F] bg-[#2D1D5C] text-white shadow-2xl transition-transform duration-300 lg:translate-x-0">
                <div class="border-b border-white/10 bg-gradient-to-r from-[#24174A] via-[#2D1D5C] to-[#3A2872] px-6 pb-5 pt-6">
                    <div class="relative flex items-center justify-center">
                        <div class="flex items-center justify-center">
                            @if($schoolLogo)
                                <img src="{{ asset('storage/' . ltrim($schoolLogo, '/')) }}" alt="{{ $schoolBrandName }} logo" class="h-14 w-14 rounded-2xl border border-white/15 bg-white object-cover shadow-lg shadow-black/15">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-xl font-extrabold tracking-[0.18em] text-[#DFE753] shadow-lg shadow-black/15">
                                    {{ $schoolInitials }}
                                </div>
                            @endif
                        </div>
                        <button type="button" id="close-admin-sidebar" class="absolute right-0 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white/80 transition hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C] lg:hidden">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-5">
                    <nav class="space-y-6">
                        @foreach($navSections as $section)
                            <div>
                                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.32em] text-white/45">{{ $section['label'] }}</p>
                                <div class="mt-3 space-y-1.5">
                                    @foreach($section['items'] as $item)
                                        @php
                                            $isActive = collect($item['pattern'])->contains(fn ($pattern) => request()->routeIs($pattern));
                                        @endphp
                                        @if(!empty($item['children']))
                                            <details class="group rounded-2xl border {{ $isActive ? 'border-[#DFE753]/60 bg-white/6' : 'border-white/8 bg-white/[0.03]' }}" {{ $isActive ? 'open' : '' }}>
                                                <summary class="fx-menu-blink flex cursor-pointer list-none items-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-semibold transition duration-200 {{ $isActive ? 'text-[#DFE753]' : 'text-slate-100/92 hover:bg-white/6 hover:text-white' }}">
                                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isActive ? 'bg-[#DFE753]/12 text-[#DFE753]' : 'bg-white/8 text-[#DFE753] group-hover:bg-white/12' }}">
                                                        {!! $renderNavIcon($item['icon']) !!}
                                                    </span>
                                                    <span class="flex-1 leading-relaxed">{{ $item['label'] }}</span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 text-current transition duration-200 group-open:rotate-180"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                                </summary>
                                                <div class="space-y-1 px-3 pb-3">
                                                    @foreach($item['children'] as $child)
                                                        @php
                                                            $childActive = request()->routeIs('settings.page') && (($child['page'] ?? null) === request()->route('page'));
                                                        @endphp
                                                        <a href="{{ $child['route'] }}" class="fx-menu-blink group flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition duration-200 {{ $childActive ? 'border-[#DFE753]/70 bg-[#DFE753] text-[#2D1D5C]' : 'border-transparent text-slate-200/88 hover:border-white/8 hover:bg-white/8 hover:text-white' }}">
                                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/6 text-[#DFE753]">
                                                                {!! $renderNavIcon($child['icon']) !!}
                                                            </span>
                                                            <span class="leading-relaxed">{{ $child['label'] }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </details>
                                        @else
                                            <a href="{{ $item['route'] }}" class="fx-menu-blink group flex items-center gap-3 rounded-2xl border px-3.5 py-3 text-sm font-medium transition duration-200 {{ $isActive ? 'border-[#DFE753] bg-[#DFE753] text-[#2D1D5C] shadow-lg shadow-black/10' : 'border-transparent text-slate-100/92 hover:border-white/10 hover:bg-white/8 hover:text-white' }}">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isActive ? 'bg-[#2D1D5C]/10 text-[#2D1D5C]' : 'bg-white/8 text-[#DFE753] group-hover:bg-white/12' }}">
                                                    {!! $renderNavIcon($item['icon']) !!}
                                                </span>
                                                <span class="flex-1 leading-relaxed">{{ $item['label'] }}</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 opacity-60 {{ $isActive ? 'text-[#2D1D5C]' : 'text-white/55 group-hover:text-[#DFE753]' }}"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                </div>

                <div class="border-t border-white/10 px-4 py-4 space-y-2">
                    @php
                        $profileRoute = ($currentUser->isStudent() || $currentUser->isParent())
                            ? route('portal.profile.show')
                            : route('profile.show');
                        $profileActive = request()->routeIs('profile.*') || request()->routeIs('portal.profile.*');
                        $logoutRoute   = ($currentUser->isStudent() || $currentUser->isParent())
                            ? route('portal.logout')
                            : ($currentUser->isStaff() ? route('staff.logout') : route('logout'));
                    @endphp
                    <a href="{{ $profileRoute }}" class="flex items-center gap-3 rounded-2xl border px-3.5 py-3 text-sm font-medium transition duration-200 {{ $profileActive ? 'border-[#DFE753] bg-[#DFE753] text-[#2D1D5C]' : 'border-white/10 bg-white/5 text-white hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C]' }}">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $profileActive ? 'bg-[#2D1D5C]/10' : 'bg-white/8' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="12" cy="8" r="3.75"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 20.25a8.25 8.25 0 0 1 16.5 0"/></svg>
                        </span>
                        <span class="flex-1">My Profile</span>
                    </a>
                    <form action="{{ $logoutRoute }}" method="POST">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:border-red-400 hover:bg-red-500 hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9.75"/><path stroke-linecap="round" stroke-linejoin="round" d="m15 9 3 3-3 3"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </aside>

            <main class="min-h-screen flex flex-col transition-all duration-300 lg:pl-[290px]">
                <header class="sticky top-0 z-30 border-b border-[#2D1D5C]/15 bg-gradient-to-r from-white/95 via-[#f5f3ff]/95 to-[#eef6ff]/95 shadow-[0_10px_30px_-20px_rgba(45,29,92,0.55)] backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" id="open-admin-sidebar" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-[#2D1D5C]/15 bg-white/90 text-[#2D1D5C] shadow-sm transition hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C] lg:hidden">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15M4.5 12h15M4.5 16.5h15"/></svg>
                            </button>
                            <div class="rounded-2xl border border-white/60 bg-white/55 px-4 py-2 shadow-sm ring-1 ring-[#2D1D5C]/5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#2D1D5C]/70">Control Panel</p>
                                <h2 class="mt-0.5 bg-gradient-to-r from-[#1f2a44] via-[#2D1D5C] to-[#355AA0] bg-clip-text text-xl font-extrabold tracking-tight text-transparent">@yield('header', 'Dashboard')</h2>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4">
                            <a href="{{ ($currentUser->isStudent() || $currentUser->isParent()) ? route('portal.profile.show') : route('profile.show') }}" class="hidden items-center gap-2.5 rounded-2xl border border-[#2D1D5C]/10 bg-white/85 px-4 py-2 shadow-[0_8px_22px_-15px_rgba(45,29,92,0.5)] ring-1 ring-white/80 transition hover:-translate-y-0.5 hover:border-[#2D1D5C]/25 hover:shadow-[0_14px_28px_-16px_rgba(45,29,92,0.6)] md:flex">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#2D1D5C] via-[#355AA0] to-[#4F46E5] text-sm font-bold text-white shadow-inner shadow-white/10">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($currentUser->first_name, 0, 1) . \Illuminate\Support\Str::substr($currentUser->last_name, 0, 1)) }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $currentUser->full_name }}</p>
                                </div>
                            </a>
                            @php
                                $bellCount = 0;
                                $bellRoute = route('notifications.index');
                                $canManageResultFeedbackInbox = ($actsAsSchoolAdmin || $currentUser->isTeacher())
                                    || in_array((string) ($currentUser->role?->value ?? ''), ['principal', 'vice_principal'], true);
                                if ($currentUser->isStudent()) {
                                    $unreadMessages = $currentUser->unreadMessagesCount();
                                    $unreadFeedbackResponses = $currentUser->unreadResultFeedbackResponsesCount();
                                    $unreadAssignmentReviews = $currentUser->unreadNotifications()
                                        ->where('type', \App\Notifications\StudentAssignmentReviewedNotification::class)
                                        ->count();

                                    $bellCount = $unreadMessages + $unreadFeedbackResponses + $unreadAssignmentReviews;
                                } elseif ($currentUser->isParent()) {
                                    $bellCount = $currentUser->unreadMessagesCount();
                                } else {
                                    $unreadReplies = $currentUser->unreadAdminRepliesCount();
                                    $unreadAssignmentSubmissions = $currentUser->unreadNotifications()
                                        ->where('type', \App\Notifications\StudentAssignmentSubmittedNotification::class)
                                        ->count();

                                    $bellCount = $unreadReplies + $unreadAssignmentSubmissions;

                                    if ($canManageResultFeedbackInbox) {
                                        $bellCount += $currentUser->openResultFeedbackCount();
                                    }
                                }
                            @endphp
                            <a href="{{ $bellRoute }}" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-[#2D1D5C]/10 bg-white/90 text-[#2D1D5C] shadow-[0_8px_20px_-14px_rgba(45,29,92,0.5)] ring-1 ring-white/70 transition hover:-translate-y-0.5 hover:border-[#2D1D5C]/30 hover:bg-white hover:shadow-[0_14px_26px_-14px_rgba(45,29,92,0.55)]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 1 0-12 0v.05c0 .232 0 .465-.001.697a8.967 8.967 0 0 1-2.311 6.025 23.848 23.848 0 0 0 5.454 1.31m5.715 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                                @if($bellCount > 0)
                                    <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-r from-rose-500 to-pink-500 text-[9px] font-bold text-white ring-2 ring-white">
                                        {{ $bellCount > 99 ? '99+' : $bellCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </header>
        @endauth

        @php
            $mainContentClass = auth()->check() ? 'px-4 py-6 sm:px-6 lg:px-8' : '';
        @endphp

        <div class="{{ $mainContentClass }} app-content-shell flex-1">
            @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any() && !Route::is('login'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </div>

        @auth
            <footer class="mt-auto" style="background-color: #2D1D5C;">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="border-t pt-5 pb-5" style="border-top-color: rgba(223,231,83,0.35);">
                        <p class="text-xs" style="color: rgba(255,255,255,0.55);">&copy; {{ date('Y') }} {{ $schoolBrandName }}. All rights reserved.</p>
                    </div>
                </div>
            </footer>
            </main>
        @endauth
    </div>

    @auth
    <script>
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('mobile-admin-overlay');
            const openButton = document.getElementById('open-admin-sidebar');
            const closeButton = document.getElementById('close-admin-sidebar');

            if (!sidebar || !overlay || !openButton || !closeButton) {
                return;
            }

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.hidden = false;
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.hidden = true;
            };

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    overlay.hidden = true;
                    sidebar.classList.remove('-translate-x-full');
                } else if (!overlay.hidden) {
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        })();
    </script>

    <script>
        (() => {
            const logoutUrl = @json(
                ($currentUser->isStudent() || $currentUser->isParent())
                    ? route('portal.logout')
                    : ($currentUser->isStaff() ? route('staff.logout') : route('logout'))
            );
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (!logoutUrl) {
                return;
            }

            const IDLE_TIMEOUT_MS = 4 * 60 * 1000; // 4 minutes
            let lastActivityAt = Date.now();
            let isMouseInsideWindow = false;
            let hasLoggedOut = false;

            const markActivity = () => {
                lastActivityAt = Date.now();
            };

            const markMouseInside = () => {
                isMouseInsideWindow = true;
                markActivity();
            };

            const markMouseOutside = () => {
                isMouseInsideWindow = false;
            };

            const submitLogout = () => {
                if (hasLoggedOut) {
                    return;
                }

                hasLoggedOut = true;

                if (!csrfToken) {
                    window.location.reload();
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = logoutUrl;
                form.style.display = 'none';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;
                form.appendChild(tokenInput);

                document.body.appendChild(form);
                form.submit();
            };

            const checkIdleTimeout = () => {
                const hasHoveredElement = (() => {
                    try {
                        return document.querySelectorAll(':hover').length > 0;
                    } catch (_) {
                        return false;
                    }
                })();

                if (hasLoggedOut || isMouseInsideWindow || hasHoveredElement) {
                    return;
                }

                if (Date.now() - lastActivityAt >= IDLE_TIMEOUT_MS) {
                    submitLogout();
                }
            };

            ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'touchmove', 'pointerdown'].forEach((eventName) => {
                window.addEventListener(eventName, markActivity, { passive: true });
            });

            window.addEventListener('mouseenter', markMouseInside, { passive: true });
            window.addEventListener('mouseover', markMouseInside, { passive: true });
            window.addEventListener('mouseleave', markMouseOutside, { passive: true });
            window.addEventListener('blur', markMouseOutside);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    markActivity();
                }
            });

            window.setInterval(checkIdleTimeout, 1000);
        })();
    </script>
    @endauth

    @stack('scripts')
</body>
</html>


