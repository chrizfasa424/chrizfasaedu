<?php

namespace App\Http\Controllers\System;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Support\DomainHelper;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const SETTINGS_PAGE_META = [
        'site-settings' => [
            'title' => 'Site Settings',
            'description' => 'Manage the school identity, contact details, logo, favicon, domain, and shared navigation text.',
            'partial' => 'system.settings.partials.site-settings',
        ],
        'hero-header' => [
            'title' => 'Hero Header',
            'description' => 'Control the hero heading, call-to-action buttons, homepage metrics, and first-impression content.',
            'partial' => 'system.settings.partials.hero-header',
        ],
        'site-theme' => [
            'title' => 'Site Theme',
            'description' => 'Adjust the school brand palette, header color, footer color, and overall visual theme.',
            'partial' => 'system.settings.partials.site-theme',
        ],
        'programs' => [
            'title' => 'Programs',
            'description' => 'Edit the Programs menu label, section intro, and card content shown on the public site.',
            'partial' => 'system.settings.partials.programs',
        ],
        'admissions' => [
            'title' => 'Admissions',
            'description' => 'Update admission wording, steps, labels, and admission-related actions for new families.',
            'partial' => 'system.settings.partials.admissions',
        ],
        'academics' => [
            'title' => 'Academic Excellence',
            'description' => 'Manage the academics headline, supporting content, highlight cards, and academic visual assets.',
            'partial' => 'system.settings.partials.academics',
        ],
        'facilities' => [
            'title' => 'Facilities',
            'description' => 'Control the facilities label, intro text, and facilities list displayed on the website.',
            'partial' => 'system.settings.partials.facilities',
        ],
        'about-us' => [
            'title' => 'About Us',
            'description' => 'Update the About Us section copy, cards, and the supporting Why Choose Us banner content.',
            'partial' => 'system.settings.partials.about-us',
        ],
        'student-life' => [
            'title' => 'Student Life',
            'description' => 'Edit the Student Life label, intro, and supporting student experience content.',
            'partial' => 'system.settings.partials.student-life',
        ],
        'parents' => [
            'title' => 'Parents',
            'description' => 'Manage parent-facing content, portal button text, and homepage parent cards.',
            'partial' => 'system.settings.partials.parents',
        ],
        'contact-us' => [
            'title' => 'Contact Us',
            'description' => 'Manage contact page headings, form labels, contact details, submenu helper text, and map content.',
            'partial' => 'system.settings.partials.contact-us',
        ],
        'testimonials' => [
            'title' => 'Testimonials',
            'description' => 'Control testimonial section labels, form wording, and testimonial slider copy.',
            'partial' => 'system.settings.partials.testimonials',
        ],
        'footer' => [
            'title' => 'Footer',
            'description' => 'Edit footer branding, footer description, quick links, resources, social links, and footer contact details.',
            'partial' => 'system.settings.partials.footer',
        ],
        'faqs' => [
            'title' => 'FAQs',
            'description' => 'Manage frequently asked questions displayed on the public Admissions FAQs page. Add, edit, or remove questions and categories.',
            'partial' => 'system.settings.partials.faqs',
        ],
        'system-preferences' => [
            'title' => 'System Preferences',
            'description' => 'Configure grading, communication, SMTP delivery, and operational preferences.',
            'partial' => 'system.settings.partials.system-preferences',
        ],
    ];

    public function index(Request $request)
    {
        $this->ensureAdminAccess();

        $page = $this->normalizeSettingsPage((string) $request->query('section', 'site-settings')) ?? 'site-settings';

        return redirect()->route('settings.page', ['page' => $page]);
    }

    public function showPage(string $page)
    {
        $this->ensureAdminAccess();

        $page = $this->normalizeSettingsPage($page);
        abort_unless($page !== null, 404);

        $school = auth()->user()->school ?? SchoolContext::current();

        return view('system.settings.page', array_merge(
            $this->settingsViewData($school),
            [
                'school' => $school,
                'settingsPage' => $page,
                'pageMeta' => self::SETTINGS_PAGE_META[$page],
                'settingsPages' => collect(self::SETTINGS_PAGE_META)
                    ->map(function (array $meta, string $key) {
                        return [
                            'key' => $key,
                            'title' => $meta['title'],
                            'route' => route('settings.page', ['page' => $key]),
                        ];
                    })
                    ->values(),
            ]
        ));
    }

    private function settingsViewData($school): array
    {
        $publicPage = PublicPageContent::forSchool($school);
        $normalizeBannerItems = function (array $items) {
            return collect($items)
                ->map(function ($item) {
                    return [
                        'image' => $item['image'] ?? ($item['path'] ?? null),
                        'title' => trim((string) ($item['title'] ?? '')),
                        'description' => trim((string) ($item['description'] ?? '')),
                    ];
                })
                ->filter(function (array $item) {
                    return !empty($item['image']) || $item['title'] !== '' || $item['description'] !== '';
                })
                ->values();
        };

        $bannerSlots = function (\Illuminate\Support\Collection $items, int $count) {
            return collect(range(0, $count - 1))
                ->map(function (int $index) use ($items) {
                    return $items->get($index, [
                        'image' => null,
                        'title' => '',
                        'description' => '',
                    ]);
                })
                ->all();
        };

        $parentsBanners = $normalizeBannerItems($publicPage['parents_banners'] ?? []);
        if ($parentsBanners->isEmpty()) {
            $parentsBanners = $normalizeBannerItems(collect($publicPage['parents'] ?? [])
                ->map(function ($item) {
                    return [
                        'image' => null,
                        'title' => trim((string) data_get($item, 'title', '')),
                        'description' => trim((string) data_get($item, 'description', '')),
                    ];
                })
                ->all());
        }

        $whyChooseUsBanners = $normalizeBannerItems($publicPage['why_choose_us_banners'] ?? []);
        if ($whyChooseUsBanners->isEmpty()) {
            $whyChooseUsBanners = collect($publicPage['why_choose_us'] ?? [])
                ->map(function ($text, int $index) {
                    return [
                        'image' => null,
                        'title' => 'Why Choose Us ' . ($index + 1),
                        'description' => trim((string) $text),
                    ];
                })
                ->filter(function (array $item) {
                    return $item['description'] !== '';
                })
                ->values();
        }

        $aboutBanners = $normalizeBannerItems($publicPage['about_banners'] ?? []);
        if ($aboutBanners->isEmpty()) {
            $aboutBanners = $normalizeBannerItems(collect($publicPage['about'] ?? [])
                ->map(function ($item) {
                    return [
                        'image' => null,
                        'title' => trim((string) data_get($item, 'title', '')),
                        'description' => trim((string) data_get($item, 'description', '')),
                    ];
                })
                ->all());
        }

        $teacherMarqueeItems = collect($publicPage['teachers_marquee'] ?? [])
            ->map(function ($item) {
                return [
                    'image' => $item['image'] ?? ($item['path'] ?? null),
                    'name' => trim((string) ($item['name'] ?? '')),
                    'role' => trim((string) ($item['role'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return !empty($item['image']) || $item['name'] !== '' || $item['role'] !== '';
            })
            ->values();

        if ($teacherMarqueeItems->isEmpty()) {
            $teacherMarqueeItems = collect([
                ['image' => null, 'name' => '', 'role' => ''],
            ]);
        }

        $parentsBannerSlots = $bannerSlots($parentsBanners, 6);
        $whyChooseUsBannerSlots = $bannerSlots($whyChooseUsBanners, 4);
        $aboutBannerSlots = $bannerSlots($aboutBanners, 6);
        $academicsVisuals = collect($publicPage['academics_visuals'] ?? [])
            ->map(function ($item) {
                if (is_array($item)) {
                    return trim((string) ($item['image'] ?? ($item['path'] ?? '')));
                }

                return trim((string) $item);
            })
            ->filter()
            ->values();
        $academicsVisualSlots = collect(range(0, 1))
            ->map(function (int $index) use ($academicsVisuals) {
                return [
                    'image' => $academicsVisuals->get($index),
                ];
            })
            ->all();

        return [
            'school' => $school,
            'publicPage' => $publicPage,
            'faviconPath' => data_get($school->settings, 'branding.favicon'),
            'whyChooseUsText' => implode(PHP_EOL, $publicPage['why_choose_us'] ?? []),
            'facilitiesText' => implode(PHP_EOL, $publicPage['facilities'] ?? []),
            'admissionStepsText' => implode(PHP_EOL, $publicPage['admission_steps'] ?? []),
            'programItemsText' => PublicPageContent::itemsToLines($publicPage['programs'] ?? []),
            'admissionsItemsText' => PublicPageContent::itemsToLines($publicPage['admissions'] ?? []),
            'academicsItemsText' => PublicPageContent::itemsToLines($publicPage['academics'] ?? []),
            'aboutItemsText' => PublicPageContent::itemsToLines($publicPage['about'] ?? []),
            'studentLifeItemsText' => PublicPageContent::itemsToLines($publicPage['student_life'] ?? []),
            'parentsItemsText' => PublicPageContent::itemsToLines($publicPage['parents'] ?? []),
            'contactItemsText' => PublicPageContent::itemsToLines($publicPage['contact_items'] ?? []),
            'footerQuickLinksText' => PublicPageContent::itemsToLines($publicPage['footer_quick_links'] ?? []),
            'footerResourcesText' => PublicPageContent::itemsToLines($publicPage['footer_resources'] ?? []),
            'footerSocialLinksText' => PublicPageContent::itemsToLines($publicPage['footer_social_links'] ?? []),
            'parentsBannerSlots' => $parentsBannerSlots,
            'whyChooseUsBannerSlots' => $whyChooseUsBannerSlots,
            'aboutBannerSlots' => $aboutBannerSlots,
            'teacherMarqueeItems' => $teacherMarqueeItems->all(),
            'academicsVisualSlots' => $academicsVisualSlots,
        ];
    }

    public function update(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $request->merge([
            'domain' => DomainHelper::normalize($request->input('domain')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'domain' => ['nullable', 'string', 'max:190', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', Rule::unique('schools', 'domain')->ignore($school->id)],
            'motto' => 'nullable|string',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'remove_logo' => 'nullable|boolean',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,webp|max:1024',
            'remove_favicon' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_logo') && $school->logo) {
            Storage::disk('public')->delete($school->logo);
            $validated['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $settings = $school->settings ?? [];
        $branding = (array) ($settings['branding'] ?? []);
        $existingFavicon = trim((string) ($branding['favicon'] ?? ''));

        if ($request->boolean('remove_favicon') && $existingFavicon !== '') {
            Storage::disk('public')->delete($existingFavicon);
            $existingFavicon = '';
        }

        if ($request->hasFile('favicon')) {
            if ($existingFavicon !== '') {
                Storage::disk('public')->delete($existingFavicon);
            }

            $existingFavicon = $request->file('favicon')->store('schools/favicons', 'public');
        }

        $branding['favicon'] = $existingFavicon !== '' ? $existingFavicon : null;
        $settings['branding'] = $branding;

        unset($validated['favicon'], $validated['remove_favicon']);
        $school->update($validated);
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Settings updated.');
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();
        $settings = $school->settings ?? [];
        $validated = $request->validate([
            'grading_system' => 'nullable|in:waec,custom',
            'currency_symbol' => 'nullable|string|max:5',
            'result_approval_required' => 'nullable|boolean',
            'online_admission_enabled' => 'nullable|boolean',
            'sms_notifications_enabled' => 'nullable|boolean',
            'email_notifications_enabled' => 'nullable|boolean',
            'smtp_enabled' => 'nullable|boolean',
            'smtp_host' => 'nullable|string|max:190',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'smtp_username' => 'nullable|string|max:190',
            'smtp_password' => 'nullable|string|max:190',
            'smtp_from_address' => 'nullable|email|max:190',
            'smtp_from_name' => 'nullable|string|max:190',
            'smtp_to_address' => 'nullable|email|max:190',
        ]);

        $settings = array_merge($settings, [
            'grading_system' => $validated['grading_system'] ?? ($settings['grading_system'] ?? null),
            'currency_symbol' => $validated['currency_symbol'] ?? ($settings['currency_symbol'] ?? null),
            'result_approval_required' => $request->boolean('result_approval_required'),
            'online_admission_enabled' => $request->boolean('online_admission_enabled'),
            'sms_notifications_enabled' => $request->boolean('sms_notifications_enabled'),
            'email_notifications_enabled' => $request->boolean('email_notifications_enabled'),
        ]);

        $existingSmtp = (array) ($settings['smtp'] ?? []);
        $incomingPassword = trim((string) ($validated['smtp_password'] ?? ''));

        $settings['smtp'] = [
            'enabled' => $request->boolean('smtp_enabled'),
            'host' => trim((string) ($validated['smtp_host'] ?? '')),
            'port' => (int) ($validated['smtp_port'] ?? 587),
            'encryption' => trim((string) ($validated['smtp_encryption'] ?? 'tls')) ?: 'tls',
            'username' => trim((string) ($validated['smtp_username'] ?? '')),
            'password' => $incomingPassword !== ''
                ? Crypt::encryptString($incomingPassword)
                : (string) ($existingSmtp['password'] ?? ''),
            'from_address' => trim((string) ($validated['smtp_from_address'] ?? '')),
            'from_name' => trim((string) ($validated['smtp_from_name'] ?? '')),
            'to_address' => trim((string) ($validated['smtp_to_address'] ?? '')),
        ];

        $school->update(['settings' => $settings]);

        return back()->with('success', 'System settings updated.');
    }

    public function uploadSubmenuImage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'section' => 'required|string|alpha_dash|max:60',
            'slug' => 'required|string|max:120|regex:/^[a-z0-9\-]+$/',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:6144',
        ]);

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $submenuImages = (array) ($publicPage['submenu_images'] ?? []);
        $sectionImages = (array) ($submenuImages[$validated['section']] ?? []);

        $existingPath = trim((string) ($sectionImages[$validated['slug']] ?? ''));
        if ($existingPath !== '') {
            Storage::disk('public')->delete($existingPath);
        }

        $path = $validated['image']->store('schools/submenu-images/' . $school->id, 'public');

        $sectionImages[$validated['slug']] = $path;
        $submenuImages[$validated['section']] = $sectionImages;
        $publicPage['submenu_images'] = $submenuImages;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Submenu image uploaded.');
    }

    public function removeSubmenuImage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'section' => 'required|string|alpha_dash|max:60',
            'slug' => 'required|string|max:120|regex:/^[a-z0-9\-]+$/',
        ]);

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $submenuImages = (array) ($publicPage['submenu_images'] ?? []);
        $sectionImages = (array) ($submenuImages[$validated['section']] ?? []);

        $existingPath = trim((string) ($sectionImages[$validated['slug']] ?? ''));
        if ($existingPath !== '') {
            Storage::disk('public')->delete($existingPath);
        }

        unset($sectionImages[$validated['slug']]);
        $submenuImages[$validated['section']] = $sectionImages;
        $publicPage['submenu_images'] = $submenuImages;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Submenu image removed.');
    }

    public function saveSubmenuContent(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'section'             => 'required|string|alpha_dash|max:60',
            'slug'                => 'required|string|max:120|regex:/^[a-z0-9\-]+$/',
            'description'         => 'nullable|string|max:8000',
            'highlight_one_title' => 'nullable|string|max:120',
            'highlight_one_text'  => 'nullable|string|max:2000',
            'highlight_two_title' => 'nullable|string|max:120',
            'highlight_two_text'  => 'nullable|string|max:2000',
        ]);

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $submenuContent = (array) ($publicPage['submenu_content'] ?? []);
        $sectionContent = (array) ($submenuContent[$validated['section']] ?? []);

        $existing = (array) ($sectionContent[$validated['slug']] ?? []);
        $sectionContent[$validated['slug']] = array_merge($existing, [
            'description'         => RichText::sanitize($validated['description'] ?? ''),
            'highlight_one_title' => trim((string) ($validated['highlight_one_title'] ?? '')),
            'highlight_one_text'  => RichText::sanitize($validated['highlight_one_text'] ?? ''),
            'highlight_two_title' => trim((string) ($validated['highlight_two_title'] ?? '')),
            'highlight_two_text'  => RichText::sanitize($validated['highlight_two_text'] ?? ''),
        ]);

        $submenuContent[$validated['section']] = $sectionContent;
        $publicPage['submenu_content'] = $submenuContent;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Content saved.');
    }

    public function uploadSubmenuContentImage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'section' => 'required|string|alpha_dash|max:60',
            'slug'    => 'required|string|max:120|regex:/^[a-z0-9\-]+$/',
            'slot'    => 'required|in:image_one,image_two',
            'image'   => 'required|image|mimes:jpg,jpeg,png,webp|max:6144',
        ]);

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $submenuContent = (array) ($publicPage['submenu_content'] ?? []);
        $sectionContent = (array) ($submenuContent[$validated['section']] ?? []);
        $itemContent = (array) ($sectionContent[$validated['slug']] ?? []);

        $existingPath = trim((string) ($itemContent[$validated['slot']] ?? ''));
        if ($existingPath !== '') {
            Storage::disk('public')->delete($existingPath);
        }

        $path = $validated['image']->store('schools/submenu-images/' . $school->id, 'public');

        $itemContent[$validated['slot']] = $path;
        $sectionContent[$validated['slug']] = $itemContent;
        $submenuContent[$validated['section']] = $sectionContent;
        $publicPage['submenu_content'] = $submenuContent;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Page image uploaded.');
    }

    public function removeSubmenuContentImage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'section' => 'required|string|alpha_dash|max:60',
            'slug'    => 'required|string|max:120|regex:/^[a-z0-9\-]+$/',
            'slot'    => 'required|in:image_one,image_two',
        ]);

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $submenuContent = (array) ($publicPage['submenu_content'] ?? []);
        $sectionContent = (array) ($submenuContent[$validated['section']] ?? []);
        $itemContent = (array) ($sectionContent[$validated['slug']] ?? []);

        $existingPath = trim((string) ($itemContent[$validated['slot']] ?? ''));
        if ($existingPath !== '') {
            Storage::disk('public')->delete($existingPath);
        }

        unset($itemContent[$validated['slot']]);
        $sectionContent[$validated['slug']] = $itemContent;
        $submenuContent[$validated['section']] = $sectionContent;
        $publicPage['submenu_content'] = $submenuContent;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Page image removed.');
    }

    public function uploadRichTextImage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $path = $validated['upload']->store('schools/public/editor/' . ($school?->id ?? 'shared'), 'public');

        return response()->json([
            'url' => asset('storage/' . ltrim($path, '/')),
        ]);
    }

    public function sendSmtpTest(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();
        $settings = $school->settings ?? [];
        $smtp = (array) ($settings['smtp'] ?? []);

        $validated = $request->validate([
            'smtp_test_recipient' => 'nullable|email|max:190',
        ]);

        $recipient = trim((string) ($validated['smtp_test_recipient'] ?? ''));
        if ($recipient === '') {
            $recipient = trim((string) ($smtp['to_address'] ?? ($school->email ?? '')));
        }

        if (!($smtp['enabled'] ?? false)) {
            return back()->withErrors(['smtp_test' => 'SMTP is disabled. Enable SMTP first, then try again.']);
        }

        if ($recipient === '') {
            return back()->withErrors(['smtp_test' => 'No recipient email configured for test delivery.']);
        }

        try {
            $this->configureSmtpMailer($smtp, (string) ($school->name ?? 'School'));

            Mail::send('emails.smtp-test', [
                'school' => $school,
                'sentAt' => now(),
                'recipient' => $recipient,
                'smtp' => $smtp,
            ], function ($message) use ($recipient, $school) {
                $message->to($recipient)
                    ->subject('SMTP Test Email - ' . ($school->name ?? 'School'));
            });
        } catch (Throwable $exception) {
            Log::error('SMTP test email failed', [
                'school_id' => $school?->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'smtp_test' => 'SMTP test failed. Please verify host, port, encryption, username/password, and sender settings, then try again.',
            ]);
        }

        return back()->with('success', 'SMTP test email sent successfully to ' . $recipient . '.');
    }

    public function updatePublicPage(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();
        $settings = $school->settings ?? [];
        $publicPage = PublicPageContent::forSchool($school);

        $this->mergeMissingPublicPageInputs($request, $publicPage);

        $validated = $request->validate([
            'hero_badge_text' => 'nullable|string|max:120',
            'hero_title' => 'required|string|max:220',
            'hero_subtitle' => 'nullable|string|max:4000',
            'cta_primary_text' => 'nullable|string|max:60',
            'cta_secondary_text' => 'nullable|string|max:60',
            'site_background_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'primary_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'heading_text_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'body_text_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'surface_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'soft_surface_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'theme_style' => 'nullable|in:modern-grid,soft-gradient,minimal-clean',
            'header_bg_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'footer_bg_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'footer_separator_color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'admission_session_text' => 'nullable|string|max:120',
            'footer_note' => 'nullable|string|max:255',

            'programs_intro' => 'nullable|string|max:4000',
            'admissions_intro' => 'nullable|string|max:4000',
            'academics_intro' => 'nullable|string|max:4000',
            'academics_support_text' => 'nullable|string|max:4000',
            'academic_highlight_1_title' => 'nullable|string|max:120',
            'academic_highlight_1_description' => 'nullable|string|max:1500',
            'academic_highlight_2_title' => 'nullable|string|max:120',
            'academic_highlight_2_description' => 'nullable|string|max:1500',
            'facilities_intro' => 'nullable|string|max:4000',
            'about_intro' => 'nullable|string|max:4000',
            'student_life_intro' => 'nullable|string|max:4000',
            'parents_intro' => 'nullable|string|max:4000',
            'contact_intro' => 'nullable|string|max:4000',
            'why_choose_us_label' => 'nullable|string|max:120',
            'why_choose_us_intro' => 'nullable|string|max:4000',
            'teachers_marquee_label' => 'nullable|string|max:120',
            'teachers_marquee_heading' => 'nullable|string|max:180',
            'teachers_marquee_intro' => 'nullable|string|max:500',
            'programs_label' => 'nullable|string|max:120',
            'admissions_label' => 'nullable|string|max:120',
            'admissions_process_label' => 'nullable|string|max:120',
            'academics_label' => 'nullable|string|max:120',
            'facilities_label' => 'nullable|string|max:120',
            'about_label' => 'nullable|string|max:120',
            'student_life_label' => 'nullable|string|max:120',
            'parents_label' => 'nullable|string|max:120',
            'contact_label' => 'nullable|string|max:120',
            'header_apply_text' => 'nullable|string|max:120',
            'header_portal_login_text' => 'nullable|string|max:120',
            'mobile_apply_text' => 'nullable|string|max:120',
            'mobile_portal_login_text' => 'nullable|string|max:120',
            'hero_slider_placeholder_text' => 'nullable|string|max:220',
            'parents_portal_button_text' => 'nullable|string|max:120',
            'testimonials_badge_text' => 'nullable|string|max:120',
            'testimonials_heading' => 'nullable|string|max:220',
            'testimonials_subheading' => 'nullable|string|max:500',
            'testimonials_form_title' => 'nullable|string|max:180',
            'testimonials_form_name_label' => 'nullable|string|max:120',
            'testimonials_form_name_placeholder' => 'nullable|string|max:160',
            'testimonials_form_role_label' => 'nullable|string|max:120',
            'testimonials_form_role_placeholder' => 'nullable|string|max:180',
            'testimonials_form_rating_label' => 'nullable|string|max:120',
            'testimonials_form_message_label' => 'nullable|string|max:120',
            'testimonials_form_message_placeholder' => 'nullable|string|max:220',
            'testimonials_form_submit_text' => 'nullable|string|max:120',
            'testimonials_slider_title' => 'nullable|string|max:180',
            'testimonials_empty_text' => 'nullable|string|max:260',
            'testimonials_success_text' => 'nullable|string|max:220',
            'testimonials_error_text' => 'nullable|string|max:220',
            'quick_contact_label' => 'nullable|string|max:120',
            'contact_phone_label' => 'nullable|string|max:60',
            'contact_whatsapp_label' => 'nullable|string|max:60',
            'contact_email_label' => 'nullable|string|max:60',
            'contact_address_label' => 'nullable|string|max:60',
            'visit_booking_button_text' => 'nullable|string|max:120',
            'quick_apply_button_text' => 'nullable|string|max:120',
            'menu_overview_suffix' => 'nullable|string|max:80',
            'site_title_suffix' => 'nullable|string|max:160',
            'mobile_menu_title' => 'nullable|string|max:80',
            'footer_quick_links_title' => 'nullable|string|max:120',
            'footer_resources_title' => 'nullable|string|max:120',
            'footer_contact_title' => 'nullable|string|max:120',
            'contact_page_browser_title' => 'nullable|string|max:120',
            'contact_page_badge_text' => 'nullable|string|max:120',
            'contact_page_heading' => 'nullable|string|max:180',
            'contact_page_subheading' => 'nullable|string|max:4000',
            'contact_form_title' => 'nullable|string|max:120',
            'contact_form_full_name_label' => 'nullable|string|max:120',
            'contact_form_full_name_placeholder' => 'nullable|string|max:160',
            'contact_form_email_label' => 'nullable|string|max:120',
            'contact_form_email_placeholder' => 'nullable|string|max:160',
            'contact_form_phone_label' => 'nullable|string|max:120',
            'contact_form_phone_placeholder' => 'nullable|string|max:160',
            'contact_form_subject_label' => 'nullable|string|max:120',
            'contact_form_subject_placeholder' => 'nullable|string|max:160',
            'contact_form_message_label' => 'nullable|string|max:120',
            'contact_form_message_placeholder' => 'nullable|string|max:160',
            'contact_form_submit_text' => 'nullable|string|max:120',
            'contact_info_title' => 'nullable|string|max:120',
            'contact_not_provided_text' => 'nullable|string|max:120',
            'contact_more_details_title' => 'nullable|string|max:140',
            'map_embed_title_text' => 'nullable|string|max:120',
            'submenu_description_fallback_template' => 'nullable|string|max:400',
            'contact_status_unavailable_text' => 'nullable|string|max:220',
            'contact_status_recipient_missing_text' => 'nullable|string|max:220',
            'contact_status_send_error_text' => 'nullable|string|max:220',
            'contact_status_success_text' => 'nullable|string|max:220',
            'legal_effective_date' => 'nullable|string|max:80',
            'privacy_policy_title' => 'nullable|string|max:180',
            'privacy_policy_intro' => 'nullable|string|max:4000',
            'privacy_policy_content' => 'nullable|string|max:30000',
            'cookies_policy_title' => 'nullable|string|max:180',
            'cookies_policy_intro' => 'nullable|string|max:4000',
            'cookies_policy_content' => 'nullable|string|max:30000',
            'cookie_banner_title' => 'nullable|string|max:120',
            'cookie_banner_text' => 'nullable|string|max:1200',
            'cookie_banner_accept_text' => 'nullable|string|max:80',
            'cookie_banner_reject_text' => 'nullable|string|max:120',
            'submenu_highlight_one_title' => 'nullable|string|max:120',
            'submenu_highlight_one_text' => 'nullable|string|max:1500',
            'submenu_highlight_two_title' => 'nullable|string|max:120',
            'submenu_highlight_two_text' => 'nullable|string|max:1500',
            'submenu_primary_button_text' => 'nullable|string|max:120',
            'submenu_back_button_prefix' => 'nullable|string|max:120',
            'submenu_more_in_prefix' => 'nullable|string|max:120',

            'program_items_text' => 'nullable|string',
            'admissions_items_text' => 'nullable|string',
            'academics_items_text' => 'nullable|string',
            'about_items_text' => 'nullable|string',
            'student_life_items_text' => 'nullable|string',
            'parents_items_text' => 'nullable|string',
            'contact_items_text' => 'nullable|string',

            'whatsapp' => 'nullable|string|max:40',
            'visit_booking_url' => 'nullable|url|max:255',
            'map_embed_url' => 'nullable|url|max:1200',

            'why_choose_us' => 'nullable|string',
            'facilities' => 'nullable|string',
            'admission_steps' => 'nullable|string',

            'metric_1_value' => 'nullable|string|max:25',
            'metric_1_label' => 'nullable|string|max:80',
            'metric_2_value' => 'nullable|string|max:25',
            'metric_2_label' => 'nullable|string|max:80',
            'metric_3_value' => 'nullable|string|max:25',
            'metric_3_label' => 'nullable|string|max:80',
            'metric_4_value' => 'nullable|string|max:25',
            'metric_4_label' => 'nullable|string|max:80',

            'hero_slide_1' => 'nullable|image|max:4096',
            'hero_slide_2' => 'nullable|image|max:4096',
            'hero_slide_3' => 'nullable|image|max:4096',
            'hero_slide_1_caption' => 'nullable|string|max:120',
            'hero_slide_2_caption' => 'nullable|string|max:120',
            'hero_slide_3_caption' => 'nullable|string|max:120',
            'remove_hero_slide_1' => 'nullable|boolean',
            'remove_hero_slide_2' => 'nullable|boolean',
            'remove_hero_slide_3' => 'nullable|boolean',
            'academic_image_1' => 'nullable|image|max:4096',
            'academic_image_2' => 'nullable|image|max:4096',
            'remove_academic_image_1' => 'nullable|boolean',
            'remove_academic_image_2' => 'nullable|boolean',
            'parent_banner_1_image' => 'nullable|image|max:4096',
            'parent_banner_2_image' => 'nullable|image|max:4096',
            'parent_banner_3_image' => 'nullable|image|max:4096',
            'parent_banner_4_image' => 'nullable|image|max:4096',
            'parent_banner_5_image' => 'nullable|image|max:4096',
            'parent_banner_6_image' => 'nullable|image|max:4096',
            'parent_banner_1_title' => 'nullable|string|max:120',
            'parent_banner_2_title' => 'nullable|string|max:120',
            'parent_banner_3_title' => 'nullable|string|max:120',
            'parent_banner_4_title' => 'nullable|string|max:120',
            'parent_banner_5_title' => 'nullable|string|max:120',
            'parent_banner_6_title' => 'nullable|string|max:120',
            'parent_banner_1_description' => 'nullable|string|max:1500',
            'parent_banner_2_description' => 'nullable|string|max:1500',
            'parent_banner_3_description' => 'nullable|string|max:1500',
            'parent_banner_4_description' => 'nullable|string|max:1500',
            'parent_banner_5_description' => 'nullable|string|max:1500',
            'parent_banner_6_description' => 'nullable|string|max:1500',
            'remove_parent_banner_1' => 'nullable|boolean',
            'remove_parent_banner_2' => 'nullable|boolean',
            'remove_parent_banner_3' => 'nullable|boolean',
            'remove_parent_banner_4' => 'nullable|boolean',
            'remove_parent_banner_5' => 'nullable|boolean',
            'remove_parent_banner_6' => 'nullable|boolean',
            'why_banner_1_image' => 'nullable|image|max:4096',
            'why_banner_2_image' => 'nullable|image|max:4096',
            'why_banner_3_image' => 'nullable|image|max:4096',
            'why_banner_4_image' => 'nullable|image|max:4096',
            'why_banner_1_title' => 'nullable|string|max:120',
            'why_banner_2_title' => 'nullable|string|max:120',
            'why_banner_3_title' => 'nullable|string|max:120',
            'why_banner_4_title' => 'nullable|string|max:120',
            'why_banner_1_description' => 'nullable|string|max:1500',
            'why_banner_2_description' => 'nullable|string|max:1500',
            'why_banner_3_description' => 'nullable|string|max:1500',
            'why_banner_4_description' => 'nullable|string|max:1500',
            'remove_why_banner_1' => 'nullable|boolean',
            'remove_why_banner_2' => 'nullable|boolean',
            'remove_why_banner_3' => 'nullable|boolean',
            'remove_why_banner_4' => 'nullable|boolean',
            'about_banner_1_image' => 'nullable|image|max:4096',
            'about_banner_2_image' => 'nullable|image|max:4096',
            'about_banner_3_image' => 'nullable|image|max:4096',
            'about_banner_4_image' => 'nullable|image|max:4096',
            'about_banner_5_image' => 'nullable|image|max:4096',
            'about_banner_6_image' => 'nullable|image|max:4096',
            'about_banner_1_title' => 'nullable|string|max:120',
            'about_banner_2_title' => 'nullable|string|max:120',
            'about_banner_3_title' => 'nullable|string|max:120',
            'about_banner_4_title' => 'nullable|string|max:120',
            'about_banner_5_title' => 'nullable|string|max:120',
            'about_banner_6_title' => 'nullable|string|max:120',
            'about_banner_1_description' => 'nullable|string|max:1500',
            'about_banner_2_description' => 'nullable|string|max:1500',
            'about_banner_3_description' => 'nullable|string|max:1500',
            'about_banner_4_description' => 'nullable|string|max:1500',
            'about_banner_5_description' => 'nullable|string|max:1500',
            'about_banner_6_description' => 'nullable|string|max:1500',
            'remove_about_banner_1' => 'nullable|boolean',
            'remove_about_banner_2' => 'nullable|boolean',
            'remove_about_banner_3' => 'nullable|boolean',
            'remove_about_banner_4' => 'nullable|boolean',
            'remove_about_banner_5' => 'nullable|boolean',
            'remove_about_banner_6' => 'nullable|boolean',
            'teacher_marquee' => 'nullable|array|max:80',
            'teacher_marquee.*.name' => 'nullable|string|max:120',
            'teacher_marquee.*.role' => 'nullable|string|max:120',
            'teacher_marquee.*.existing_image' => 'nullable|string|max:500',
            'teacher_marquee.*.remove_image' => 'nullable|boolean',
            'teacher_marquee.*.remove_row' => 'nullable|boolean',
            'teacher_marquee.*.image' => 'nullable|image|max:4096',

            'footer_description' => 'nullable|string|max:4000',
            'footer_contact_address' => 'nullable|string|max:255',
            'footer_contact_phone' => 'nullable|string|max:80',
            'footer_contact_email' => 'nullable|email|max:190',
            'footer_quick_links_text' => 'nullable|string',
            'footer_resources_text' => 'nullable|string',
            'footer_social_links_text' => 'nullable|string',
            'footer_logo' => 'nullable|image|max:4096',
            'remove_footer_logo' => 'nullable|boolean',
            'contact_hero_image' => 'nullable|image|max:6144',
            'remove_contact_hero_image' => 'nullable|boolean',
        ]);

        foreach ([
            'hero_subtitle',
            'programs_intro',
            'admissions_intro',
            'academics_intro',
            'academics_support_text',
            'facilities_intro',
            'about_intro',
            'student_life_intro',
            'parents_intro',
            'contact_intro',
            'why_choose_us_intro',
            'contact_page_subheading',
            'submenu_highlight_one_text',
            'submenu_highlight_two_text',
            'academic_highlight_1_description',
            'academic_highlight_2_description',
            'footer_description',
            'privacy_policy_intro',
            'privacy_policy_content',
            'cookies_policy_intro',
            'cookies_policy_content',
        ] as $richField) {
            $validated[$richField] = RichText::sanitize($validated[$richField] ?? '');
        }

        foreach (range(1, 6) as $index) {
            $validated["parent_banner_{$index}_description"] = RichText::sanitize($validated["parent_banner_{$index}_description"] ?? '');
            $validated["about_banner_{$index}_description"] = RichText::sanitize($validated["about_banner_{$index}_description"] ?? '');
        }

        foreach (range(1, 4) as $index) {
            $validated["why_banner_{$index}_description"] = RichText::sanitize($validated["why_banner_{$index}_description"] ?? '');
        }

        $publicPage['hero_badge_text'] = $validated['hero_badge_text'] ?? '';
        $publicPage['hero_title'] = $validated['hero_title'];
        $publicPage['hero_subtitle'] = $validated['hero_subtitle'] ?? '';
        $publicPage['cta_primary_text'] = $validated['cta_primary_text'] ?? 'Start Admission';
        $publicPage['cta_secondary_text'] = $validated['cta_secondary_text'] ?? 'Explore Programs';
        $primaryColor = strtoupper($validated['primary_color'] ?? ($publicPage['primary_color'] ?? '#2D1D5C'));
        $secondaryColor = strtoupper($validated['secondary_color'] ?? ($publicPage['secondary_color'] ?? '#DFE753'));

        $publicPage['site_background_color'] = strtoupper($validated['site_background_color'] ?? '#F8FAFC');
        $publicPage['primary_color'] = $primaryColor;
        $publicPage['secondary_color'] = $secondaryColor;
        $publicPage['heading_text_color'] = strtoupper($validated['heading_text_color'] ?? ($publicPage['heading_text_color'] ?? '#0F172A'));
        $publicPage['body_text_color'] = strtoupper($validated['body_text_color'] ?? ($publicPage['body_text_color'] ?? '#475569'));
        $publicPage['surface_color'] = strtoupper($validated['surface_color'] ?? ($publicPage['surface_color'] ?? '#FFFFFF'));
        $publicPage['soft_surface_color'] = strtoupper($validated['soft_surface_color'] ?? ($publicPage['soft_surface_color'] ?? '#EEF6FF'));
        $publicPage['theme_style'] = $validated['theme_style'] ?? ($publicPage['theme_style'] ?? 'modern-grid');
        $publicPage['header_bg_color'] = strtoupper($validated['header_bg_color'] ?? $primaryColor);
        $publicPage['footer_bg_color'] = strtoupper($validated['footer_bg_color'] ?? $primaryColor);
        $publicPage['footer_separator_color'] = strtoupper($validated['footer_separator_color'] ?? $secondaryColor);
        $publicPage['admission_session_text'] = $validated['admission_session_text'] ?? 'Apply for Current Session';
        $publicPage['footer_note'] = $validated['footer_note'] ?? 'All rights reserved.';

        $publicPage['programs_intro'] = $validated['programs_intro'] ?? '';
        $publicPage['admissions_intro'] = $validated['admissions_intro'] ?? '';
        $publicPage['academics_intro'] = $validated['academics_intro'] ?? 'A Structured Learning Culture With Mentorship At The Center.';
        $publicPage['academics_support_text'] = $validated['academics_support_text'] ?? 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.';
        $publicPage['academic_highlights'] = collect([
            [
                'title' => trim((string) ($validated['academic_highlight_1_title'] ?? 'STEM-First Curriculum')),
                'description' => trim((string) ($validated['academic_highlight_1_description'] ?? 'Coding, robotics, and science labs integrated into junior and senior classes.')),
            ],
            [
                'title' => trim((string) ($validated['academic_highlight_2_title'] ?? 'Student Leadership')),
                'description' => trim((string) ($validated['academic_highlight_2_description'] ?? 'Public speaking, media, and entrepreneurship clubs with measurable outcomes.')),
            ],
        ])->filter(function (array $item) {
            return $item['title'] !== '' || $item['description'] !== '';
        })->values()->all();
        $publicPage['facilities_intro'] = $validated['facilities_intro'] ?? '';
        $publicPage['about_intro'] = $validated['about_intro'] ?? '';
        $publicPage['student_life_intro'] = $validated['student_life_intro'] ?? '';
        $publicPage['parents_intro'] = $validated['parents_intro'] ?? '';
        $publicPage['contact_intro'] = $validated['contact_intro'] ?? '';
        $publicPage['why_choose_us_label'] = $validated['why_choose_us_label'] ?? 'Why Choose Us';
        $publicPage['why_choose_us_intro'] = $validated['why_choose_us_intro'] ?? '';
        $publicPage['teachers_marquee_label'] = $validated['teachers_marquee_label'] ?? 'Our Teachers';
        $publicPage['teachers_marquee_heading'] = $validated['teachers_marquee_heading'] ?? 'Meet Our Teaching Team';
        $publicPage['teachers_marquee_intro'] = $validated['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.';
        $publicPage['programs_label'] = $validated['programs_label'] ?? 'Programs';
        $publicPage['admissions_label'] = $validated['admissions_label'] ?? 'Admissions';
        $publicPage['admissions_process_label'] = $validated['admissions_process_label'] ?? 'Admissions Process';
        $publicPage['academics_label'] = $validated['academics_label'] ?? 'Academic Excellence';
        $publicPage['facilities_label'] = $validated['facilities_label'] ?? 'Facilities';
        $publicPage['about_label'] = $validated['about_label'] ?? 'About Us';
        $publicPage['student_life_label'] = $validated['student_life_label'] ?? 'Student Life';
        $publicPage['parents_label'] = $validated['parents_label'] ?? 'Parents';
        $publicPage['contact_label'] = $validated['contact_label'] ?? 'Contact';
        $publicPage['header_apply_text'] = $validated['header_apply_text'] ?? 'Apply';
        $publicPage['header_portal_login_text'] = $validated['header_portal_login_text'] ?? 'Portal Login';
        $publicPage['mobile_apply_text'] = $validated['mobile_apply_text'] ?? 'Apply Now';
        $publicPage['mobile_portal_login_text'] = $validated['mobile_portal_login_text'] ?? 'Portal Login';
        $publicPage['hero_slider_placeholder_text'] = $validated['hero_slider_placeholder_text'] ?? 'Upload hero slider images from Admin Settings to personalize this section.';
        $publicPage['parents_portal_button_text'] = $validated['parents_portal_button_text'] ?? 'Parent Portal Login';
        $publicPage['testimonials_badge_text'] = $validated['testimonials_badge_text'] ?? 'Testimonials';
        $publicPage['testimonials_heading'] = $validated['testimonials_heading'] ?? 'What Parents and Student Say';
        $publicPage['testimonials_subheading'] = $validated['testimonials_subheading'] ?? 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.';
        $publicPage['testimonials_form_title'] = $validated['testimonials_form_title'] ?? 'Share Your Testimonial';
        $publicPage['testimonials_form_name_label'] = $validated['testimonials_form_name_label'] ?? 'Full Name';
        $publicPage['testimonials_form_name_placeholder'] = $validated['testimonials_form_name_placeholder'] ?? 'Enter your full name';
        $publicPage['testimonials_form_role_label'] = $validated['testimonials_form_role_label'] ?? 'Role or Context';
        $publicPage['testimonials_form_role_placeholder'] = $validated['testimonials_form_role_placeholder'] ?? 'Parent, student, alumni, guardian, etc.';
        $publicPage['testimonials_form_rating_label'] = $validated['testimonials_form_rating_label'] ?? 'Rating';
        $publicPage['testimonials_form_message_label'] = $validated['testimonials_form_message_label'] ?? 'Your Testimonial';
        $publicPage['testimonials_form_message_placeholder'] = $validated['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...';
        $publicPage['testimonials_form_submit_text'] = $validated['testimonials_form_submit_text'] ?? 'Submit Testimonial';
        $publicPage['testimonials_slider_title'] = $validated['testimonials_slider_title'] ?? 'Approved Testimonials';
        $publicPage['testimonials_empty_text'] = $validated['testimonials_empty_text'] ?? 'No testimonials have been approved yet. Be the first to share your experience.';
        $publicPage['testimonials_success_text'] = $validated['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.';
        $publicPage['testimonials_error_text'] = $validated['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.';
        $publicPage['quick_contact_label'] = $validated['quick_contact_label'] ?? 'Quick Contact';
        $publicPage['contact_phone_label'] = $validated['contact_phone_label'] ?? 'Phone';
        $publicPage['contact_whatsapp_label'] = $validated['contact_whatsapp_label'] ?? 'WhatsApp';
        $publicPage['contact_email_label'] = $validated['contact_email_label'] ?? 'Email';
        $publicPage['contact_address_label'] = $validated['contact_address_label'] ?? 'Address';
        $publicPage['visit_booking_button_text'] = $validated['visit_booking_button_text'] ?? 'Visit Booking';
        $publicPage['quick_apply_button_text'] = $validated['quick_apply_button_text'] ?? 'Apply Now';
        $publicPage['menu_overview_suffix'] = $validated['menu_overview_suffix'] ?? 'Overview';
        $publicPage['site_title_suffix'] = $validated['site_title_suffix'] ?? 'KG, Primary and Secondary School';
        $publicPage['mobile_menu_title'] = $validated['mobile_menu_title'] ?? 'Menu';
        $publicPage['footer_quick_links_title'] = $validated['footer_quick_links_title'] ?? 'Quick Links';
        $publicPage['footer_resources_title'] = $validated['footer_resources_title'] ?? 'Resources';
        $publicPage['footer_contact_title'] = $validated['footer_contact_title'] ?? 'Contact';
        $publicPage['contact_page_browser_title'] = $validated['contact_page_browser_title'] ?? 'Contact Us';
        $publicPage['contact_page_badge_text'] = $validated['contact_page_badge_text'] ?? 'Contact Us';
        $publicPage['contact_page_heading'] = $validated['contact_page_heading'] ?? 'We are here to help you';
        $publicPage['contact_page_subheading'] = $validated['contact_page_subheading'] ?? 'Send us a message and our admissions or support team will respond as soon as possible.';
        $publicPage['contact_form_title'] = $validated['contact_form_title'] ?? 'Contact Us Form';
        $publicPage['contact_form_full_name_label'] = $validated['contact_form_full_name_label'] ?? 'Full Name';
        $publicPage['contact_form_full_name_placeholder'] = $validated['contact_form_full_name_placeholder'] ?? 'Enter your full name';
        $publicPage['contact_form_email_label'] = $validated['contact_form_email_label'] ?? 'Email';
        $publicPage['contact_form_email_placeholder'] = $validated['contact_form_email_placeholder'] ?? 'you@example.com';
        $publicPage['contact_form_phone_label'] = $validated['contact_form_phone_label'] ?? 'Phone Number';
        $publicPage['contact_form_phone_placeholder'] = $validated['contact_form_phone_placeholder'] ?? '+234...';
        $publicPage['contact_form_subject_label'] = $validated['contact_form_subject_label'] ?? 'Subject';
        $publicPage['contact_form_subject_placeholder'] = $validated['contact_form_subject_placeholder'] ?? 'How can we help?';
        $publicPage['contact_form_message_label'] = $validated['contact_form_message_label'] ?? 'Message';
        $publicPage['contact_form_message_placeholder'] = $validated['contact_form_message_placeholder'] ?? 'Write your message...';
        $publicPage['contact_form_submit_text'] = $validated['contact_form_submit_text'] ?? 'Send Message';
        $publicPage['contact_info_title'] = $validated['contact_info_title'] ?? 'Contact Information';
        $publicPage['contact_not_provided_text'] = $validated['contact_not_provided_text'] ?? 'Not provided yet';
        $publicPage['contact_more_details_title'] = $validated['contact_more_details_title'] ?? 'More Contact Details';
        $publicPage['map_embed_title_text'] = $validated['map_embed_title_text'] ?? 'School map';
        $publicPage['submenu_description_fallback_template'] = $validated['submenu_description_fallback_template'] ?? 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.';
        $publicPage['contact_status_unavailable_text'] = $validated['contact_status_unavailable_text'] ?? 'Contact form is currently unavailable. Please try again later.';
        $publicPage['contact_status_recipient_missing_text'] = $validated['contact_status_recipient_missing_text'] ?? 'Contact recipient is not configured by admin yet.';
        $publicPage['contact_status_send_error_text'] = $validated['contact_status_send_error_text'] ?? 'Message could not be sent right now. Please try again shortly.';
        $publicPage['contact_status_success_text'] = $validated['contact_status_success_text'] ?? 'Thank you. Your message has been received. Our team will contact you shortly.';
        $publicPage['legal_effective_date'] = trim((string) ($validated['legal_effective_date'] ?? ''));
        $publicPage['privacy_policy_title'] = trim((string) ($validated['privacy_policy_title'] ?? 'Privacy Policy')) ?: 'Privacy Policy';
        $publicPage['privacy_policy_intro'] = $validated['privacy_policy_intro'] ?? 'This Privacy Policy explains how we collect, use, store, and protect personal information in line with GDPR principles and Nigeria data protection obligations.';
        $publicPage['privacy_policy_content'] = $validated['privacy_policy_content'] ?? '';
        $publicPage['cookies_policy_title'] = trim((string) ($validated['cookies_policy_title'] ?? 'Cookies Policy')) ?: 'Cookies Policy';
        $publicPage['cookies_policy_intro'] = $validated['cookies_policy_intro'] ?? 'This Cookies Policy explains what cookies are, how this website uses them, and how visitors can accept or reject optional cookies.';
        $publicPage['cookies_policy_content'] = $validated['cookies_policy_content'] ?? '';
        $publicPage['cookie_banner_title'] = trim((string) ($validated['cookie_banner_title'] ?? 'Cookie Notice')) ?: 'Cookie Notice';
        $publicPage['cookie_banner_text'] = trim((string) ($validated['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.')) ?: 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.';
        $publicPage['cookie_banner_accept_text'] = trim((string) ($validated['cookie_banner_accept_text'] ?? 'Accept Cookies')) ?: 'Accept Cookies';
        $publicPage['cookie_banner_reject_text'] = trim((string) ($validated['cookie_banner_reject_text'] ?? 'Reject Optional')) ?: 'Reject Optional';
        $publicPage['submenu_highlight_one_title'] = $validated['submenu_highlight_one_title'] ?? 'What Students Gain';
        $publicPage['submenu_highlight_one_text'] = $validated['submenu_highlight_one_text'] ?? 'Learners receive practical support, clear expectations, and measurable progress across this focus area.';
        $publicPage['submenu_highlight_two_title'] = $validated['submenu_highlight_two_title'] ?? 'How We Deliver';
        $publicPage['submenu_highlight_two_text'] = $validated['submenu_highlight_two_text'] ?? 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.';
        $publicPage['submenu_primary_button_text'] = $validated['submenu_primary_button_text'] ?? 'Start Admission';
        $publicPage['submenu_back_button_prefix'] = $validated['submenu_back_button_prefix'] ?? 'Back to';
        $publicPage['submenu_more_in_prefix'] = $validated['submenu_more_in_prefix'] ?? 'More In';

        $publicPage['programs'] = PublicPageContent::linesToItems($validated['program_items_text'] ?? '');
        $publicPage['admissions'] = PublicPageContent::linesToItems($validated['admissions_items_text'] ?? '');
        $publicPage['academics'] = PublicPageContent::linesToItems($validated['academics_items_text'] ?? '');
        $publicPage['student_life'] = PublicPageContent::linesToItems($validated['student_life_items_text'] ?? '');
        $publicPage['contact_items'] = PublicPageContent::linesToItems($validated['contact_items_text'] ?? '');
        $legacyAboutSource = array_key_exists('about_items_text', $validated)
            ? (string) ($validated['about_items_text'] ?? '')
            : PublicPageContent::itemsToLines($publicPage['about'] ?? []);
        $legacyAbout = PublicPageContent::linesToItems($legacyAboutSource);
        $legacyParentsSource = array_key_exists('parents_items_text', $validated)
            ? (string) ($validated['parents_items_text'] ?? '')
            : PublicPageContent::itemsToLines($publicPage['parents'] ?? []);
        $legacyParents = PublicPageContent::linesToItems($legacyParentsSource);
        $legacyWhyChooseUs = PublicPageContent::linesToArray($validated['why_choose_us'] ?? '');

        $publicPage['whatsapp'] = $validated['whatsapp'] ?? '';
        $publicPage['visit_booking_url'] = $validated['visit_booking_url'] ?? '';
        $publicPage['map_embed_url'] = $validated['map_embed_url'] ?? '';

        $publicPage['metrics'] = [
            ['value' => $validated['metric_1_value'] ?? '', 'label' => $validated['metric_1_label'] ?? ''],
            ['value' => $validated['metric_2_value'] ?? '', 'label' => $validated['metric_2_label'] ?? ''],
            ['value' => $validated['metric_3_value'] ?? '', 'label' => $validated['metric_3_label'] ?? ''],
            ['value' => $validated['metric_4_value'] ?? '', 'label' => $validated['metric_4_label'] ?? ''],
        ];

        $publicPage['facilities'] = PublicPageContent::linesToArray($validated['facilities'] ?? '');
        $publicPage['admission_steps'] = PublicPageContent::linesToArray($validated['admission_steps'] ?? '');
        $publicPage['footer_description'] = $validated['footer_description'] ?? '';
        $publicPage['footer_contact_address'] = $validated['footer_contact_address'] ?? '';
        $publicPage['footer_contact_phone'] = $validated['footer_contact_phone'] ?? '';
        $publicPage['footer_contact_email'] = $validated['footer_contact_email'] ?? '';
        $publicPage['footer_quick_links'] = PublicPageContent::linesToItems($validated['footer_quick_links_text'] ?? '');
        $publicPage['footer_resources'] = PublicPageContent::linesToItems($validated['footer_resources_text'] ?? '');
        $publicPage['footer_social_links'] = PublicPageContent::linesToItems($validated['footer_social_links_text'] ?? '');

        $slides = [];
        $existingSlides = $publicPage['hero_slides'] ?? [];

        for ($i = 1; $i <= 3; $i++) {
            $index = $i - 1;
            $existing = $existingSlides[$index] ?? [];
            $path = $existing['path'] ?? null;

            if ($request->boolean("remove_hero_slide_{$i}") && $path) {
                Storage::disk('public')->delete($path);
                $path = null;
            }

            if ($request->hasFile("hero_slide_{$i}")) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }

                $path = $request->file("hero_slide_{$i}")->store('schools/public/hero', 'public');
            }

            $caption = $validated["hero_slide_{$i}_caption"] ?? ($existing['caption'] ?? '');

            if ($path) {
                $slides[] = [
                    'path' => $path,
                    'caption' => $caption,
                ];
            }
        }

        $publicPage['hero_slides'] = $slides;
        $existingAcademicsVisuals = collect($publicPage['academics_visuals'] ?? [])
            ->map(function ($item) {
                if (is_array($item)) {
                    return trim((string) ($item['image'] ?? ($item['path'] ?? '')));
                }

                return trim((string) $item);
            })
            ->filter()
            ->values()
            ->all();
        $academicsVisuals = [];

        for ($i = 1; $i <= 2; $i++) {
            $index = $i - 1;
            $imagePath = $existingAcademicsVisuals[$index] ?? null;

            if ($request->boolean("remove_academic_image_{$i}") && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("academic_image_{$i}")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file("academic_image_{$i}")->store('schools/public/academics', 'public');
            }

            if ($imagePath) {
                $academicsVisuals[] = $imagePath;
            }
        }

        $publicPage['academics_visuals'] = $academicsVisuals;

        $existingParentsBanners = $publicPage['parents_banners'] ?? [];
        $parentsBanners = [];

        for ($i = 1; $i <= 6; $i++) {
            $index = $i - 1;
            $existing = $existingParentsBanners[$index] ?? [];
            $imagePath = $existing['image'] ?? ($existing['path'] ?? null);

            if ($request->boolean("remove_parent_banner_{$i}") && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("parent_banner_{$i}_image")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file("parent_banner_{$i}_image")->store('schools/public/parents', 'public');
            }

            $title = trim((string) ($validated["parent_banner_{$i}_title"] ?? ($existing['title'] ?? '')));
            $description = trim((string) ($validated["parent_banner_{$i}_description"] ?? ($existing['description'] ?? '')));

            if ($imagePath || $title !== '' || $description !== '') {
                $parentsBanners[] = [
                    'image' => $imagePath,
                    'title' => $title,
                    'description' => $description,
                ];
            }
        }

        if (empty($parentsBanners) && !empty($legacyParents)) {
            $parentsBanners = collect($legacyParents)
                ->map(function (array $item) {
                    return [
                        'image' => null,
                        'title' => trim((string) ($item['title'] ?? '')),
                        'description' => trim((string) ($item['description'] ?? '')),
                    ];
                })
                ->filter(function (array $item) {
                    return $item['title'] !== '' || $item['description'] !== '';
                })
                ->values()
                ->all();
        }

        $publicPage['parents_banners'] = $parentsBanners;
        $publicPage['parents'] = collect($parentsBanners)
            ->map(function (array $item) {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return $item['title'] !== '';
            })
            ->values()
            ->all();

        $existingWhyChooseUsBanners = $publicPage['why_choose_us_banners'] ?? [];
        $whyChooseUsBanners = [];

        for ($i = 1; $i <= 4; $i++) {
            $index = $i - 1;
            $existing = $existingWhyChooseUsBanners[$index] ?? [];
            $imagePath = $existing['image'] ?? ($existing['path'] ?? null);

            if ($request->boolean("remove_why_banner_{$i}") && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("why_banner_{$i}_image")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file("why_banner_{$i}_image")->store('schools/public/why-choose-us', 'public');
            }

            $defaultTitle = $existing['title'] ?? ('Why Choose Us ' . $i);
            $title = trim((string) ($validated["why_banner_{$i}_title"] ?? $defaultTitle));
            $description = trim((string) ($validated["why_banner_{$i}_description"] ?? ($existing['description'] ?? '')));

            if ($imagePath || $title !== '' || $description !== '') {
                $whyChooseUsBanners[] = [
                    'image' => $imagePath,
                    'title' => $title,
                    'description' => $description,
                ];
            }
        }

        if (empty($whyChooseUsBanners) && !empty($legacyWhyChooseUs)) {
            $whyChooseUsBanners = collect($legacyWhyChooseUs)
                ->map(function ($text, int $index) {
                    return [
                        'image' => null,
                        'title' => 'Why Choose Us ' . ($index + 1),
                        'description' => trim((string) $text),
                    ];
                })
                ->filter(function (array $item) {
                    return $item['description'] !== '';
                })
                ->values()
                ->all();
        }

        $publicPage['why_choose_us_banners'] = $whyChooseUsBanners;
        $publicPage['why_choose_us'] = collect($whyChooseUsBanners)
            ->map(fn (array $item) => trim((string) ($item['description'] ?? '')))
            ->filter()
            ->values()
            ->all();

        $existingAboutBanners = $publicPage['about_banners'] ?? [];
        $aboutBanners = [];

        for ($i = 1; $i <= 6; $i++) {
            $index = $i - 1;
            $existing = $existingAboutBanners[$index] ?? [];
            $imagePath = $existing['image'] ?? ($existing['path'] ?? null);

            if ($request->boolean("remove_about_banner_{$i}") && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("about_banner_{$i}_image")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file("about_banner_{$i}_image")->store('schools/public/about', 'public');
            }

            $title = trim((string) ($validated["about_banner_{$i}_title"] ?? ($existing['title'] ?? '')));
            $description = trim((string) ($validated["about_banner_{$i}_description"] ?? ($existing['description'] ?? '')));

            if ($imagePath || $title !== '' || $description !== '') {
                $aboutBanners[] = [
                    'image' => $imagePath,
                    'title' => $title,
                    'description' => $description,
                ];
            }
        }

        if (empty($aboutBanners) && !empty($legacyAbout)) {
            $aboutBanners = collect($legacyAbout)
                ->map(function (array $item) {
                    return [
                        'image' => null,
                        'title' => trim((string) ($item['title'] ?? '')),
                        'description' => trim((string) ($item['description'] ?? '')),
                    ];
                })
                ->filter(function (array $item) {
                    return $item['title'] !== '' || $item['description'] !== '';
                })
                ->values()
                ->all();
        }

        $publicPage['about_banners'] = $aboutBanners;
        $publicPage['about'] = collect($aboutBanners)
            ->map(function (array $item) {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(function (array $item) {
                return $item['title'] !== '';
            })
            ->values()
            ->all();

        $existingTeacherMarqueeItems = $publicPage['teachers_marquee'] ?? [];
        $existingTeacherImagePaths = collect($existingTeacherMarqueeItems)
            ->map(function ($item) {
                return trim((string) ($item['image'] ?? ($item['path'] ?? '')));
            })
            ->filter()
            ->values();

        $teacherMarqueeItems = [];
        $submittedTeacherRows = $validated['teacher_marquee'] ?? [];

        foreach ($submittedTeacherRows as $index => $row) {
            $existingImage = trim((string) ($row['existing_image'] ?? ''));
            $imagePath = $existingImage !== '' ? $existingImage : null;
            $removeImage = !empty($row['remove_image']);
            $removeRow = !empty($row['remove_row']);

            if (($removeImage || $removeRow) && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("teacher_marquee.{$index}.image")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file("teacher_marquee.{$index}.image")->store('schools/public/teachers-marquee', 'public');
            }

            if ($removeRow) {
                continue;
            }

            $name = trim((string) ($row['name'] ?? ''));
            $role = trim((string) ($row['role'] ?? ''));

            if ($imagePath || $name !== '' || $role !== '') {
                $teacherMarqueeItems[] = [
                    'image' => $imagePath,
                    'name' => $name,
                    'role' => $role,
                ];
            }
        }

        $keptTeacherImagePaths = collect($teacherMarqueeItems)
            ->pluck('image')
            ->filter()
            ->values();

        $staleTeacherImagePaths = $existingTeacherImagePaths
            ->diff($keptTeacherImagePaths)
            ->values();

        foreach ($staleTeacherImagePaths as $staleImagePath) {
            Storage::disk('public')->delete($staleImagePath);
        }

        $publicPage['teachers_marquee'] = $teacherMarqueeItems;

        $existingFooterLogo = $publicPage['footer_logo'] ?? null;
        if ($request->boolean('remove_footer_logo') && $existingFooterLogo) {
            Storage::disk('public')->delete($existingFooterLogo);
            $existingFooterLogo = null;
        }
        if ($request->hasFile('footer_logo')) {
            if ($existingFooterLogo) {
                Storage::disk('public')->delete($existingFooterLogo);
            }
            $existingFooterLogo = $request->file('footer_logo')->store('schools/public/footer', 'public');
        }
        $publicPage['footer_logo'] = $existingFooterLogo;

        $existingContactHero = trim((string) ($publicPage['contact_hero_image'] ?? ''));
        if ($request->boolean('remove_contact_hero_image') && $existingContactHero !== '') {
            Storage::disk('public')->delete($existingContactHero);
            $existingContactHero = '';
        }
        if ($request->hasFile('contact_hero_image')) {
            if ($existingContactHero !== '') {
                Storage::disk('public')->delete($existingContactHero);
            }
            $existingContactHero = $request->file('contact_hero_image')->store('schools/public/contact-hero', 'public');
        }
        $publicPage['contact_hero_image'] = $existingContactHero;

        $settings['public_page'] = $publicPage;

        $school->update(['settings' => $settings]);

        return back()->with('success', 'Public page content updated successfully.');
    }

    public function resetThemeDefaults()
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();
        $settings = $school->settings ?? [];
        $publicPage = PublicPageContent::forSchool($school);
        $defaults = PublicPageContent::defaults($school);

        $publicPage['site_background_color'] = $defaults['site_background_color'] ?? '#F8FAFC';
        $publicPage['primary_color'] = $defaults['primary_color'] ?? '#2D1D5C';
        $publicPage['secondary_color'] = $defaults['secondary_color'] ?? '#DFE753';
        $publicPage['heading_text_color'] = $defaults['heading_text_color'] ?? '#0F172A';
        $publicPage['body_text_color'] = $defaults['body_text_color'] ?? '#475569';
        $publicPage['surface_color'] = $defaults['surface_color'] ?? '#FFFFFF';
        $publicPage['soft_surface_color'] = $defaults['soft_surface_color'] ?? '#EEF6FF';
        $publicPage['theme_style'] = $defaults['theme_style'] ?? 'modern-grid';
        $publicPage['header_bg_color'] = $defaults['header_bg_color'] ?? ($defaults['primary_color'] ?? '#2D1D5C');
        $publicPage['footer_bg_color'] = $defaults['footer_bg_color'] ?? ($defaults['primary_color'] ?? '#2D1D5C');
        $publicPage['footer_separator_color'] = $defaults['footer_separator_color'] ?? ($defaults['secondary_color'] ?? '#DFE753');

        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'Theme colors reset to default values.');
    }

    public function saveFaqs(Request $request)
    {
        $this->ensureAdminAccess();

        $school = auth()->user()->school ?? SchoolContext::current();

        $validated = $request->validate([
            'categories'                  => 'nullable|array|max:20',
            'categories.*.id'             => 'required|string|max:80|regex:/^[a-z0-9\-]+$/',
            'categories.*.label'          => 'required|string|max:120',
            'categories.*.items'          => 'nullable|array|max:50',
            'categories.*.items.*.q'      => 'required|string|max:400',
            'categories.*.items.*.a'      => 'required|string|max:3000',
        ]);

        $faqs = collect($validated['categories'] ?? [])
            ->map(function (array $cat) {
                $id    = trim((string) ($cat['id'] ?? ''));
                $label = trim((string) ($cat['label'] ?? ''));
                if ($id === '' || $label === '') {
                    return null;
                }
                $items = collect($cat['items'] ?? [])
                    ->map(function (array $item) {
                        $q = trim(strip_tags((string) ($item['q'] ?? '')));
                        $a = RichText::sanitize((string) ($item['a'] ?? ''));
                        return ($q !== '' && $a !== '') ? compact('q', 'a') : null;
                    })
                    ->filter()
                    ->values()
                    ->all();

                return ['id' => $id, 'label' => $label, 'items' => $items];
            })
            ->filter()
            ->values()
            ->all();

        $settings = $school->settings ?? [];
        $publicPage = (array) ($settings['public_page'] ?? []);
        $publicPage['faqs'] = $faqs;
        $settings['public_page'] = $publicPage;
        $school->update(['settings' => $settings]);

        return back()->with('success', 'FAQs saved successfully.');
    }

    private function ensureAdminAccess(): void
    {
        $role = auth()->user()?->role?->value;

        abort_unless(in_array($role, [UserRole::SUPER_ADMIN->value, UserRole::SCHOOL_ADMIN->value], true), 403);
    }

    private function normalizeSettingsPage(string $page): ?string
    {
        return array_key_exists($page, self::SETTINGS_PAGE_META) ? $page : null;
    }

    private function mergeMissingPublicPageInputs(Request $request, array $publicPage): void
    {
        $defaults = [
            'hero_badge_text' => $publicPage['hero_badge_text'] ?? '',
            'hero_title' => $publicPage['hero_title'] ?? '',
            'hero_subtitle' => $publicPage['hero_subtitle'] ?? '',
            'cta_primary_text' => $publicPage['cta_primary_text'] ?? '',
            'cta_secondary_text' => $publicPage['cta_secondary_text'] ?? '',
            'site_background_color' => $publicPage['site_background_color'] ?? '#F8FAFC',
            'primary_color' => $publicPage['primary_color'] ?? '#2D1D5C',
            'secondary_color' => $publicPage['secondary_color'] ?? '#DFE753',
            'heading_text_color' => $publicPage['heading_text_color'] ?? '#0F172A',
            'body_text_color' => $publicPage['body_text_color'] ?? '#475569',
            'surface_color' => $publicPage['surface_color'] ?? '#FFFFFF',
            'soft_surface_color' => $publicPage['soft_surface_color'] ?? '#EEF6FF',
            'theme_style' => $publicPage['theme_style'] ?? 'modern-grid',
            'header_bg_color' => $publicPage['header_bg_color'] ?? '#2D1D5C',
            'footer_bg_color' => $publicPage['footer_bg_color'] ?? '#2D1D5C',
            'footer_separator_color' => $publicPage['footer_separator_color'] ?? '#DFE753',
            'admission_session_text' => $publicPage['admission_session_text'] ?? '',
            'footer_note' => $publicPage['footer_note'] ?? '',
            'programs_intro' => $publicPage['programs_intro'] ?? '',
            'admissions_intro' => $publicPage['admissions_intro'] ?? '',
            'academics_intro' => $publicPage['academics_intro'] ?? '',
            'academics_support_text' => $publicPage['academics_support_text'] ?? '',
            'academic_highlight_1_title' => data_get($publicPage, 'academic_highlights.0.title', 'STEM-First Curriculum'),
            'academic_highlight_1_description' => data_get($publicPage, 'academic_highlights.0.description', 'Coding, robotics, and science labs integrated into junior and senior classes.'),
            'academic_highlight_2_title' => data_get($publicPage, 'academic_highlights.1.title', 'Student Leadership'),
            'academic_highlight_2_description' => data_get($publicPage, 'academic_highlights.1.description', 'Public speaking, media, and entrepreneurship clubs with measurable outcomes.'),
            'facilities_intro' => $publicPage['facilities_intro'] ?? '',
            'about_intro' => $publicPage['about_intro'] ?? '',
            'student_life_intro' => $publicPage['student_life_intro'] ?? '',
            'parents_intro' => $publicPage['parents_intro'] ?? '',
            'contact_intro' => $publicPage['contact_intro'] ?? '',
            'why_choose_us_label' => $publicPage['why_choose_us_label'] ?? 'Why Choose Us',
            'why_choose_us_intro' => $publicPage['why_choose_us_intro'] ?? '',
            'teachers_marquee_label' => $publicPage['teachers_marquee_label'] ?? 'Our Teachers',
            'teachers_marquee_heading' => $publicPage['teachers_marquee_heading'] ?? 'Meet Our Teaching Team',
            'teachers_marquee_intro' => $publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.',
            'programs_label' => $publicPage['programs_label'] ?? 'Programs',
            'admissions_label' => $publicPage['admissions_label'] ?? 'Admissions',
            'admissions_process_label' => $publicPage['admissions_process_label'] ?? 'Admissions Process',
            'academics_label' => $publicPage['academics_label'] ?? 'Academic Excellence',
            'facilities_label' => $publicPage['facilities_label'] ?? 'Facilities',
            'about_label' => $publicPage['about_label'] ?? 'About Us',
            'student_life_label' => $publicPage['student_life_label'] ?? 'Student Life',
            'parents_label' => $publicPage['parents_label'] ?? 'Parents',
            'contact_label' => $publicPage['contact_label'] ?? 'Contact',
            'header_apply_text' => $publicPage['header_apply_text'] ?? 'Apply',
            'header_portal_login_text' => $publicPage['header_portal_login_text'] ?? 'Portal Login',
            'mobile_apply_text' => $publicPage['mobile_apply_text'] ?? 'Apply Now',
            'mobile_portal_login_text' => $publicPage['mobile_portal_login_text'] ?? 'Portal Login',
            'hero_slider_placeholder_text' => $publicPage['hero_slider_placeholder_text'] ?? 'Upload hero slider images from Admin Settings to personalize this section.',
            'parents_portal_button_text' => $publicPage['parents_portal_button_text'] ?? 'Parent Portal Login',
            'testimonials_badge_text' => $publicPage['testimonials_badge_text'] ?? 'Testimonials',
            'testimonials_heading' => $publicPage['testimonials_heading'] ?? 'What Parents and Student Say',
            'testimonials_subheading' => $publicPage['testimonials_subheading'] ?? 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.',
            'testimonials_form_title' => $publicPage['testimonials_form_title'] ?? 'Share Your Testimonial',
            'testimonials_form_name_label' => $publicPage['testimonials_form_name_label'] ?? 'Full Name',
            'testimonials_form_name_placeholder' => $publicPage['testimonials_form_name_placeholder'] ?? 'Enter your full name',
            'testimonials_form_role_label' => $publicPage['testimonials_form_role_label'] ?? 'Role or Context',
            'testimonials_form_role_placeholder' => $publicPage['testimonials_form_role_placeholder'] ?? 'Parent, student, alumni, guardian, etc.',
            'testimonials_form_rating_label' => $publicPage['testimonials_form_rating_label'] ?? 'Rating',
            'testimonials_form_message_label' => $publicPage['testimonials_form_message_label'] ?? 'Your Testimonial',
            'testimonials_form_message_placeholder' => $publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...',
            'testimonials_form_submit_text' => $publicPage['testimonials_form_submit_text'] ?? 'Submit Testimonial',
            'testimonials_slider_title' => $publicPage['testimonials_slider_title'] ?? 'Approved Testimonials',
            'testimonials_empty_text' => $publicPage['testimonials_empty_text'] ?? 'No testimonials have been approved yet. Be the first to share your experience.',
            'testimonials_success_text' => $publicPage['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.',
            'testimonials_error_text' => $publicPage['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.',
            'quick_contact_label' => $publicPage['quick_contact_label'] ?? 'Quick Contact',
            'contact_phone_label' => $publicPage['contact_phone_label'] ?? 'Phone',
            'contact_whatsapp_label' => $publicPage['contact_whatsapp_label'] ?? 'WhatsApp',
            'contact_email_label' => $publicPage['contact_email_label'] ?? 'Email',
            'contact_address_label' => $publicPage['contact_address_label'] ?? 'Address',
            'visit_booking_button_text' => $publicPage['visit_booking_button_text'] ?? 'Visit Booking',
            'quick_apply_button_text' => $publicPage['quick_apply_button_text'] ?? 'Apply Now',
            'menu_overview_suffix' => $publicPage['menu_overview_suffix'] ?? 'Overview',
            'site_title_suffix' => $publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School',
            'mobile_menu_title' => $publicPage['mobile_menu_title'] ?? 'Menu',
            'footer_quick_links_title' => $publicPage['footer_quick_links_title'] ?? 'Quick Links',
            'footer_resources_title' => $publicPage['footer_resources_title'] ?? 'Resources',
            'footer_contact_title' => $publicPage['footer_contact_title'] ?? 'Contact',
            'contact_page_browser_title' => $publicPage['contact_page_browser_title'] ?? 'Contact Us',
            'contact_page_badge_text' => $publicPage['contact_page_badge_text'] ?? 'Contact Us',
            'contact_page_heading' => $publicPage['contact_page_heading'] ?? 'We are here to help you',
            'contact_page_subheading' => $publicPage['contact_page_subheading'] ?? 'Send us a message and our admissions or support team will respond as soon as possible.',
            'contact_form_title' => $publicPage['contact_form_title'] ?? 'Contact Us Form',
            'contact_form_full_name_label' => $publicPage['contact_form_full_name_label'] ?? 'Full Name',
            'contact_form_full_name_placeholder' => $publicPage['contact_form_full_name_placeholder'] ?? 'Enter your full name',
            'contact_form_email_label' => $publicPage['contact_form_email_label'] ?? 'Email',
            'contact_form_email_placeholder' => $publicPage['contact_form_email_placeholder'] ?? 'you@example.com',
            'contact_form_phone_label' => $publicPage['contact_form_phone_label'] ?? 'Phone Number',
            'contact_form_phone_placeholder' => $publicPage['contact_form_phone_placeholder'] ?? '+234...',
            'contact_form_subject_label' => $publicPage['contact_form_subject_label'] ?? 'Subject',
            'contact_form_subject_placeholder' => $publicPage['contact_form_subject_placeholder'] ?? 'How can we help?',
            'contact_form_message_label' => $publicPage['contact_form_message_label'] ?? 'Message',
            'contact_form_message_placeholder' => $publicPage['contact_form_message_placeholder'] ?? 'Write your message...',
            'contact_form_submit_text' => $publicPage['contact_form_submit_text'] ?? 'Send Message',
            'contact_info_title' => $publicPage['contact_info_title'] ?? 'Contact Information',
            'contact_not_provided_text' => $publicPage['contact_not_provided_text'] ?? 'Not provided yet',
            'contact_more_details_title' => $publicPage['contact_more_details_title'] ?? 'More Contact Details',
            'map_embed_title_text' => $publicPage['map_embed_title_text'] ?? 'School map',
            'submenu_description_fallback_template' => $publicPage['submenu_description_fallback_template'] ?? 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.',
            'contact_status_unavailable_text' => $publicPage['contact_status_unavailable_text'] ?? 'Contact form is currently unavailable. Please try again later.',
            'contact_status_recipient_missing_text' => $publicPage['contact_status_recipient_missing_text'] ?? 'Contact recipient is not configured by admin yet.',
            'contact_status_send_error_text' => $publicPage['contact_status_send_error_text'] ?? 'Message could not be sent right now. Please try again shortly.',
            'contact_status_success_text' => $publicPage['contact_status_success_text'] ?? 'Thank you. Your message has been received. Our team will contact you shortly.',
            'legal_effective_date' => $publicPage['legal_effective_date'] ?? '',
            'privacy_policy_title' => $publicPage['privacy_policy_title'] ?? 'Privacy Policy',
            'privacy_policy_intro' => $publicPage['privacy_policy_intro'] ?? 'This Privacy Policy explains how we collect, use, store, and protect personal information in line with GDPR principles and Nigeria data protection obligations.',
            'privacy_policy_content' => $publicPage['privacy_policy_content'] ?? '',
            'cookies_policy_title' => $publicPage['cookies_policy_title'] ?? 'Cookies Policy',
            'cookies_policy_intro' => $publicPage['cookies_policy_intro'] ?? 'This Cookies Policy explains what cookies are, how this website uses them, and how visitors can accept or reject optional cookies.',
            'cookies_policy_content' => $publicPage['cookies_policy_content'] ?? '',
            'cookie_banner_title' => $publicPage['cookie_banner_title'] ?? 'Cookie Notice',
            'cookie_banner_text' => $publicPage['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.',
            'cookie_banner_accept_text' => $publicPage['cookie_banner_accept_text'] ?? 'Accept Cookies',
            'cookie_banner_reject_text' => $publicPage['cookie_banner_reject_text'] ?? 'Reject Optional',
            'submenu_highlight_one_title' => $publicPage['submenu_highlight_one_title'] ?? 'What Students Gain',
            'submenu_highlight_one_text' => $publicPage['submenu_highlight_one_text'] ?? 'Learners receive practical support, clear expectations, and measurable progress across this focus area.',
            'submenu_highlight_two_title' => $publicPage['submenu_highlight_two_title'] ?? 'How We Deliver',
            'submenu_highlight_two_text' => $publicPage['submenu_highlight_two_text'] ?? 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.',
            'submenu_primary_button_text' => $publicPage['submenu_primary_button_text'] ?? 'Start Admission',
            'submenu_back_button_prefix' => $publicPage['submenu_back_button_prefix'] ?? 'Back to',
            'submenu_more_in_prefix' => $publicPage['submenu_more_in_prefix'] ?? 'More In',
            'whatsapp' => $publicPage['whatsapp'] ?? '',
            'visit_booking_url' => $publicPage['visit_booking_url'] ?? '',
            'map_embed_url' => $publicPage['map_embed_url'] ?? '',
            'footer_description' => $publicPage['footer_description'] ?? '',
            'footer_contact_address' => $publicPage['footer_contact_address'] ?? '',
            'footer_contact_phone' => $publicPage['footer_contact_phone'] ?? '',
            'footer_contact_email' => $publicPage['footer_contact_email'] ?? '',
            'metric_1_value' => data_get($publicPage, 'metrics.0.value', ''),
            'metric_1_label' => data_get($publicPage, 'metrics.0.label', ''),
            'metric_2_value' => data_get($publicPage, 'metrics.1.value', ''),
            'metric_2_label' => data_get($publicPage, 'metrics.1.label', ''),
            'metric_3_value' => data_get($publicPage, 'metrics.2.value', ''),
            'metric_3_label' => data_get($publicPage, 'metrics.2.label', ''),
            'metric_4_value' => data_get($publicPage, 'metrics.3.value', ''),
            'metric_4_label' => data_get($publicPage, 'metrics.3.label', ''),
            'program_items_text' => PublicPageContent::itemsToLines($publicPage['programs'] ?? []),
            'admissions_items_text' => PublicPageContent::itemsToLines($publicPage['admissions'] ?? []),
            'academics_items_text' => PublicPageContent::itemsToLines($publicPage['academics'] ?? []),
            'about_items_text' => PublicPageContent::itemsToLines($publicPage['about'] ?? []),
            'student_life_items_text' => PublicPageContent::itemsToLines($publicPage['student_life'] ?? []),
            'parents_items_text' => PublicPageContent::itemsToLines($publicPage['parents'] ?? []),
            'contact_items_text' => PublicPageContent::itemsToLines($publicPage['contact_items'] ?? []),
            'footer_quick_links_text' => PublicPageContent::itemsToLines($publicPage['footer_quick_links'] ?? []),
            'footer_resources_text' => PublicPageContent::itemsToLines($publicPage['footer_resources'] ?? []),
            'footer_social_links_text' => PublicPageContent::itemsToLines($publicPage['footer_social_links'] ?? []),
            'why_choose_us' => PublicPageContent::arrayToLines($publicPage['why_choose_us'] ?? []),
            'facilities' => PublicPageContent::arrayToLines($publicPage['facilities'] ?? []),
            'admission_steps' => PublicPageContent::arrayToLines($publicPage['admission_steps'] ?? []),
        ];

        foreach ($defaults as $key => $value) {
            if (!$request->exists($key)) {
                $request->merge([$key => $value]);
            }
        }
    }

    private function configureSmtpMailer(array $smtp, string $fallbackFromName): void
    {
        $host = trim((string) ($smtp['host'] ?? ''));
        $port = (int) ($smtp['port'] ?? 0);
        $fromAddress = trim((string) ($smtp['from_address'] ?? ''));
        $encryption = trim((string) ($smtp['encryption'] ?? 'tls'));

        if ($host === '' || $port < 1 || $fromAddress === '') {
            throw new \RuntimeException('SMTP host, port, or from address is missing.');
        }

        $smtpPassword = trim((string) ($smtp['password'] ?? ''));
        if ($smtpPassword !== '') {
            try {
                $smtpPassword = Crypt::decryptString($smtpPassword);
            } catch (Throwable $exception) {
                // Keep backward compatibility for plain-text legacy SMTP passwords.
            }
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption === 'none' ? null : $encryption,
            'username' => trim((string) ($smtp['username'] ?? '')) ?: null,
            'password' => $smtpPassword ?: null,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ]);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', trim((string) ($smtp['from_name'] ?? '')) ?: $fallbackFromName);

        app('mail.manager')->purge('smtp');
    }
}
