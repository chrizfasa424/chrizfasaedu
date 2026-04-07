<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Enrolment Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

      {{-- Header --}}
      <tr>
        <td style="background:#16a34a;padding:36px 40px;text-align:center;">
          <p style="margin:0;color:rgba(255,255,255,0.8);font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;">
            {{ $school?->name ?? 'Our School' }}
          </p>
          <h1 style="margin:12px 0 0;color:#ffffff;font-size:26px;font-weight:700;">Enrolment Confirmed!</h1>
        </td>
      </tr>
      <tr>
        <td style="background:#16a34a;padding:0 40px 32px;text-align:center;">
          <div style="display:inline-block;background:rgba(255,255,255,0.2);border-radius:50%;width:64px;height:64px;line-height:64px;font-size:28px;">🎓</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:36px 40px;">
          <p style="margin:0 0 16px;color:#64748b;font-size:15px;">Dear <strong style="color:#1e293b;">{{ $admission->parent_name }}</strong>,</p>
          <p style="margin:0 0 20px;color:#475569;font-size:15px;line-height:1.7;">
            We are pleased to inform you that <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong> has been successfully enrolled at <strong>{{ $school?->name ?? 'our school' }}</strong>. A student portal account has been created.
          </p>

          {{-- Student details --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;">
            <tr><td style="padding:20px 24px;">
              <p style="margin:0 0 14px;color:#1e293b;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Student Details</p>
              <table width="100%" cellpadding="0" cellspacing="6">
                <tr>
                  <td style="color:#64748b;font-size:14px;width:45%;padding:3px 0;">Admission No.</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:700;font-family:monospace;">{{ $admission->admission_number }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Full Name</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:600;">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Class</td>
                  <td style="color:#1e293b;font-size:14px;">{{ $admission->class_applied_for }}</td>
                </tr>
              </table>
            </td></tr>
          </table>

          {{-- Login credentials --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;border-radius:12px;border:1px solid #bfdbfe;margin-bottom:24px;">
            <tr><td style="padding:20px 24px;">
              <p style="margin:0 0 14px;color:#1e3a5f;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">🔑 Portal Login Credentials</p>
              <table width="100%" cellpadding="0" cellspacing="6">
                <tr>
                  <td style="color:#64748b;font-size:14px;width:45%;padding:3px 0;">Login URL</td>
                  <td style="font-size:14px;">
                    <a href="{{ $loginUrl }}" style="color:#2563eb;font-weight:600;">{{ $loginUrl }}</a>
                  </td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Email</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:600;">{{ $loginEmail }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:14px;padding:3px 0;">Password</td>
                  <td style="color:#1e293b;font-size:14px;font-weight:700;font-family:monospace;letter-spacing:0.08em;">{{ $plainPassword }}</td>
                </tr>
              </table>
            </td></tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:12px;border:1px solid #fde68a;margin-bottom:24px;">
            <tr><td style="padding:16px 24px;">
              <p style="margin:0;color:#78350f;font-size:13px;line-height:1.7;">
                ⚠️ <strong>Important:</strong> Please log in and change your password immediately. Keep these credentials private and do not share them with anyone.
              </p>
            </td></tr>
          </table>

          <p style="margin:0;color:#475569;font-size:15px;">
            For assistance, contact us at
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
