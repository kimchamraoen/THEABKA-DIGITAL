<?php

namespace App\Helpers;

use App\Models\NavLabel;

class NavLabelHelper
{
    public static function getLabel(string $key, string $fallback): string
    {
        $label = NavLabel::findByKey($key);
        $storedLabel = trim((string) ($label?->label ?? ''));

        if ($storedLabel === '') {
            return $fallback;
        }

        // If label was never customized (still default seed), keep locale-aware fallback.
        $defaultLabel = NavLabel::defaultLabelForKey($key);
        if ($defaultLabel !== null && strcasecmp($storedLabel, $defaultLabel) === 0) {
            return $fallback;
        }

        return $storedLabel;
    }

    public static function isVisible(string $key): bool
    {
        $label = NavLabel::findByKey($key);
        return $label ? (bool) $label->is_visible : true;
    }

    public static function sortOrder(string $key, int $fallback = 0): int
    {
        $label = NavLabel::findByKey($key);
        return $label?->sort_order ?? $fallback;
    }
}
