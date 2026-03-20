<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class ApiSettings extends Component
{
    public string $google_fonts_api_key = '';
    public array $gemini_api_keys = [''];
    public string $gemini_model = 'gemini-2.5-flash';
    public string $translation_source_language = 'en';

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->google_fonts_api_key = $settings->google_fonts_api_key ?? '';
        $this->gemini_model = $settings->gemini_model ?? 'gemini-2.5-flash';
        $this->translation_source_language = $settings->translation_source_language ?? 'en';

        // Load keys array, fall back to legacy single key
        $keys = $settings->gemini_api_keys;
        if (!empty($keys) && is_array($keys)) {
            $this->gemini_api_keys = $keys;
        } elseif (!empty($settings->gemini_api_key)) {
            $this->gemini_api_keys = [$settings->gemini_api_key];
        } else {
            $this->gemini_api_keys = [''];
        }
    }

    public function addKey(): void
    {
        if (count($this->gemini_api_keys) < 10) {
            $this->gemini_api_keys[] = '';
        }
    }

    public function removeKey(int $index): void
    {
        if (count($this->gemini_api_keys) > 1) {
            unset($this->gemini_api_keys[$index]);
            $this->gemini_api_keys = array_values($this->gemini_api_keys);
        }
    }

    public function save(): void
    {
        $this->validate([
            'google_fonts_api_key' => 'nullable|string|max:255',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'google_fonts_api_key' => $this->google_fonts_api_key ?: null,
        ]);

        cache()->forget('google_fonts_list');
        session()->flash('api-message', 'Google Fonts API key saved successfully!');
    }

    public function testKey(): void
    {
        $key = $this->google_fonts_api_key ?: config('services.google_fonts.api_key', '');

        if (empty($key)) {
            session()->flash('api-error', 'No API key provided.');
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->get("https://www.googleapis.com/webfonts/v1/webfonts", [
                    'key' => $key,
                    'sort' => 'popularity',
                ]);

            if ($response->successful()) {
                $count = count($response->json('items', []));
                session()->flash('api-message', "API key is valid! Found {$count} fonts.");
            } else {
                session()->flash('api-error', 'API key is invalid or expired. Status: ' . $response->status());
            }
        } catch (\Throwable $e) {
            session()->flash('api-error', 'Connection failed: ' . $e->getMessage());
        }
    }

    public function saveGemini(): void
    {
        $this->validate([
            'gemini_api_keys' => 'required|array|min:1',
            'gemini_api_keys.*' => 'nullable|string|max:500',
            'gemini_model' => 'required|string|max:100',
            'translation_source_language' => 'required|in:en,km',
        ]);

        // Filter out empty keys
        $validKeys = array_values(array_filter($this->gemini_api_keys, fn($k) => !empty(trim($k))));

        $settings = Setting::instance();
        $settings->update([
            'gemini_api_key' => $validKeys[0] ?? null, // keep legacy field in sync
            'gemini_api_keys' => !empty($validKeys) ? $validKeys : null,
            'gemini_model' => $this->gemini_model,
            'translation_source_language' => $this->translation_source_language,
        ]);

        // Keep at least one empty slot if all removed
        if (empty($validKeys)) {
            $this->gemini_api_keys = [''];
        } else {
            $this->gemini_api_keys = $validKeys;
        }

        session()->flash('gemini-message', count($validKeys) . ' API key(s) saved successfully!');
    }

    public function testGeminiKey(int $index = 0): void
    {
        $key = trim($this->gemini_api_keys[$index] ?? '');

        if (empty($key)) {
            session()->flash('gemini-error', "Key #" . ($index + 1) . ": No API key provided.");
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->gemini_model}:generateContent?key={$key}", [
                    'contents' => [
                        ['parts' => [['text' => 'Respond with only the word "OK"']]]
                    ],
                ]);

            if ($response->successful()) {
                session()->flash('gemini-message', "Key #" . ($index + 1) . " is valid! Connection successful.");
            } else {
                $error = $response->json('error.message', 'Unknown error');
                session()->flash('gemini-error', "Key #" . ($index + 1) . ": " . $error);
            }
        } catch (\Throwable $e) {
            session()->flash('gemini-error', "Key #" . ($index + 1) . ": Connection failed - " . $e->getMessage());
        }
    }

    public function testAllKeys(): void
    {
        $validKeys = array_filter($this->gemini_api_keys, fn($k) => !empty(trim($k)));
        if (empty($validKeys)) {
            session()->flash('gemini-error', 'No API keys to test.');
            return;
        }

        $results = [];
        foreach ($validKeys as $i => $key) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->gemini_model}:generateContent?key={$key}", [
                        'contents' => [
                            ['parts' => [['text' => 'Respond with only the word "OK"']]]
                        ],
                    ]);

                $results[] = "Key #" . ($i + 1) . ": " . ($response->successful() ? '✓ Valid' : '✗ ' . $response->json('error.message', 'Failed'));
            } catch (\Throwable $e) {
                $results[] = "Key #" . ($i + 1) . ": ✗ " . $e->getMessage();
            }
        }

        session()->flash('gemini-message', implode(' | ', $results));
    }

    public function render()
    {
        return view('livewire.admin.api-settings');
    }
}
