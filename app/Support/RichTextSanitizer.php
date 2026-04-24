<?php

namespace App\Support;

class RichTextSanitizer
{
    /**
     * Keep only safe formatting tags and strip risky tags/attributes.
     */
    public static function sanitize(string $html): string
    {
        $content = trim($html);
        if ($content === '') {
            return '';
        }

        // Remove dangerous tags entirely.
        $content = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)[^>]*>.*?</\1>#is', '', $content) ?? '';
        $content = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)([^>]*)/?>#is', '', $content) ?? '';

        // Keep a conservative rich-text whitelist.
        $content = strip_tags($content, '<p><br><strong><b><em><i><u><s><ul><ol><li><blockquote><a><h2><h3><h4><table><thead><tbody><tr><th><td>');

        // Remove inline event/style handlers.
        $content = preg_replace('/\s+on\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $content) ?? '';
        $content = preg_replace('/\s+style\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $content) ?? '';

        // Prevent javascript: URLs.
        $content = preg_replace('/\s+href\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', ' href="#"', $content) ?? '';
        $content = preg_replace('/\s+src\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', '', $content) ?? '';

        return trim($content);
    }

    public static function plainTextLength(string $html): int
    {
        $text = trim(strip_tags(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        return mb_strlen($text);
    }
}
