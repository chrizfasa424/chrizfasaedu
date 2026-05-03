@props([
    'title' => 'Notification',
    'school' => null,
    'schoolName' => null,
    'previewText' => null,
    'mailerMessage' => null,
])
@php
    $resolvedSchoolName = trim((string) ($schoolName ?? ($school?->name ?? config('app.name', 'School'))));
    $logoPath = trim((string) ($school?->logo ?? ''));
    $normalizedLogoPath = ltrim($logoPath, '/');
    $logoUrl = null;
    if ($normalizedLogoPath !== '') {
        if (\Illuminate\Support\Str::startsWith($normalizedLogoPath, ['http://', 'https://'])) {
            $logoUrl = $normalizedLogoPath;
        } else {
            $logoUrl = asset('storage/' . $normalizedLogoPath);
        }
    }
    $logoEmbeddedSrc = null;
    if ($normalizedLogoPath !== '' && $mailerMessage) {
        $logoAbsolutePath = storage_path('app/public/' . $normalizedLogoPath);
        if (is_file($logoAbsolutePath)) {
            try {
                $logoEmbeddedSrc = $mailerMessage->embed($logoAbsolutePath);
            } catch (\Throwable $e) {
                $logoEmbeddedSrc = null;
            }
        }
    }
    $logoSrc = $logoEmbeddedSrc ?: $logoUrl;

    $publicPage = \App\Support\PublicPageContent::forSchool($school);
    $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);

    $primaryColor = (string) data_get($theme, 'primary.500', '#25333E');
    $secondaryColor = (string) data_get($theme, 'secondary.500', '#DFE753');

    $initials = \Illuminate\Support\Str::upper(
        collect(preg_split('/\s+/', $resolvedSchoolName))
            ->filter()
            ->take(2)
            ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
            ->implode('')
    );

    $hexToRgba = function (string $hex, float $alpha): string {
        $clean = ltrim(trim($hex), '#');
        if (!preg_match('/^[A-Fa-f0-9]{6}$/', $clean)) {
            return 'rgba(37, 51, 62, ' . $alpha . ')';
        }
        $r = hexdec(substr($clean, 0, 2));
        $g = hexdec(substr($clean, 2, 2));
        $b = hexdec(substr($clean, 4, 2));
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    };

    $canvasBg = $hexToRgba($secondaryColor, 0.16);
    $panelBg = $hexToRgba($secondaryColor, 0.08);
    $mutedText = '#475569';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:{{ $canvasBg }};font-family:'Segoe UI',Arial,sans-serif;color:#0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:{{ $canvasBg }};padding:28px 16px;">
        <tr>
            <td align="center">
                <table width="680" cellpadding="0" cellspacing="0" style="max-width:680px;width:100%;background:#ffffff;border:1px solid {{ $hexToRgba($primaryColor, 0.14) }};border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});padding:24px 28px;text-align:center;">
                            @if($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ $resolvedSchoolName }} Logo" style="display:block;margin:0 auto 12px;height:64px;width:64px;border-radius:14px;border:1px solid rgba(255,255,255,0.72);object-fit:cover;background:#ffffff;">
                            @else
                                <div style="display:inline-flex;align-items:center;justify-content:center;margin:0 auto 12px;height:64px;width:64px;border-radius:14px;background:#ffffff;color:{{ $primaryColor }};font-size:22px;font-weight:800;border:1px solid rgba(255,255,255,0.72);">
                                    {{ $initials !== '' ? $initials : 'SC' }}
                                </div>
                            @endif
                            <p style="margin:0;color:rgba(255,255,255,0.9);font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">{{ $resolvedSchoolName }}</p>
                            <h1 style="margin:10px 0 0;color:#ffffff;font-size:26px;line-height:1.25;font-weight:800;">{{ $title }}</h1>
                            @if($previewText)
                                <p style="margin:10px 0 0;color:rgba(255,255,255,0.88);font-size:14px;line-height:1.5;">{{ $previewText }}</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;background:{{ $panelBg }};">
                            <div style="background:#ffffff;border:1px solid {{ $hexToRgba($primaryColor, 0.12) }};border-radius:14px;padding:22px;">
                                {{ $slot }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;background:{{ $primaryColor }};text-align:center;border-top:3px solid {{ $secondaryColor }};">
                            <p style="margin:0;color:rgba(255,255,255,0.92);font-size:12px;">&copy; {{ date('Y') }} {{ $resolvedSchoolName }}. All rights reserved.</p>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.75);font-size:11px;">This is an automated email alert from {{ $resolvedSchoolName }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
