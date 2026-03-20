<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate([], [
            'default_theme' => 'dark',
            'app_font' => 'Inter',
            // Theme colors
            'color_primary' => '#3b82f6',
            'color_secondary' => '#6366f1',
            'color_accent' => '#8b5cf6',
            'color_success' => '#22c55e',
            'color_warning' => '#eab308',
            'color_danger' => '#ef4444',
            // Background gradients
            'dark_bg_from' => '#172554',
            'dark_bg_via' => '#1e3a5f',
            'dark_bg_to' => '#0f172a',
            'light_bg_from' => '#f3f4f6',
            'light_bg_via' => '#eff6ff',
            'light_bg_to' => '#e5e7eb',
            // Glass settings
            'glass_blur' => '20px',
            'glass_opacity' => '0.1',
            'glass_border_opacity' => '0.2',
        ]);
    }
}
