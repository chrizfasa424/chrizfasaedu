<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\HtmlString;

class RichText
{
    private const ALLOWED_TAGS = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'em' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'a' => ['href', 'target', 'rel'],
        'figure' => ['class'],
        'figcaption' => [],
        'img' => ['src', 'alt'],
    ];

    public static function sanitize(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value) ?? $value;
        $value = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $value) ?? $value;
        $value = preg_replace('/<!--(.|\s)*?-->/', '', $value) ?? $value;

        if (!class_exists(DOMDocument::class)) {
            return trim(strip_tags($value));
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><body>' . $value . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $document->getElementsByTagName('body')->item(0);
        if (!$body) {
            return trim(strip_tags($value));
        }

        self::sanitizeNode($body);

        $html = '';
        foreach ($body->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return trim($html);
    }

    public static function render(?string $value): HtmlString
    {
        $sanitized = self::sanitize($value);

        if ($sanitized === '') {
            return new HtmlString('');
        }

        if (!preg_match('/<(p|br|strong|em|ul|ol|li|blockquote|h2|h3|h4|a|figure|figcaption|img)\b/i', $sanitized)) {
            return new HtmlString(nl2br(e($sanitized)));
        }

        return new HtmlString($sanitized);
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
            $child = $node->childNodes->item($index);

            if (!$child) {
                continue;
            }

            if ($child instanceof DOMElement) {
                $tagName = strtolower($child->tagName);

                if (!array_key_exists($tagName, self::ALLOWED_TAGS)) {
                    self::unwrapElement($child);
                    continue;
                }

                self::filterAttributes($child, self::ALLOWED_TAGS[$tagName]);
            }

            self::sanitizeNode($child);
        }
    }

    private static function unwrapElement(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private static function filterAttributes(DOMElement $element, array $allowedAttributes): void
    {
        if ($element->hasAttributes()) {
            for ($index = $element->attributes->length - 1; $index >= 0; $index--) {
                $attribute = $element->attributes->item($index);

                if (!$attribute) {
                    continue;
                }

                $attributeName = strtolower($attribute->nodeName);
                if (!in_array($attributeName, $allowedAttributes, true)) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                $sanitizedValue = self::sanitizeAttributeValue($element->tagName, $attributeName, $attribute->nodeValue);
                if ($sanitizedValue === null) {
                    $element->removeAttribute($attributeName);
                    continue;
                }

                $element->setAttribute($attributeName, $sanitizedValue);
            }
        }

        if (strtolower($element->tagName) === 'a') {
            $href = trim((string) $element->getAttribute('href'));
            if ($href !== '' && !$element->hasAttribute('rel')) {
                $element->setAttribute('rel', 'noopener noreferrer');
            }
            if ($href !== '' && !$element->hasAttribute('target')) {
                $element->setAttribute('target', '_blank');
            }
        }
    }

    private static function sanitizeAttributeValue(string $tagName, string $attributeName, string $value): ?string
    {
        $value = trim($value);
        $tagName = strtolower($tagName);
        $attributeName = strtolower($attributeName);

        if ($value === '') {
            return null;
        }

        if ($attributeName === 'href') {
            return preg_match('/^(https?:\/\/|mailto:|tel:|\/|#)/i', $value) ? $value : null;
        }

        if ($attributeName === 'src') {
            return preg_match('/^(https?:\/\/|\/)/i', $value) ? $value : null;
        }

        if ($tagName === 'figure' && $attributeName === 'class') {
            return preg_match('/^[a-z0-9\-\s_]+$/i', $value) ? $value : null;
        }

        if ($attributeName === 'target') {
            return in_array($value, ['_blank', '_self'], true) ? $value : null;
        }

        if ($attributeName === 'rel') {
            return 'noopener noreferrer';
        }

        if ($attributeName === 'alt') {
            return strip_tags($value);
        }

        return strip_tags($value);
    }
}
