<?php

namespace App\Http\Concerns;

trait SanitizesSearchInput
{
    protected function sanitizeSearchInput(mixed $value, int $maxLength = 100): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $trimmed = trim((string) $value);

        if ($trimmed === '') {
            return null;
        }

        return mb_substr($trimmed, 0, $maxLength);
    }
}
