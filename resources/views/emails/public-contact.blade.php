@php
    $schoolName = $schoolName ?? ($school?->name ?? 'School');
@endphp

<x-email-layout
    title="New Contact Message"
    :school="$school"
    :school-name="$schoolName"
    :mailer-message="isset($message) ? $message : null"
    preview-text="A new message was submitted from the public contact form."
>
    <table width="100%" cellpadding="0" cellspacing="6" style="margin:0 0 14px;">
        <tr><td style="width:38%;color:#64748b;font-size:14px;">Full Name</td><td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $payload['full_name'] }}</td></tr>
        <tr><td style="color:#64748b;font-size:14px;">Email</td><td style="color:#0f172a;font-size:14px;"><a href="mailto:{{ $payload['email'] }}" style="color:#25333E;text-decoration:none;font-weight:600;">{{ $payload['email'] }}</a></td></tr>
        <tr><td style="color:#64748b;font-size:14px;">Phone Number</td><td style="color:#0f172a;font-size:14px;">{{ $payload['phone_number'] ?: 'Not provided' }}</td></tr>
        <tr><td style="color:#64748b;font-size:14px;">Subject</td><td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $payload['subject'] }}</td></tr>
        <tr><td style="color:#64748b;font-size:14px;">Submitted At</td><td style="color:#0f172a;font-size:14px;">{{ $submittedAt->format('Y-m-d H:i:s') }}</td></tr>
        <tr><td style="color:#64748b;font-size:14px;">IP Address</td><td style="color:#0f172a;font-size:14px;">{{ $requestIp }}</td></tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0 0 8px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Message</p>
                <p style="margin:0;color:#334155;font-size:14px;line-height:1.7;white-space:pre-wrap;">{{ $payload['message'] }}</p>
            </td>
        </tr>
    </table>
</x-email-layout>
