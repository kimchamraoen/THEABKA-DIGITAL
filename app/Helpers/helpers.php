<?php

use App\Helpers\IconHelper;
use App\Helpers\NavLabelHelper;

if (!function_exists('get_icon')) {
    function get_icon(string $key, string $fallback = ''): string
    {
        return IconHelper::getIcon($key, $fallback);
    }
}

if (!function_exists('get_nav_label')) {
    function get_nav_label(string $key, string $fallback): string
    {
        return NavLabelHelper::getLabel($key, $fallback);
    }
}

if (!function_exists('is_nav_visible')) {
    function is_nav_visible(string $key): bool
    {
        return NavLabelHelper::isVisible($key);
    }
}

if (!function_exists('get_nav_sort_order')) {
    function get_nav_sort_order(string $key, int $fallback = 0): int
    {
        return NavLabelHelper::sortOrder($key, $fallback);
    }
}
