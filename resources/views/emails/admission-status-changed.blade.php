@php
    $schoolName = $school?->name ?? 'Our School';
    $schoolEmail = trim((string) ($school?->email ?? ''));
    $schoolPhone = trim((string) ($school?->phone ?? ''));
    $isApproved = $admission->status->value === 'approved';
    $title = $isApproved ? 'Application Approved' : 'Application Update';
    $statusText = $isApproved ? 'Approved' : 'Not Successful';
    $statusBg = $isApproved ? '#dcfce7' : '#fee2e2';
    $statusColor = $isApproved ? '#15803d' : '#b91c1c';
@endphp

<x-email-layout
    :title="$title"
    :school="$school"
    :school-name="$schoolName"
    :mailer-message="isset($message) ? $message : null"
    :preview-text="$isApproved ? 'Congratulations. Your application has been approved.' : 'Your application review has been completed.'"
>
    <p style="margin:0 0 12px;color:#64748b;font-size:15px;">Dear <strong style="color:#0f172a;">{{ $admission->parent_name }}</strong>,</p>

    @if($isApproved)
        <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
            We are pleased to inform you that the application for <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>
            has been approved for admission into <strong>{{ $schoolName }}</strong>.
        </p>
    @else
        <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
            Thank you for your interest in <strong>{{ $schoolName }}</strong>. After careful review, the application for
            <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong> was not successful at this time.
        </p>
    @endif

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
                        <td style="color:#64748b;font-size:14px;">Class Applied</td>
                        <td style="color:#0f172a;font-size:14px;">{{ $admission->class_applied_for }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-size:14px;">Status</td>
                        <td><span style="display:inline-block;border-radius:20px;padding:3px 10px;background:{{ $statusBg }};color:{{ $statusColor }};font-size:12px;font-weight:700;">{{ $statusText }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($admission->review_notes)
        <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #fde68a;border-radius:12px;background:#fffbeb;">
            <tr>
                <td style="padding:14px 16px;">
                    <p style="margin:0 0 6px;color:#92400e;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;">Admissions Note</p>
                    <p style="margin:0;color:#78350f;font-size:14px;line-height:1.6;">{{ $admission->review_notes }}</p>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:0;color:#475569;font-size:15px;line-height:1.7;">
        For enquiries, contact us
        @if($schoolPhone !== '')
            at <a href="tel:{{ $schoolPhone }}" style="color:#25333E;font-weight:600;text-decoration:none;">{{ $schoolPhone }}</a>
        @endif
        @if($schoolEmail !== '')
            @if($schoolPhone !== '') or @else at @endif
            <a href="mailto:{{ $schoolEmail }}" style="color:#25333E;font-weight:600;text-decoration:none;">{{ $schoolEmail }}</a>
        @endif.
    </p>
</x-email-layout>
