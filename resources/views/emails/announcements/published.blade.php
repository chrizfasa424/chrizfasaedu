@php
    $resolvedSchoolName = trim((string) ($schoolName ?? ($school?->name ?? config('app.name', 'School'))));
    $publicPage = \App\Support\PublicPageContent::forSchool($school);
    $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    $primaryColor = (string) data_get($theme, 'primary.500', '#25333E');
    $secondaryColor = (string) data_get($theme, 'secondary.500', '#DFE753');

    $contactEmail = trim((string) ($publicPage['footer_contact_email'] ?? ($school?->email ?? '')));
    $contactPhone = trim((string) ($publicPage['footer_contact_phone'] ?? ($school?->phone ?? '')));
    $contactAddress = trim((string) ($publicPage['footer_contact_address'] ?? ($school?->address ?? '')));
    $websiteUrl = trim((string) config('app.url', ''));

    $announcementBody = trim(strip_tags((string) ($announcement->body ?? '')));
@endphp

<x-email-layout
    title="New Announcement"
    :school="$school"
    :school-name="$resolvedSchoolName"
    :mailer-message="isset($message) ? $message : null"
    :preview-text="$previewText ?? 'A new announcement has been published for you.'"
>
    <p style="margin:0 0 14px;color:#0f172a;font-size:28px;font-weight:800;line-height:1.2;">
        Hello {{ $recipientName }},
    </p>

    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
        A new announcement has been published for your account. Please review the details below.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:16px 18px;">
                <table width="100%" cellpadding="0" cellspacing="8">
                    <tr>
                        <td style="width:38%;color:#64748b;font-size:13px;">Title</td>
                        <td style="color:#0f172a;font-size:14px;font-weight:700;">{{ $announcement->title }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:13px;">Type</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $typeLabel }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:13px;">Priority</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $priorityLabel }}</td>
                    </tr>
                    @if($announcement->published_at)
                        <tr>
                            <td style="color:#64748b;font-size:13px;">Published</td>
                            <td style="color:#0f172a;font-size:14px;">{{ $announcement->published_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($announcementBody !== '')
        <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px;border:1px solid #e2e8f0;border-radius:12px;background:#ffffff;">
            <tr>
                <td style="padding:16px 18px;">
                    <p style="margin:0 0 8px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Message</p>
                    <p style="margin:0;color:#334155;font-size:14px;line-height:1.75;white-space:pre-wrap;">{{ $announcementBody }}</p>
                </td>
            </tr>
        </table>
    @endif

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            <td align="center">
                <a href="{{ $targetUrl }}" style="display:inline-block;padding:12px 24px;border-radius:10px;background:{{ $primaryColor }};color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;border:2px solid {{ $secondaryColor }};">
                    Open Notifications
                </a>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0 0 10px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                    {{ $resolvedSchoolName }} Contact Details
                </p>

                @if($contactPhone !== '')
                    <p style="margin:0 0 6px;color:#334155;font-size:14px;">
                        <strong>Phone:</strong> <a href="tel:{{ $contactPhone }}" style="color:{{ $primaryColor }};text-decoration:none;">{{ $contactPhone }}</a>
                    </p>
                @endif
                @if($contactEmail !== '')
                    <p style="margin:0 0 6px;color:#334155;font-size:14px;">
                        <strong>Email:</strong> <a href="mailto:{{ $contactEmail }}" style="color:{{ $primaryColor }};text-decoration:none;">{{ $contactEmail }}</a>
                    </p>
                @endif
                @if($contactAddress !== '')
                    <p style="margin:0 0 6px;color:#334155;font-size:14px;">
                        <strong>Address:</strong> {{ $contactAddress }}
                    </p>
                @endif
                @if($websiteUrl !== '')
                    <p style="margin:0;color:#334155;font-size:14px;">
                        <strong>Website:</strong> <a href="{{ $websiteUrl }}" style="color:{{ $primaryColor }};text-decoration:none;">{{ $websiteUrl }}</a>
                    </p>
                @endif
            </td>
        </tr>
    </table>
</x-email-layout>
