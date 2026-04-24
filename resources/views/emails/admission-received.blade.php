<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Application Received</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
@php
    $schoolName = $school?->name ?? 'Our School';
    $schoolEmail = trim((string) ($school?->email ?? ''));
    $schoolPhone = trim((string) ($school?->phone ?? ''));
    $schoolWebsite = trim((string) ($school?->website ?? ''));
    $schoolWebsiteHref = $schoolWebsite !== '' && !preg_match('/^https?:\/\//i', $schoolWebsite)
        ? 'https://' . $schoolWebsite
        : $schoolWebsite;
    $schoolAddress = collect([
        trim((string) ($school?->address ?? '')),
        trim((string) ($school?->city ?? '')),
        trim((string) ($school?->state ?? '')),
        trim((string) ($school?->country ?? '')),
    ])->filter()->implode(', ');
    $schoolMotto = trim((string) ($school?->motto ?? ''));

    $smtpSettings = (array) data_get($school?->settings, 'smtp', []);
    $signatureName = trim((string) ($smtpSettings['from_name'] ?? '')) ?: ($schoolName . ' Admissions Team');
    $signatureEmail = trim((string) ($smtpSettings['from_address'] ?? '')) ?: $schoolEmail;
    $contactRecipient = trim((string) ($smtpSettings['to_address'] ?? '')) ?: $schoolEmail;
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                <tr>
                    <td style="background:#2D1D5C;padding:34px 40px;text-align:center;">
                        <p style="margin:0;color:rgba(255,255,255,0.78);font-size:12px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">{{ $schoolName }}</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:26px;font-weight:700;line-height:1.3;">Application Received</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:34px 40px;">
                        <p style="margin:0 0 8px;color:#64748b;font-size:15px;">Dear <strong style="color:#1e293b;">{{ $admission->parent_name }}</strong>,</p>
                        <p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
                            Thank you for applying to <strong>{{ $schoolName }}</strong>. We have successfully received your ward's admission application and it is currently under review.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:20px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 16px;color:#1e293b;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Application Summary</p>
                                    <table width="100%" cellpadding="0" cellspacing="8">
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:4px 0;width:45%;">Application Number</td>
                                            <td style="color:#1e293b;font-size:14px;font-weight:700;font-family:monospace;">{{ $admission->application_number }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:4px 0;">Student Name</td>
                                            <td style="color:#1e293b;font-size:14px;font-weight:600;">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:4px 0;">Class Applied For</td>
                                            <td style="color:#1e293b;font-size:14px;font-weight:600;">{{ $admission->class_applied_for }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:4px 0;">Submitted On</td>
                                            <td style="color:#1e293b;font-size:14px;">{{ $admission->created_at->format('d F Y, g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:4px 0;">Current Status</td>
                                            <td><span style="background:#fef9c3;color:#854d0e;font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;">Pending Review</span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;color:#1e293b;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">School Details</p>
                                    <table width="100%" cellpadding="0" cellspacing="6">
                                        @if($schoolMotto !== '')
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;width:45%;padding:3px 0;">Motto</td>
                                            <td style="color:#1e293b;font-size:14px;">{{ $schoolMotto }}</td>
                                        </tr>
                                        @endif
                                        @if($schoolPhone !== '')
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:3px 0;">Phone</td>
                                            <td style="font-size:14px;"><a href="tel:{{ $schoolPhone }}" style="color:#2D1D5C;font-weight:600;text-decoration:none;">{{ $schoolPhone }}</a></td>
                                        </tr>
                                        @endif
                                        @if($schoolEmail !== '')
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:3px 0;">Email</td>
                                            <td style="font-size:14px;"><a href="mailto:{{ $schoolEmail }}" style="color:#2D1D5C;font-weight:600;text-decoration:none;">{{ $schoolEmail }}</a></td>
                                        </tr>
                                        @endif
                                        @if($schoolWebsite !== '')
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:3px 0;">Website</td>
                                            <td style="font-size:14px;"><a href="{{ $schoolWebsiteHref }}" style="color:#2D1D5C;font-weight:600;text-decoration:none;">{{ $schoolWebsite }}</a></td>
                                        </tr>
                                        @endif
                                        @if($schoolAddress !== '')
                                        <tr>
                                            <td style="color:#64748b;font-size:14px;padding:3px 0;">Address</td>
                                            <td style="color:#1e293b;font-size:14px;">{{ $schoolAddress }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 20px;color:#475569;font-size:15px;line-height:1.7;">
                            Our admissions team will review your application and notify you of the outcome via email. Please keep your application number safe for reference.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e2e8f0;padding-top:16px;">
                            <tr>
                                <td style="padding-top:16px;">
                                    <p style="margin:0 0 4px;color:#334155;font-size:14px;">Warm regards,</p>
                                    <p style="margin:0;color:#0f172a;font-size:15px;font-weight:700;">{{ $signatureName }}</p>
                                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Admissions Office, {{ $schoolName }}</p>
                                    @if($signatureEmail !== '')
                                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Email: <a href="mailto:{{ $signatureEmail }}" style="color:#2D1D5C;text-decoration:none;">{{ $signatureEmail }}</a></p>
                                    @endif
                                    @if($contactRecipient !== '' && $contactRecipient !== $signatureEmail)
                                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Admissions Desk: <a href="mailto:{{ $contactRecipient }}" style="color:#2D1D5C;text-decoration:none;">{{ $contactRecipient }}</a></p>
                                    @endif
                                    @if($schoolPhone !== '')
                                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Phone: <a href="tel:{{ $schoolPhone }}" style="color:#2D1D5C;text-decoration:none;">{{ $schoolPhone }}</a></p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f8fafc;padding:22px 40px;border-top:1px solid #e2e8f0;text-align:center;">
                        <p style="margin:0;color:#94a3b8;font-size:13px;">&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
                        <p style="margin:6px 0 0;color:#cbd5e1;font-size:12px;">This is an automated message. Please do not reply directly to this email.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
