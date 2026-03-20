<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Setting;
use App\Services\BladeTranslationScanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TranslationController extends Controller
{
    protected function activeLanguages(): Collection
    {
        if (! Schema::hasTable('languages')) {
            return collect([
                (object) ['locale' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true, 'font_type' => 'system', 'font_value' => null],
                (object) ['locale' => 'km', 'name' => 'Khmer', 'flag' => '🇰🇭', 'is_default' => false, 'font_type' => 'system', 'font_value' => null],
            ]);
        }

        $languages = Language::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        if ($languages->isEmpty()) {
            return collect([
                (object) ['locale' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true, 'font_type' => 'system', 'font_value' => null],
                (object) ['locale' => 'km', 'name' => 'Khmer', 'flag' => '🇰🇭', 'is_default' => false, 'font_type' => 'system', 'font_value' => null],
            ]);
        }

        return $languages;
    }

    protected function sourceLanguage(): object
    {
        $languages = $this->activeLanguages();

        $en = $languages->firstWhere('locale', 'en');
        if ($en) {
            return (object) $en;
        }

        $default = $languages->firstWhere('is_default', true);

        return (object) ($default ?? $languages->first());
    }

    protected function langPath(string $locale): string
    {
        return lang_path("{$locale}/app.php");
    }

    protected function jsonLangPath(string $locale): string
    {
        return lang_path("{$locale}.json");
    }

    protected function loadTranslations(string $locale): array
    {
        $path = $this->langPath($locale);

        return file_exists($path) ? (include $path) : [];
    }

    protected function loadJsonTranslations(string $locale): array
    {
        $path = $this->jsonLangPath($locale);

        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }

    protected function writeJsonTranslations(string $locale, array $translations): void
    {
        $path = $this->jsonLangPath($locale);
        ksort($translations);
        file_put_contents($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Flatten a nested array into dot-notation keys.
     */
    protected function flatten(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $dotKey = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value, $dotKey));
            } else {
                $result[$dotKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Expand dot-notation keys into a nested array.
     */
    protected function expand(array $flat): array
    {
        $result = [];

        foreach ($flat as $key => $value) {
            data_set($result, $key, $value);
        }

        return $result;
    }

    /**
     * Write translations to the PHP lang file.
     */
    protected function writeTranslations(string $locale, array $translations): void
    {
        $path = $this->langPath($locale);
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $export = var_export($translations, true);

        // Clean up var_export output for readability
        $export = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $export);
        $export = preg_replace(["/array \(/", "/\)(,?)$/m"], ["[", "]$1"], $export);
        $export = preg_replace("/\=\>\s*\n\s*\[/", '=> [', $export);

        file_put_contents($path, "<?php\n\nreturn {$export};\n");
    }

    public function index(): View
    {
        $languages = $this->activeLanguages();

        $mergedByLocale = [];
        $allKeys = [];

        foreach ($languages as $language) {
            $locale = $language->locale;
            $php = $this->flatten($this->loadTranslations($locale));
            $json = $this->loadJsonTranslations($locale);

            $mergedByLocale[$locale] = array_merge(
                array_combine(array_map(fn ($k) => "app.{$k}", array_keys($php)), array_values($php)) ?: [],
                $json
            );

            $allKeys = array_merge($allKeys, array_keys($mergedByLocale[$locale]));
        }

        $allKeys = array_unique($allKeys);
        sort($allKeys);

        $translations = [];
        foreach ($allKeys as $key) {
            $translations[$key] = [];
            foreach ($languages as $language) {
                $locale = $language->locale;
                $translations[$key][$locale] = $mergedByLocale[$locale][$key] ?? '';
            }
        }

        $sourceLanguage = $this->sourceLanguage();

        return view('admin.translations.index', [
            'translations' => $translations,
            'languages' => $languages,
            'sourceLocale' => $sourceLanguage->locale,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = $request->input('keys', []);
        $languages = $this->activeLanguages();
        $localeValues = $request->input('values', []);

        // Backward-compat payload support for legacy en/km form.
        if (empty($localeValues) && ($request->has('en') || $request->has('km'))) {
            $localeValues = [
                'en' => $request->input('en', []),
                'km' => $request->input('km', []),
            ];
        }

        $phpByLocale = [];
        $jsonByLocale = [];

        foreach ($languages as $language) {
            $phpByLocale[$language->locale] = [];
            $jsonByLocale[$language->locale] = [];
        }

        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key === '') {
                continue;
            }

            // Keys starting with "app." go to PHP files, everything else to JSON
            if (str_starts_with($key, 'app.')) {
                $phpKey = substr($key, 4); // Remove "app." prefix
                foreach ($languages as $language) {
                    $locale = $language->locale;
                    $phpByLocale[$locale][$phpKey] = $localeValues[$locale][$i] ?? '';
                }
            } else {
                foreach ($languages as $language) {
                    $locale = $language->locale;
                    $jsonByLocale[$locale][$key] = $localeValues[$locale][$i] ?? '';
                }
            }
        }

        foreach ($languages as $language) {
            $locale = $language->locale;

            if (! empty($phpByLocale[$locale])) {
                $this->writeTranslations($locale, $this->expand($phpByLocale[$locale]));
            }

            if (! empty($jsonByLocale[$locale])) {
                $this->writeJsonTranslations($locale, $jsonByLocale[$locale]);
            }
        }

        return redirect()->route('admin.translations.index')
            ->with('success', __('Translations saved successfully'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $key = $request->input('key');

        if (! $key) {
            return redirect()->back();
        }

        foreach ($this->activeLanguages() as $language) {
            $locale = $language->locale;
            // Check if it's an app.* key (PHP) or direct key (JSON)
            if (str_starts_with($key, 'app.')) {
                $phpKey = substr($key, 4);
                $translations = $this->flatten($this->loadTranslations($locale));
                unset($translations[$phpKey]);
                $this->writeTranslations($locale, $this->expand($translations));
            } else {
                $jsonTranslations = $this->loadJsonTranslations($locale);
                unset($jsonTranslations[$key]);
                $this->writeJsonTranslations($locale, $jsonTranslations);
            }
        }

        return redirect()->route('admin.translations.index')
            ->with('success', __('Translation deleted successfully'));
    }

    /**
     * Get all configured API keys.
     */
    protected function getApiKeys(): array
    {
        $settings = Setting::instance();
        $keys = $settings->gemini_api_keys;

        if (!empty($keys) && is_array($keys)) {
            return array_values(array_filter($keys, fn($k) => !empty(trim($k))));
        }

        // Fall back to legacy single key
        if (!empty($settings->gemini_api_key)) {
            return [$settings->gemini_api_key];
        }

        return [];
    }

    /**
     * Auto-translate a single key using Gemini API.
     */
    public function autoTranslate(Request $request): JsonResponse
    {
        $locales = array_map(fn ($lang) => $lang->locale, $this->activeLanguages()->all());

        $request->validate([
            'text' => 'required|string|max:5000',
            'target_locale' => 'required|string|in:' . implode(',', $locales),
        ]);

        $apiKeys = $this->getApiKeys();

        if (empty($apiKeys)) {
            return response()->json(['error' => 'Gemini API key not configured. Go to Settings → API Keys to add one.'], 422);
        }

        $text = $request->input('text');
        $targetLocale = $request->input('target_locale');
        $targetLanguage = $this->activeLanguages()->firstWhere('locale', $targetLocale);

        if (! $targetLanguage) {
            return response()->json(['error' => 'Target language is not active.'], 422);
        }

        $targetLang = $targetLanguage->name;
        $sourceLang = 'English';

        $lastError = null;
        foreach ($apiKeys as $apiKey) {
            try {
                $translation = $this->callGeminiTranslation($apiKey, $text, $sourceLang, $targetLang);
                return response()->json(['translation' => $translation]);
            } catch (\Throwable $e) {
                $lastError = $e;
                if ($this->isRateLimitError($e->getMessage())) {
                    continue; // Try next key
                }
                break; // Non-rate-limit error, stop
            }
        }

        return response()->json(['error' => $lastError?->getMessage() ?? 'All API keys exhausted'], 500);
    }

    /**
     * Auto-translate all missing keys using Gemini API with key rotation.
     */
    public function autoTranslateAll(Request $request): JsonResponse
    {
        $apiKeys = $this->getApiKeys();

        if (empty($apiKeys)) {
            return response()->json(['error' => 'Gemini API key not configured. Go to Settings → API Keys to add one.'], 422);
        }

        $languages = $this->activeLanguages();
        $sourceLanguage = $this->sourceLanguage();

        if ($sourceLanguage->locale !== 'en') {
            return response()->json(['error' => 'English language is required as source for auto translation.'], 422);
        }

        $sourcePhp = $this->flatten($this->loadTranslations('en'));
        $sourceJson = $this->loadJsonTranslations('en');

        $targets = $languages->filter(fn ($lang) => $lang->locale !== 'en')->values();
        $totalMissing = 0;

        $missingByLocale = [];
        foreach ($targets as $target) {
            $targetPhp = $this->flatten($this->loadTranslations($target->locale));
            $targetJson = $this->loadJsonTranslations($target->locale);

            $missingPhp = [];
            foreach ($sourcePhp as $key => $value) {
                if (! empty($value) && empty($targetPhp[$key] ?? '')) {
                    $missingPhp[$key] = $value;
                }
            }

            $missingJson = [];
            foreach ($sourceJson as $key => $value) {
                if (! empty($value) && (empty($targetJson[$key] ?? '') || ($targetJson[$key] ?? '') === $value)) {
                    $missingJson[$key] = $value;
                }
            }

            $missingByLocale[$target->locale] = [
                'php' => $missingPhp,
                'json' => $missingJson,
                'phpData' => $targetPhp,
                'jsonData' => $targetJson,
                'name' => $target->name,
            ];

            $totalMissing += count($missingPhp) + count($missingJson);
        }

        if ($totalMissing === 0) {
            return response()->json(['translated' => 0, 'message' => 'All keys are already translated.']);
        }

        $translated = 0;
        $errors = [];
        $currentKeyIndex = 0;

        foreach ($targets as $target) {
            $locale = $target->locale;
            $meta = $missingByLocale[$locale];

            $targetPhp = $meta['phpData'];
            $targetJson = $meta['jsonData'];

            $phpBatches = array_chunk($meta['php'], 20, true);
            foreach ($phpBatches as $batch) {
                if ($currentKeyIndex >= count($apiKeys)) {
                    break 2;
                }

                $result = $this->translateBatch($apiKeys, $currentKeyIndex, $batch, $targetPhp, $errors, 'English', $meta['name']);
                $translated += $result['translated'];
                $currentKeyIndex = $result['keyIndex'];
                $this->writeTranslations($locale, $this->expand($targetPhp));
                usleep(300000);
            }

            $jsonBatches = array_chunk($meta['json'], 20, true);
            foreach ($jsonBatches as $batch) {
                if ($currentKeyIndex >= count($apiKeys)) {
                    break 2;
                }

                $result = $this->translateBatch($apiKeys, $currentKeyIndex, $batch, $targetJson, $errors, 'English', $meta['name']);
                $translated += $result['translated'];
                $currentKeyIndex = $result['keyIndex'];
                $this->writeJsonTranslations($locale, $targetJson);
                usleep(300000);
            }
        }

        return response()->json([
            'translated' => $translated,
            'total' => $totalMissing,
            'errors' => array_slice($errors, 0, 5),
            'keys_used' => min($currentKeyIndex + 1, count($apiKeys)),
        ]);
    }

    /**
     * Translate a batch of keys with API key rotation.
     */
    protected function translateBatch(array $apiKeys, int $currentKeyIndex, array $batch, array &$translations, array &$errors, string $sourceLang, string $targetLang): array
    {
        $translated = 0;
        $batchDone = false;

        while (!$batchDone && $currentKeyIndex < count($apiKeys)) {
            $apiKey = $apiKeys[$currentKeyIndex];
            try {
                $results = $this->callGeminiBatchTranslation($apiKey, $batch, $sourceLang, $targetLang);
                foreach ($results as $key => $value) {
                    $translations[$key] = $value;
                    $translated++;
                }
                $batchDone = true;
            } catch (\Throwable $e) {
                if ($this->isRateLimitError($e->getMessage())) {
                    $currentKeyIndex++;
                    $errors[] = "Key #" . $currentKeyIndex . " rate limited, switching...";
                    continue;
                }
                $errors[] = $e->getMessage();
                // Fall back to individual translations
                foreach ($batch as $key => $value) {
                    $individualDone = false;
                    $tryIndex = $currentKeyIndex;
                    while (!$individualDone && $tryIndex < count($apiKeys)) {
                        try {
                            $translations[$key] = $this->callGeminiTranslation($apiKeys[$tryIndex], $value, $sourceLang, $targetLang);
                            $translated++;
                            $individualDone = true;
                            usleep(200000);
                        } catch (\Throwable $e2) {
                            if ($this->isRateLimitError($e2->getMessage())) {
                                $tryIndex++;
                                continue;
                            }
                            $errors[] = "{$key}: {$e2->getMessage()}";
                            break;
                        }
                    }
                    $currentKeyIndex = $tryIndex;
                }
                $batchDone = true;
            }
        }

        return ['translated' => $translated, 'keyIndex' => $currentKeyIndex];
    }

    /**
     * Call Gemini API to translate a single text.
     */
    protected function callGeminiTranslation(string $apiKey, string $text, string $sourceLang, string $targetLang): string
    {
        $prompt = "Translate from {$sourceLang} to {$targetLang}. "
            . "Return ONLY the translated text. "
            . "Keep :placeholder variables like :name exactly as-is. "
            . "Text: {$text}";

        $model = Setting::instance()->gemini_model ?? 'gemini-2.5-flash';

        $response = Http::timeout(15)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 500,
                ],
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Unknown API error');
            throw new \RuntimeException("Gemini API error: {$error}");
        }

        $result = $response->json('candidates.0.content.parts.0.text', '');

        return trim($result);
    }

    /**
     * Call Gemini API to translate a batch of texts.
     */
    protected function callGeminiBatchTranslation(string $apiKey, array $texts, string $sourceLang, string $targetLang): array
    {
        $lines = [];
        foreach ($texts as $key => $value) {
            $lines[] = "{$key}|||{$value}";
        }

        $prompt = "Translate from {$sourceLang} to {$targetLang}. "
            . "Each input line is key|||text. Return ONLY lines in key|||translation format. "
            . "Keep :placeholder variables exactly as-is. No extra explanations.\n\n"
            . implode("\n", $lines);

        $model = Setting::instance()->gemini_model ?? 'gemini-2.5-flash';

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 4096,
                ],
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Unknown API error');
            throw new \RuntimeException("Gemini API error: {$error}");
        }

        $result = trim($response->json('candidates.0.content.parts.0.text', ''));
        $resultLines = array_filter(explode("\n", $result));

        $translations = [];
        foreach ($resultLines as $line) {
            $parts = explode('|||', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if (isset($texts[$key])) {
                    $translations[$key] = $value;
                }
            }
        }

        return $translations;
    }

    /**
     * Check if an error message indicates a rate limit / quota exceeded error.
     */
    protected function isRateLimitError(string $message): bool
    {
        return str_contains(strtolower($message), 'quota')
            || str_contains(strtolower($message), 'rate limit')
            || str_contains(strtolower($message), 'resource exhausted')
            || str_contains(strtolower($message), '429');
    }

    /**
     * Scan Blade views for hardcoded English text.
     */
    public function scanViews(BladeTranslationScanner $scanner): JsonResponse
    {
        $results = $scanner->scan(resource_path('views'));

        // Filter out strings that already exist as translation values (PHP or JSON)
        $sourceLocale = $this->sourceLanguage()->locale;
        $enPhp = $this->flatten($this->loadTranslations($sourceLocale));
        $enJson = $this->loadJsonTranslations($sourceLocale);
        $existingValues = array_merge(
            array_map('strtolower', array_values($enPhp)),
            array_map('strtolower', array_keys($enJson))  // JSON keys = values for direct translations
        );

        $results = array_values(array_filter($results, function ($item) use ($existingValues) {
            return ! in_array(strtolower($item['text']), $existingValues, true);
        }));

        // Group results by file for clearer display
        $grouped = [];
        foreach ($results as $item) {
            $file = $item['file'];
            if (! isset($grouped[$file])) {
                $grouped[$file] = [];
            }
            $grouped[$file][] = $item;
        }

        return response()->json([
            'results' => $results,
            'grouped' => $grouped,
            'count' => count($results),
        ]);
    }

    /**
     * Add scanned keys to both language files.
     * Uses JSON files for direct string translations.
     */
    public function addScannedKeys(Request $request): JsonResponse
    {
        $request->validate([
            'keys' => 'required|array|min:1',
            'keys.*.key' => 'required|string|max:255',
            'keys.*.text' => 'required|string|max:5000',
        ]);

        $languages = $this->activeLanguages();
        $sourceLocale = $this->sourceLanguage()->locale;

        $jsonByLocale = [];
        foreach ($languages as $language) {
            $jsonByLocale[$language->locale] = $this->loadJsonTranslations($language->locale);
        }

        $added = 0;
        $replacements = [];

        foreach ($request->input('keys') as $item) {
            // Use the text itself as the key for direct translations
            $text = trim($item['text']);

            if ($text === '') {
                continue;
            }

            // Skip if key already exists
            if (isset($jsonByLocale[$sourceLocale][$text])) {
                continue;
            }

            foreach ($languages as $language) {
                $locale = $language->locale;
                $jsonByLocale[$locale][$text] = $locale === $sourceLocale ? $text : '';
            }
            $added++;

            $replacements[] = [
                'key' => $text,
                'text' => $text,
                'snippet' => "{{ __('" . addslashes($text) . "') }}",
            ];
        }

        if ($added > 0) {
            foreach ($languages as $language) {
                $locale = $language->locale;
                $this->writeJsonTranslations($locale, $jsonByLocale[$locale]);
            }
        }

        return response()->json([
            'added' => $added,
            'replacements' => $replacements,
        ]);
    }
}
