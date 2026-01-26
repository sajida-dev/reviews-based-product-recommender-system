<?php

namespace App\Services\Reviews;

use RuntimeException;

class ReviewTextValidator
{
    public function validate(?string $text): string
    {
        if (! $text) {
            return '';
        }

        $text = trim($text);

        if (! preg_match('/[a-zA-Z0-9]/u', $text)) {
            throw new RuntimeException('Review must contain meaningful text.');
        }

        if (preg_match('/(.)\1{6,}/', $text)) {
            throw new RuntimeException('Review text appears spammy.');
        }

        return $text;
    }
}
