<?php

namespace App\Services;

/**
 * HTML Sanitizer for TinyMCE content
 * Removes potentially dangerous HTML while preserving safe formatting
 */
class HtmlSanitizerService
{
    /**
     * Allowed HTML tags and their allowed attributes
     */
    private const ALLOWED_TAGS = [
        'p' => ['class', 'style'],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        's' => [],
        'strike' => [],
        'h1' => ['class', 'style'],
        'h2' => ['class', 'style'],
        'h3' => ['class', 'style'],
        'h4' => ['class', 'style'],
        'h5' => ['class', 'style'],
        'h6' => ['class', 'style'],
        'ul' => ['class'],
        'ol' => ['class'],
        'li' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'class', 'style'],
        'table' => ['class', 'style', 'border'],
        'thead' => [],
        'tbody' => [],
        'tr' => [],
        'th' => ['colspan', 'rowspan', 'class', 'style'],
        'td' => ['colspan', 'rowspan', 'class', 'style'],
        'blockquote' => ['class'],
        'pre' => ['class'],
        'code' => ['class'],
        'span' => ['class', 'style'],
        'div' => ['class', 'style'],
        'figure' => ['class'],
        'figcaption' => [],
        'hr' => [],
        'sup' => [],
        'sub' => [],
    ];

    /**
     * Dangerous patterns to remove
     */
    private const DANGEROUS_PATTERNS = [
        // Event handlers
        '/\s+on\w+\s*=\s*["\'][^"\']*["\']/i',
        // JavaScript URLs
        '/javascript\s*:/i',
        // VBScript URLs
        '/vbscript\s*:/i',
        // Data URLs with scripts
        '/data\s*:[^,]*text\/html/i',
        // Expression in styles (IE)
        '/expression\s*\(/i',
        // Binding
        '/-moz-binding\s*:/i',
        // Behavior (IE)
        '/behavior\s*:/i',
    ];

    /**
     * Sanitize HTML content
     */
    public function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Remove dangerous patterns first
        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }

        // Parse and clean HTML
        $dom = new \DOMDocument();
        
        // Suppress warnings from malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with proper encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();

        // Clean the DOM
        $this->cleanNode($dom->documentElement);

        // Get the cleaned HTML
        $result = '';
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }
        }

        return trim($result);
    }

    /**
     * Recursively clean a DOM node
     */
    private function cleanNode(\DOMNode $node): void
    {
        $nodesToRemove = [];

        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                $tagName = strtolower($child->tagName);

                // Check if tag is allowed
                if (!isset(self::ALLOWED_TAGS[$tagName])) {
                    // Replace with text content or remove
                    if ($tagName === 'script' || $tagName === 'style' || $tagName === 'iframe' || $tagName === 'object' || $tagName === 'embed') {
                        $nodesToRemove[] = $child;
                    } else {
                        // Keep text content but remove tag
                        $fragment = $child->ownerDocument->createDocumentFragment();
                        while ($child->firstChild) {
                            $fragment->appendChild($child->firstChild);
                        }
                        $child->parentNode->replaceChild($fragment, $child);
                    }
                    continue;
                }

                // Clean attributes
                $allowedAttrs = self::ALLOWED_TAGS[$tagName];
                $attrsToRemove = [];

                foreach ($child->attributes as $attr) {
                    $attrName = strtolower($attr->name);
                    
                    if (!in_array($attrName, $allowedAttrs, true)) {
                        $attrsToRemove[] = $attr->name;
                        continue;
                    }

                    // Extra validation for specific attributes
                    if ($attrName === 'href' || $attrName === 'src') {
                        $value = strtolower(trim($attr->value));
                        // Block javascript: and other dangerous protocols
                        if (preg_match('/^(javascript|vbscript|data):/i', $value)) {
                            $attrsToRemove[] = $attr->name;
                        }
                    }

                    // Clean style attribute
                    if ($attrName === 'style') {
                        $cleanedStyle = $this->sanitizeStyle($attr->value);
                        if (empty($cleanedStyle)) {
                            $attrsToRemove[] = $attr->name;
                        } else {
                            $child->setAttribute('style', $cleanedStyle);
                        }
                    }
                }

                foreach ($attrsToRemove as $attrName) {
                    $child->removeAttribute($attrName);
                }

                // Recursively clean children
                $this->cleanNode($child);
            }
        }

        // Remove dangerous nodes
        foreach ($nodesToRemove as $nodeToRemove) {
            $nodeToRemove->parentNode->removeChild($nodeToRemove);
        }
    }

    /**
     * Sanitize inline styles
     */
    private function sanitizeStyle(string $style): string
    {
        // Remove dangerous CSS
        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            $style = preg_replace($pattern, '', $style);
        }

        // Only allow safe CSS properties
        $allowedProperties = [
            'color', 'background-color', 'background',
            'font-size', 'font-family', 'font-weight', 'font-style',
            'text-align', 'text-decoration', 'line-height',
            'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
            'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
            'border', 'border-radius', 'width', 'height', 'max-width', 'max-height',
            'display', 'float', 'clear',
        ];

        $parts = explode(';', $style);
        $cleaned = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            $colonPos = strpos($part, ':');
            if ($colonPos === false) continue;

            $property = strtolower(trim(substr($part, 0, $colonPos)));
            $value = trim(substr($part, $colonPos + 1));

            if (in_array($property, $allowedProperties, true) && !empty($value)) {
                $cleaned[] = $property . ': ' . $value;
            }
        }

        return implode('; ', $cleaned);
    }
}
