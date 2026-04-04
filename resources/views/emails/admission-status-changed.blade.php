<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Application Status Update</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
@php
    $isApproved = $admission->status->value === 'approved';
    $headerColor = $isApproved ? '#16a34a' : '#dc2626';
    $icon        = $isApproved ? '🎉' : '📋';
    $headline    = $isApproved ? 'Application Approved!' : 'Application Update';
    $badgeBg     = $isApproved ? '#dcfce7' : '#fee2e2';
    $badgeColor  = $isApproved ? '#15803d' : '#b91c1c';
    $badgeText   = $isApproved ? 'Approved' : 'Not Successful';
@endphp
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

      {{-- Header --}}
      <tr>
        <td style="background:{{ $headerColor }};padding:36px 40px;text-align:center;">
          <p style="margin:0;color:rgba(255,255,255,0.8);font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;">
            {{ $school?->name ?? 'Our School' }}
          </p>
          <h1 style="margin:12px 0 0;color:#ffffff;font-size:26px;font-weight:700;">{{ $headline }}</h1>
        </td>
      </tr>
      <tr>
        <td style="background:{{ $headerColor }};padding:0 40px 32px;text-align:center;">
          <div style="display:inline-block;background:rgba(255,255,255,0.2);border-radius:50%;width:64px;height:64px;line-height:64px;font-size:28px;">{{ $icon }}</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:36px 40px;">
          <p style="margin:0 0 16px;color:#64748b;font-size:15px;">Dear <strong style="color:#1e293b;">{{ $admission->parent_name }}</strong>,</p>

          @if($isApproved)
          <p style="margin:0 0 20px;color:#475569;font-size:15px;line-height:1.7;">
            We are delighted to inform you that the admission application for your ward, <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>, has been <strong style="color:#16a34a;">approved</strong> for admission into <strong>{{ $school?->name ?? 'our school' }}</strong>. Congratulations!
          </p>
          @else
          <p style="margin:0 0 20px;color:#475569;font-size:15px;line-height:1.7;">
            We regret to inform you that, after careful review, the admission application for your ward, <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>, was <strong style="color:#dc2626;">not successful</strong> at this time. We appreciate your interest in <strong>{{ $school?->name ?? 'our school' }}</strong>.
          </p>
          @endif

          {{-- Details card --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;">
            <tr><td style="padding:20px 24px;">
              <p style="margin:0 0 14px;color:#1e293b;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Application Details</p>
              <table width="100%" cellpadding="0" cellspacing="6">
                <tr>
                  <td style="color:#64748b;font-size:14px;width:45%;padding:3px 0;">Application No.</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:700;font-family:monospace;">{{ $admission->application_number }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Student</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:600;">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Class Applied</td>
                  <td style="color:#1e293b;font-size:14px;">{{ $admission->class_applied_for }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Status</td>
                  <td><span style="background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $badgeText }}</span></td>
                </tr>
              </table>
            </td></tr>
          </table>

          @if($admission->review_notes)
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:12px;border:1px solid #fde68a;margin-bottom:24px;">
            <tr><td style="padding:18px 24px;">
              <p style="margin:0 0 8px;color:#92400e;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">Note from Admissions</p>
              <p style="margin:0;color:#78350f;font-size:14px;line-height:1.7;">{{ $admission->review_notes }}</p>
            </td></tr>
          </table>
          @endif

          @if($isApproved)
          <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
            Please contact us to complete the enrolment process. We look forward to welcoming your child to our school community.
          </p>
          @else
          <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.7;">
            We encourage you to reapply in the next admission cycle. If you have any questions, please do not hesitate to contact us.
          </p>
          @endif

          <p style="margin:0;color:#475569;font-size:15px;">
            For enquiries, contact us at
            @if($school?->phone)
              <a href="tel:{{ $school->phone }}" style="color:#2D1D5C;font-weight:600;">{{ $school->phone }}</a>
            @endif
            @if($school?->email)
              or <a href="mailto:{{ $school->email }}" style="color:#2D1D5C;font-weight:600;">{{ $school->email }}</a>.
            @endif
          </p>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:#f8fafc;padding:24px 40px;border-top:1px solid #e2e8f0;text-align:center;">
          <p style="margin:0;color:#94a3b8;font-size:13px;">&copy; {{ date('Y') }} {{ $school?->name ?? 'Our School' }}. All rights reserved.</p>
          <p style="margin:6px 0 0;color:#cbd5e1;font-size:12px;">This is an automated message. Please do not reply to this email.</p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
