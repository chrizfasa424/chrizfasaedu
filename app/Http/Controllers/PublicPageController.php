<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Testimonial;
use App\Support\DomainHelper;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PublicPageController extends Controller
{
    public function index(Request $request)
    {
        $school = $this->resolveSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $approvedTestimonials = collect();

        if ($school) {
            $approvedTestimonials = Testimonial::query()
                ->with('student')
                ->where('school_id', $school->id)
                ->where('status', 'approved')
                ->latest('reviewed_at')
                ->latest('id')
                ->take(18)
                ->get();
        }

        return view('welcome', compact('school', 'publicPage', 'approvedTestimonials'));
    }

    public function submenuPage(Request $request, string $section, string $slug)
    {
        $school = $this->resolveSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $catalog = $this->buildMenuCatalog($publicPage);
        $sectionData = $catalog[$section] ?? null;

        if (!$sectionData) {
            abort(404);
        }

        $item = collect($sectionData['items'])->firstWhere('slug', $slug);

        if (!$item) {
            abort(404);
        }

        $schoolName = $school?->name ?? 'ChrizFasa Academy';

        return view('public.submenu', [
            'school' => $school,
            'schoolName' => $schoolName,
            'publicPage' => $publicPage,
            'sectionKey' => $section,
            'sectionLabel' => $sectionData['label'],
            'item' => $item,
            'menuCatalog' => $catalog,
        ]);
    }

    public function contactPage(Request $request)
    {
        $school = $this->resolveSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $catalog = $this->buildMenuCatalog($publicPage);
        $schoolName = $school?->name ?? 'ChrizFasa Academy';
        $contactItems = $catalog['contact']['items'] ?? [];

        return view('public.contact', [
            'school' => $school,
            'schoolName' => $schoolName,
            'publicPage' => $publicPage,
            'menuCatalog' => $catalog,
            'contactItems' => $contactItems,
        ]);
    }

    public function privacyPage(Request $request)
    {
        $school = $this->resolveSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $schoolName = $school?->name ?? 'ChrizFasa Academy';

        return view('public.privacy', [
            'school' => $school,
            'schoolName' => $schoolName,
            'publicPage' => $publicPage,
            'effectiveDate' => now()->toFormattedDateString(),
        ]);
    }

    public function cookiesPage(Request $request)
    {
        $school = $this->resolveSchool($request);
        $publicPage = PublicPageContent::forSchool($school);
        $schoolName = $school?->name ?? 'ChrizFasa Academy';

        return view('public.cookies', [
            'school' => $school,
            'schoolName' => $schoolName,
            'publicPage' => $publicPage,
            'effectiveDate' => now()->toFormattedDateString(),
        ]);
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone_number' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $school = $this->resolveSchool($request);
        $schoolName = $school?->name ?? config('app.name', 'School');
        $publicPage = PublicPageContent::forSchool($school);
        $smtp = (array) data_get($school?->settings, 'smtp', []);
        $recipient = trim((string) ($smtp['to_address'] ?? ($school?->email ?? '')));
        $contactUnavailableText = trim((string) ($publicPage['contact_status_unavailable_text'] ?? 'Contact form is currently unavailable. Please try again later.'));
        $contactRecipientMissingText = trim((string) ($publicPage['contact_status_recipient_missing_text'] ?? 'Contact recipient is not configured by admin yet.'));
        $contactSendErrorText = trim((string) ($publicPage['contact_status_send_error_text'] ?? 'Message could not be sent right now. Please try again shortly.'));
        $contactSuccessText = trim((string) ($publicPage['contact_status_success_text'] ?? 'Thank you. Your message has been received. Our team will contact you shortly.'));

        if (!($smtp['enabled'] ?? false)) {
            return back()
                ->withErrors(['contact_form' => $contactUnavailableText !== '' ? $contactUnavailableText : 'Contact form is currently unavailable. Please try again later.'])
                ->withInput();
        }

        if ($recipient === '') {
            return back()
                ->withErrors(['contact_form' => $contactRecipientMissingText !== '' ? $contactRecipientMissingText : 'Contact recipient is not configured by admin yet.'])
                ->withInput();
        }

        try {
            $this->configureSmtpForSchool($smtp, $schoolName);

            Mail::send('emails.public-contact', [
                'payload' => $validated,
                'school' => $school,
                'schoolName' => $schoolName,
                'submittedAt' => now(),
                'requestIp' => $request->ip(),
            ], function ($message) use ($validated, $recipient, $schoolName) {
                $message->to($recipient)
                    ->subject("New Contact Message - {$schoolName}")
                    ->replyTo($validated['email'], $validated['full_name']);
            });
        } catch (Throwable $exception) {
            Log::error('Public contact email send failed', [
                'school_id' => $school?->id,
                'host' => $request->getHost(),
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withErrors(['contact_form' => $contactSendErrorText !== '' ? $contactSendErrorText : 'Message could not be sent right now. Please try again shortly.'])
                ->withInput();
        }

        return redirect()
            ->route('public.contact')
            ->with('contact_success', $contactSuccessText !== '' ? $contactSuccessText : 'Thank you. Your message has been received. Our team will contact you shortly.');
    }

    public function submitTestimonial(Request $request)
    {
        $school = $this->resolveSchool($request);
        if (!$school) {
            abort(404);
        }

        $publicPage = PublicPageContent::forSchool($school);
        $testimonialSuccessText = trim((string) ($publicPage['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.'));
        $testimonialErrorText = trim((string) ($publicPage['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.'));

        // Honeypot anti-bot field.
        if (trim((string) $request->input('website', '')) !== '') {
            return redirect()
                ->to(route('public.home') . '#testimonials')
                ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'role_title' => ['nullable', 'string', 'max:140'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'message' => ['required', 'string', 'min:20', 'max:1200'],
            'started_at' => ['required', 'integer'],
            'website' => ['nullable', 'max:0'],
        ]);

        $startedAt = (int) $validated['started_at'];
        if ($startedAt <= 0 || (now()->timestamp - $startedAt) < 3) {
            return redirect()
                ->to(route('public.home') . '#testimonials')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        $sanitize = static function (?string $value): string {
            return trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');
        };

        $fullName = $sanitize($validated['full_name']);
        $roleTitle = $sanitize($validated['role_title'] ?? '');
        $message = $sanitize($validated['message']);

        if ($fullName === '' || $message === '') {
            return redirect()
                ->to(route('public.home') . '#testimonials')
                ->withErrors(['testimonial_form' => $testimonialErrorText !== '' ? $testimonialErrorText : 'Unable to submit testimonial. Please try again.'])
                ->withInput();
        }

        if (preg_match('/https?:\/\/|www\./i', $message)) {
            return redirect()
                ->to(route('public.home') . '#testimonials')
                ->withErrors(['testimonial_form' => 'Links are not allowed in testimonials.'])
                ->withInput();
        }

        Testimonial::query()->create([
            'school_id' => $school->id,
            'full_name' => $fullName,
            'role_title' => $roleTitle,
            'rating' => (int) $validated['rating'],
            'message' => $message,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        return redirect()
            ->to(route('public.home') . '#testimonials')
            ->with('testimonial_success', $testimonialSuccessText !== '' ? $testimonialSuccessText : 'Thank you for your testimonial. It has been submitted for admin review.');
    }

    private function configureSmtpForSchool(array $smtp, string $schoolName): void
    {
        $host = trim((string) ($smtp['host'] ?? ''));
        $port = (int) ($smtp['port'] ?? 0);
        $fromAddress = trim((string) ($smtp['from_address'] ?? ''));
        $encryption = trim((string) ($smtp['encryption'] ?? 'tls'));

        if ($host === '' || $port < 1 || $fromAddress === '') {
            throw new \RuntimeException('SMTP host, port, or from address is missing.');
        }

        $normalizedEncryption = $encryption === 'none' ? null : $encryption;
        $smtpPassword = trim((string) ($smtp['password'] ?? ''));

        if ($smtpPassword !== '') {
            try {
                $smtpPassword = Crypt::decryptString($smtpPassword);
            } catch (Throwable $exception) {
                // Backward compatibility for previously stored plain text SMTP passwords.
            }
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'encryption' => $normalizedEncryption,
            'username' => trim((string) ($smtp['username'] ?? '')) ?: null,
            'password' => $smtpPassword ?: null,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ]);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', trim((string) ($smtp['from_name'] ?? '')) ?: $schoolName);

        app('mail.manager')->purge('smtp');
    }

    private function resolveSchool(Request $request): ?School
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return SchoolContext::current($request);
        }

        $host = $request->getHost();
        $normalizedHost = DomainHelper::normalize($host);

        if ($normalizedHost) {
            $matchedSchool = School::query()
                ->where('is_active', true)
                ->where('domain', $normalizedHost)
                ->first();

            if ($matchedSchool) {
                return $matchedSchool;
            }
        }

        return School::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('domain')->orWhere('domain', '');
            })
            ->orderBy('id')
            ->first();
    }

    private function buildMenuCatalog(array $publicPage): array
    {
        $aboutSource = $publicPage['about'] ?? [];
        if (empty($aboutSource) && !empty($publicPage['about_banners'])) {
            $aboutSource = collect($publicPage['about_banners'])
                ->map(function ($item) {
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
        }

        $parentsSource = $publicPage['parents'] ?? [];
        if (empty($parentsSource) && !empty($publicPage['parents_banners'])) {
            $parentsSource = collect($publicPage['parents_banners'])
                ->map(function ($item) {
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
        }

        $sectionSources = [
            'programs' => ['label' => trim((string) ($publicPage['programs_label'] ?? 'Programs')) ?: 'Programs', 'items' => $publicPage['programs'] ?? []],
            'admissions' => ['label' => trim((string) ($publicPage['admissions_label'] ?? 'Admissions')) ?: 'Admissions', 'items' => $publicPage['admissions'] ?? []],
            'academics' => ['label' => trim((string) ($publicPage['academics_label'] ?? 'Academics')) ?: 'Academics', 'items' => $publicPage['academics'] ?? []],
            'facilities' => ['label' => trim((string) ($publicPage['facilities_label'] ?? 'Facilities')) ?: 'Facilities', 'items' => $publicPage['facilities'] ?? []],
            'about' => ['label' => trim((string) ($publicPage['about_label'] ?? 'About Us')) ?: 'About Us', 'items' => $aboutSource],
            'student-life' => ['label' => trim((string) ($publicPage['student_life_label'] ?? 'Student Life')) ?: 'Student Life', 'items' => $publicPage['student_life'] ?? []],
            'parents' => ['label' => trim((string) ($publicPage['parents_label'] ?? 'Parents')) ?: 'Parents', 'items' => $parentsSource],
            'contact' => ['label' => trim((string) ($publicPage['contact_label'] ?? 'Contact')) ?: 'Contact', 'items' => $publicPage['contact_items'] ?? []],
        ];
        $submenuDescriptionFallbackTemplate = trim((string) ($publicPage['submenu_description_fallback_template'] ?? 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.'));
        $submenuDescriptionFallbackTemplate = $submenuDescriptionFallbackTemplate !== ''
            ? $submenuDescriptionFallbackTemplate
            : 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.';

        $catalog = [];
        $submenuImages = (array) ($publicPage['submenu_images'] ?? []);
        $submenuContent = (array) ($publicPage['submenu_content'] ?? []);

        foreach ($sectionSources as $key => $source) {
            $sectionImages = (array) ($submenuImages[$key] ?? []);
            $sectionContent = (array) ($submenuContent[$key] ?? []);
            $items = [];
            foreach ($source['items'] as $rawItem) {
                $title = '';
                $description = '';

                if (is_array($rawItem)) {
                    $title = trim((string) ($rawItem['title'] ?? ''));
                    $description = trim((string) ($rawItem['description'] ?? ''));
                } else {
                    $title = trim((string) $rawItem);
                }

                if ($title === '') {
                    continue;
                }

                $slug = Str::slug($title);
                $normalized = preg_replace('/\s+/', ' ', $description ?? '');
                $normalized = is_string($normalized) ? trim($normalized) : '';

                $fallbackDescription = str_replace('{title}', $title, $submenuDescriptionFallbackTemplate);
                $perItemContent = (array) ($sectionContent[$slug] ?? []);

                $items[] = [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $normalized !== ''
                        ? Str::limit($normalized, 340)
                        : $fallbackDescription,
                    'image' => trim((string) ($sectionImages[$slug] ?? '')),
                    'rich_description' => trim((string) ($perItemContent['description'] ?? '')),
                    'highlight_one_title' => trim((string) ($perItemContent['highlight_one_title'] ?? '')),
                    'highlight_one_text' => trim((string) ($perItemContent['highlight_one_text'] ?? '')),
                    'highlight_two_title' => trim((string) ($perItemContent['highlight_two_title'] ?? '')),
                    'highlight_two_text' => trim((string) ($perItemContent['highlight_two_text'] ?? '')),
                    'image_one' => trim((string) ($perItemContent['image_one'] ?? '')),
                    'image_two' => trim((string) ($perItemContent['image_two'] ?? '')),
                ];
            }

            $catalog[$key] = [
                'label' => $source['label'],
                'items' => $items,
            ];
        }

        return $catalog;
    }
}
