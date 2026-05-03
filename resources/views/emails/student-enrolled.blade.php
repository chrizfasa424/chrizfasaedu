@php
    $schoolName = $school?->name ?? 'Our School';
    $schoolEmail = trim((string) ($school?->email ?? ''));
    $schoolPhone = trim((string) ($school?->phone ?? ''));
@endphp

<x-email-layout
    title="Enrolment Confirmed"
    :school="$school"
    :school-name="$schoolName"
    :mailer-message="isset($message) ? $message : null"
    preview-text="Your ward has been successfully enrolled and a portal account has been created."
>
    <p style="margin:0 0 12px;color:#64748b;font-size:15px;">Dear <strong style="color:#0f172a;">{{ $admission->parent_name }}</strong>,</p>
    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
        We are pleased to confirm that <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>
        has been enrolled at <strong>{{ $schoolName }}</strong>.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0 0 10px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Student Details</p>
                <table width="100%" cellpadding="0" cellspacing="6">
                    <tr>
                        <td style="width:42%;color:#64748b;font-size:14px;">Admission Number</td>
                        <td style="color:#0f172a;font-size:14px;font-family:monospace;font-weight:700;">{{ $admission->admission_number }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Full Name</td>
                        <td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Class</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->class_applied_for }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #bfdbfe;border-radius:12px;background:#eff6ff;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0 0 10px;color:#1e3a5f;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Portal Login Credentials</p>
                <table width="100%" cellpadding="0" cellspacing="6">
                    <tr>
                        <td style="width:42%;color:#64748b;font-size:14px;">Login URL</td>
                        <td style="font-size:14px;"><a href="{{ $loginUrl }}" style="color:#1d4ed8;font-weight:700;text-decoration:none;">{{ $loginUrl }}</a></td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Email</td>
                        <td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $loginEmail }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Password</td>
                        <td style="color:#0f172a;font-size:14px;font-family:monospace;font-weight:700;letter-spacing:0.04em;">{{ $plainPassword }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #fde68a;border-radius:12px;background:#fffbeb;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0;color:#78350f;font-size:13px;line-height:1.6;">
                    Important: Please log in and change the password immediately. Keep these credentials private.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0;color:#475569;font-size:15px;line-height:1.7;">
        For support, contact us
        @if($schoolPhone !== '')
            at <a href="tel:{{ $schoolPhone }}" style="color:#25333E;font-weight:600;text-decoration:none;">{{ $schoolPhone }}</a>
        @endif
        @if($schoolEmail !== '')
            @if($schoolPhone !== '') or @else at @endif
            <a href="mailto:{{ $schoolEmail }}" style="color:#25333E;font-weight:600;text-decoration:none;">{{ $schoolEmail }}</a>
        @endif.
    </p>
</x-email-layout>
