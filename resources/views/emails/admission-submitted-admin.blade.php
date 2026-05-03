@php
    $schoolName = $school?->name ?? 'Our School';
    $schoolEmail = trim((string) ($school?->email ?? ''));
    $schoolPhone = trim((string) ($school?->phone ?? ''));
@endphp

<x-email-layout
    title="New Admission Submitted"
    :school="$school"
    :school-name="$schoolName"
    :mailer-message="isset($message) ? $message : null"
    preview-text="A new online admission application has been submitted and is awaiting review."
>
    <p style="margin:0 0 14px;color:#475569;font-size:15px;line-height:1.7;">
        A new application has been submitted through the public admission form.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0 0 10px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Application Details</p>
                <table width="100%" cellpadding="0" cellspacing="6">
                    <tr>
                        <td style="width:42%;color:#64748b;font-size:14px;">Application Number</td>
                        <td style="color:#0f172a;font-size:14px;font-family:monospace;font-weight:700;">{{ $admission->application_number }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Student</td>
                        <td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Class Applied For</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->class_applied_for }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Parent / Guardian</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->parent_name }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Parent Email</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->parent_email }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Parent Phone</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->parent_phone }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Submitted At</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->created_at->format('d F Y, g:i A') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0;color:#475569;font-size:14px;line-height:1.7;">
        Review this application from the Admissions panel in your admin dashboard.
        @if($schoolEmail !== '' || $schoolPhone !== '')
            For support:
            @if($schoolEmail !== '') <a href="mailto:{{ $schoolEmail }}" style="color:#25333E;text-decoration:none;">{{ $schoolEmail }}</a>@endif
            @if($schoolEmail !== '' && $schoolPhone !== '') | @endif
            @if($schoolPhone !== '') <a href="tel:{{ $schoolPhone }}" style="color:#25333E;text-decoration:none;">{{ $schoolPhone }}</a>@endif
        @endif
    </p>
</x-email-layout>

