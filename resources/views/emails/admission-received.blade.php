@php
    $schoolName = $school?->name ?? 'Our School';
    $schoolEmail = trim((string) ($school?->email ?? ''));
    $schoolPhone = trim((string) ($school?->phone ?? ''));
    $signatureName = $schoolName . ' Admissions Team';
@endphp

<x-email-layout
    title="Application Received"
    :school="$school"
    :school-name="$schoolName"
    :mailer-message="isset($message) ? $message : null"
    preview-text="Your application has been submitted and is now under review."
>
    <p style="margin:0 0 12px;color:#64748b;font-size:15px;">Dear <strong style="color:#0f172a;">{{ $admission->parent_name }}</strong>,</p>
    <p style="margin:0 0 18px;color:#475569;font-size:15px;line-height:1.7;">
        Thank you for applying to <strong>{{ $schoolName }}</strong>. We have received your ward's application and it is currently under review.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0 0 10px;color:#334155;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Application Summary</p>
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
                        <td style="color:#64748b;font-size:14px;">Submitted On</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->created_at->format('d F Y, g:i A') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
        Please keep your application number safe for follow-up enquiries. We will notify you by email once your application review is complete.
    </p>

    <p style="margin:0;color:#334155;font-size:14px;">
        Regards,<br>
        <strong style="color:#0f172a;">{{ $signatureName }}</strong>
        @if($schoolEmail !== '' || $schoolPhone !== '')
            <br>
            @if($schoolEmail !== '')Email: <a href="mailto:{{ $schoolEmail }}" style="color:#25333E;text-decoration:none;">{{ $schoolEmail }}</a>@endif
            @if($schoolEmail !== '' && $schoolPhone !== '') | @endif
            @if($schoolPhone !== '')Phone: <a href="tel:{{ $schoolPhone }}" style="color:#25333E;text-decoration:none;">{{ $schoolPhone }}</a>@endif
        @endif
    </p>
</x-email-layout>
