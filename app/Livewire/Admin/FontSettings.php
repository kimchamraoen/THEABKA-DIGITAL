<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use App\Models\Setting;
use App\Services\GoogleFontService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class FontSettings extends Component
{
    use WithFileUploads;

    public array $languages = [];
    public array $fontTypeByLocale = [];
    public array $fontValueByLocale = [];
    public array $fontUploads = [];
    public array $previewSamples = [];

    protected GoogleFontService $fontService;

    public function boot(GoogleFontService $fontService): void
    {
        $this->fontService = $fontService;
    }

    public function mount(): void
    {
        $settings = Setting::instance();

        Language::ensureDefaults();

        $this->languages = Language::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'locale', 'flag', 'font_type', 'font_value'])
            ->toArray();

        foreach ($this->languages as $language) {
            $locale = $language['locale'];
            $defaultType = $language['font_type'] ?: 'system';
            $defaultValue = $language['font_value'];

            if (in_array($locale, ['en', 'km'], true)) {
                $settingsType = $settings->{'font_type_' . $locale} ?? null;
                $settingsValue = $settings->{'font_value_' . $locale} ?? null;
                $defaultType = $settingsType ?: $defaultType;
                $defaultValue = $settingsValue ?: $defaultValue;
            }

            $this->fontTypeByLocale[$locale] = $defaultType ?: 'system';
            $this->fontValueByLocale[$locale] = $defaultValue ?? '';
            $this->previewSamples[$locale] = $this->previewSampleForLocale($locale);
        }
    }

    public function saveFont(): void
    {
        $rules = [];

        foreach ($this->languages as $language) {
            $locale = $language['locale'];
            $rules["fontTypeByLocale.{$locale}"] = 'required|in:system,google,custom';
            $rules["fontValueByLocale.{$locale}"] = 'nullable|string|max:255';
            $rules["fontUploads.{$locale}"] = 'nullable|file|mimes:ttf,woff,woff2|max:10240';
        }

        $this->validate($rules);

        $settings = Setting::instance();
        $settingsPayload = [];

        $fontsDir = storage_path('app/public/fonts');
        if (! File::isDirectory($fontsDir)) {
            File::makeDirectory($fontsDir, 0755, true);
        }

        foreach ($this->languages as $languageData) {
            $language = Language::find($languageData['id']);
            if (! $language) {
                continue;
            }

            $locale = $language->locale;
            $type = $this->fontTypeByLocale[$locale] ?? 'system';
            $value = trim((string) ($this->fontValueByLocale[$locale] ?? ''));

            if ($type === 'system') {
                $value = '';
            }

            if ($type === 'custom') {
                $upload = $this->fontUploads[$locale] ?? null;
                if ($upload instanceof TemporaryUploadedFile) {
                    $filename = $locale . '_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $upload->getClientOriginalExtension();
                    $upload->storeAs('fonts', $filename, 'public');
                    $value = $filename;
                }
            }

            $language->update([
                'font_type' => $type,
                'font_value' => $value !== '' ? $value : null,
            ]);

            if (in_array($locale, ['en', 'km'], true)) {
                $settingsPayload['font_type_' . $locale] = $type;
                $settingsPayload['font_value_' . $locale] = $value !== '' ? $value : null;
            }

            $this->fontValueByLocale[$locale] = $value;
        }

        if (! empty($settingsPayload)) {
            $settings->update($settingsPayload);
        }

        // Ensure subsequent requests and SPA navigations use fresh locale font data.
        Cache::forget('active_languages');
        Cache::forget('active_locales');
        Cache::forget('app_settings');

        $effectiveFont = Setting::instance()->resolveLocaleFontConfig(app()->getLocale());

        session()->flash('message', 'Font settings updated successfully.');
        $this->dispatch('font-updated', fontFamily: $effectiveFont['bodyFontFamily'], fontUrl: $effectiveFont['fontUrl']);
    }

    public function previewSampleForLocale(string $locale): string
    {
        return match ($locale) {
            'km' => 'សូមស្វាគមន៍មកកាន់ប្រព័ន្ធ',
            'en' => 'The quick brown fox jumps',
            default => 'Hello World 123',
        };
    }

    public function googleFontSuggestions(string $locale): array
    {
        $search = trim((string) ($this->fontValueByLocale[$locale] ?? ''));
        return $this->fontService->searchFonts($search, 20);
    }

    public function getCustomFontUrl(string $locale): ?string
    {
        $value = trim((string) ($this->fontValueByLocale[$locale] ?? ''));
        if ($value === '') {
            return null;
        }

        return asset('storage/fonts/' . ltrim($value, '/'));
    }

    public function render()
    {
        return view('livewire.admin.font-settings');
    }
}
