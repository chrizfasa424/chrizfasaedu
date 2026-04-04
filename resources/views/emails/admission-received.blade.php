<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Application Received</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

      {{-- Header --}}
      <tr>
        <td style="background:#2D1D5C;padding:36px 40px;text-align:center;">
          <p style="margin:0;color:rgba(255,255,255,0.7);font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;">
            {{ $school?->name ?? 'Our School' }}
          </p>
          <h1 style="margin:12px 0 0;color:#ffffff;font-size:26px;font-weight:700;line-height:1.3;">
            Application Received!
          </h1>
        </td>
      </tr>

      {{-- Icon row --}}
      <tr>
        <td style="background:#2D1D5C;padding:0 40px 32px;text-align:center;">
          <div style="display:inline-block;background:#DFE753;border-radius:50%;width:64px;height:64px;line-height:64px;font-size:28px;text-align:center;">✅</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:36px 40px;">
          <p style="margin:0 0 8px;color:#64748b;font-size:15px;">Dear <strong style="color:#1e293b;">{{ $admission->parent_name }}</strong>,</p>
          <p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
            Thank you for applying to <strong>{{ $school?->name ?? 'our school' }}</strong>. We have successfully received the admission application for your ward and it is currently under review.
          </p>

          {{-- Application details card --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;">
            <tr><td style="padding:20px 24px;">
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
            </td></tr>
          </table>

          <p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
            Our admissions team will review your application and notify you of the outcome via email. Please keep your <strong>Application Number</strong> safe for reference.
          </p>

          <p style="margin:0;color:#475569;font-size:15px;line-height:1.7;">
            For enquiries, please contact us at
            @if($school?->phone)
              <a href="tel:{{ $school->phone }}" style="color:#2D1D5C;font-weight:600;">{{ $school->phone }}</a> or
            @endif
            @if($school?->email)
              <a href="mailto:{{ $school->email }}" style="color:#2D1D5C;font-weight:600;">{{ $school->email }}</a>.
            @else
              our school office.
            @endif
          </p>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:#f8fafc;padding:24px 40px;border-top:1px solid #e2e8f0;text-align:center;">
          <p style="margin:0;color:#94a3b8;font-size:13px;">
            &copy; {{ date('Y') }} {{ $school?->name ?? 'Our School' }}. All rights reserved.
          </p>
          <p style="margin:6px 0 0;color:#cbd5e1;font-size:12px;">
            This is an automated message. Please do not reply to this email.
          </p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
