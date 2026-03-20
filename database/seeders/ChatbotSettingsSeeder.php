<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'OPENAI_API_KEY', 'value' => null],
            ['key' => 'GEMINI_API_KEY', 'value' => null],
            ['key' => 'CHATBOT_DEFAULT_PROVIDER', 'value' => 'auto'],
            ['key' => 'CHATBOT_ENABLED', 'value' => '1'],
            ['key' => 'CHATBOT_ALLOW_USER_API_KEY', 'value' => '0'],
            ['key' => 'CHATBOT_SYSTEM_PROMPT', 'value' => 'You are a helpful assistant for Source Share. Answer questions clearly and concisely.'],
        ];

        foreach ($settings as $setting) {
            DB::table('chatbot_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
