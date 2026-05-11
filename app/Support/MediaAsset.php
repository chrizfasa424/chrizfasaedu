<?php

namespace App\Support;

use Illuminate\Support\Str;

class MediaAsset
{
    public static function normalize(?string $path): string
    {
        $normalized = trim((string) $path);
        if ($normalized === '') {
            return '';
        }

        $normalized = str_replace('\\', '/', ltrim($normalized, '/'));
        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        return ltrim($normalized, '/');
    }

    public static function url(?string $path): ?string
    {
        $normalized = self::normalize($path);
        if ($normalized === '') {
            return null;
        }

        if (Str::startsWith($normalized, ['http://', 'https://'])) {
            return $normalized;
        }

        $publicStoragePath = public_path('storage/' . $normalized);
        if (file_exists($publicStoragePath)) {
            return asset('storage/' . $normalized);
        }

        $encodedSegments = array_map('rawurlencode', explode('/', $normalized));
        return url('/media/public/' . implode('/', $encodedSegments));
    }

    public static function publicPath(?string $path): ?string
    {
        $normalized = self::normalize($path);
        if ($normalized === '') {
            return null;
        }

        $publicStoragePath = public_path('storage/' . $normalized);
        if (is_file($publicStoragePath)) {
            return $publicStoragePath;
        }

        $storagePublicPath = storage_path('app/public/' . $normalized);
        if (is_file($storagePublicPath)) {
            return $storagePublicPath;
        }

        return null;
    }
}

