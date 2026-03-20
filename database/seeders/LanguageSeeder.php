<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::updateOrCreate(
            ['locale' => 'en'],
            [
                'name' => 'English',
                'flag' => '🇬🇧',
                'font_type' => 'system',
                'font_value' => null,
                'is_active' => true,
                'is_default' => true,
            ]
        );

        Language::updateOrCreate(
            ['locale' => 'km'],
            [
                'name' => 'Khmer',
                'flag' => '🇰🇭',
                'font_type' => 'system',
                'font_value' => null,
                'is_active' => true,
                'is_default' => false,
            ]
        );
    }
}
