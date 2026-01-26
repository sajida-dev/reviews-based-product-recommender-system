<?php

namespace App\Services\Reviews;

class ReviewPreprocessor
{
    public function preprocess(string $text): string
    {
        // Normalize unicode
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Lowercase
        $text = mb_strtolower($text);

        // Remove HTML
        $text = strip_tags($text);

        // Remove URLs
        $text = preg_replace(
            '/https?:\/\/[^\s]+/i',
            '',
            $text
        );

        // Remove emojis & symbols (keep letters, numbers, spaces)
        $text = preg_replace(
            '/[^\p{L}\p{N}\s]/u',
            '',
            $text
        );

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
