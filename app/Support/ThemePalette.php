<?php

namespace App\Support;

class ThemePalette
{
    public static function fromPublicPage(array $publicPage): array
    {
        $primary = self::sanitizeHex($publicPage['primary_color'] ?? null, '#2D1D5C');
        $secondary = self::sanitizeHex($publicPage['secondary_color'] ?? null, '#DFE753');
        $heading = self::sanitizeHex($publicPage['heading_text_color'] ?? null, '#0F172A');
        $body = self::sanitizeHex($publicPage['body_text_color'] ?? null, '#475569');
        $surface = self::sanitizeHex($publicPage['surface_color'] ?? null, '#FFFFFF');
        $softSurface = self::sanitizeHex($publicPage['soft_surface_color'] ?? null, '#EEF6FF');
        $siteBackground = self::sanitizeHex($publicPage['site_background_color'] ?? null, '#F8FAFC');
        $header = self::sanitizeHex($publicPage['header_bg_color'] ?? $primary, $primary);
        $footer = self::sanitizeHex($publicPage['footer_bg_color'] ?? $primary, $primary);
        $divider = self::sanitizeHex($publicPage['footer_separator_color'] ?? $secondary, $secondary);

        return [
            'primary' => [
                '50' => self::mix($primary, '#FFFFFF', 0.92),
                '100' => self::mix($primary, '#FFFFFF', 0.82),
                '200' => self::mix($primary, '#FFFFFF', 0.68),
                '300' => self::mix($primary, '#FFFFFF', 0.5),
                '400' => self::mix($primary, '#FFFFFF', 0.26),
                '500' => $primary,
                '600' => self::mix($primary, '#000000', 0.12),
                '700' => self::mix($primary, '#000000', 0.24),
            ],
            'secondary' => [
                '50' => self::mix($secondary, '#FFFFFF', 0.88),
                '100' => self::mix($secondary, '#FFFFFF', 0.74),
                '200' => self::mix($secondary, '#FFFFFF', 0.56),
                '300' => self::mix($secondary, '#FFFFFF', 0.34),
                '400' => self::mix($secondary, '#FFFFFF', 0.16),
                '500' => $secondary,
                '600' => self::mix($secondary, '#000000', 0.1),
                '700' => self::mix($secondary, '#000000', 0.2),
            ],
            'accent' => [
                '300' => self::mix($secondary, '#FFFFFF', 0.38),
                '400' => self::mix($secondary, '#FFFFFF', 0.18),
                '500' => $secondary,
            ],
            'ink' => $heading,
            'muted' => $body,
            'surface' => $surface,
            'soft_surface' => $softSurface,
            'site_background' => $siteBackground,
            'header' => $header,
            'footer' => $footer,
            'divider' => $divider,
            'primary_text_on_secondary' => self::contrastText($secondary),
            'primary_text_on_primary' => self::contrastText($primary),
            'theme_style' => in_array(($publicPage['theme_style'] ?? 'modern-grid'), ['modern-grid', 'soft-gradient', 'minimal-clean'], true)
                ? $publicPage['theme_style']
                : 'modern-grid',
        ];
    }

    public static function sanitizeHex(?string $value, string $default): string
    {
        $value = strtoupper(trim((string) $value));

        if (!preg_match('/^#[A-F0-9]{6}$/', $value)) {
            return strtoupper($default);
        }

        return $value;
    }

    public static function contrastText(string $hex): string
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

        return $luminance > 160 ? '#0F172A' : '#FFFFFF';
    }

    public static function mix(string $source, string $target, float $ratio): string
    {
        $ratio = max(0, min(1, $ratio));
        [$r1, $g1, $b1] = self::hexToRgb($source);
        [$r2, $g2, $b2] = self::hexToRgb($target);

        $r = (int) round(($r1 * (1 - $ratio)) + ($r2 * $ratio));
        $g = (int) round(($g1 * (1 - $ratio)) + ($g2 * $ratio));
        $b = (int) round(($b1 * (1 - $ratio)) + ($b2 * $ratio));

        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim(self::sanitizeHex($hex, '#000000'), '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
