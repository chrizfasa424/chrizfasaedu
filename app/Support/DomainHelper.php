<?php

namespace App\Support;

class DomainHelper
{
    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, '://')) {
            $host = parse_url($value, PHP_URL_HOST);
            $value = is_string($host) ? $host : $value;
        } else {
            $parsed = parse_url('//' . ltrim($value, '/'), PHP_URL_HOST);
            if (is_string($parsed) && $parsed !== '') {
                $value = $parsed;
            }
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/:\d+$/', '', $value) ?? $value;
        $value = trim($value, ". \t\n\r\0\x0B/");
        $value = preg_replace('/^www\./', '', $value) ?? $value;

        return $value !== '' ? $value : null;
    }
}
