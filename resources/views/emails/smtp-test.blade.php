<x-email-layout
    title="SMTP Test Successful"
    :school="$school"
    :school-name="$school?->name ?? 'School'"
    :mailer-message="isset($message) ? $message : null"
    preview-text="This confirms your SMTP configuration is sending emails successfully."
>
    <table width="100%" cellpadding="0" cellspacing="6">
        <tr>
            <td style="width:38%;color:#64748b;font-size:14px;">School</td>
            <td style="color:#0f172a;font-size:14px;font-weight:600;">{{ $school?->name ?? 'School' }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:14px;">Recipient</td>
            <td style="color:#0f172a;font-size:14px;">{{ $recipient }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:14px;">Sent At</td>
            <td style="color:#0f172a;font-size:14px;">{{ $sentAt->format('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:14px;">SMTP Host</td>
            <td style="color:#0f172a;font-size:14px;">{{ $smtp['host'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:14px;">SMTP Port</td>
            <td style="color:#0f172a;font-size:14px;">{{ $smtp['port'] ?? 'N/A' }}</td>
        </tr>
    </table>
</x-email-layout>
