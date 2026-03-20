<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleFontService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://www.googleapis.com/webfonts/v1/webfonts';

    public function __construct()
    {
        // Try to get API key from DB settings first, then fall back to .env
        try {
            $settings = \App\Models\Setting::instance();
            $this->apiKey = $settings->getGoogleFontsApiKey();
        } catch (\Throwable $e) {
            $this->apiKey = config('services.google_fonts.api_key', '');
        }
    }

    /**
     * Fetch all Google Fonts (cached for 24 hours).
     *
     * @return array<int, array{family: string, category: string, variants: array}>
     */
    public function getFonts(): array
    {
        return Cache::remember('google_fonts_list', 86400, function () {
            return $this->fetchFromApi();
        });
    }

    /**
     * Search fonts by name.
     *
     * @return array<int, array{family: string, category: string}>
     */
    public function searchFonts(string $query, int $limit = 20): array
    {
        if (empty($query)) {
            return array_slice($this->getPopularFonts(), 0, $limit);
        }

        $fonts = $this->getFonts();
        $query = strtolower($query);

        $results = array_filter($fonts, function ($font) use ($query) {
            return str_contains(strtolower($font['family']), $query);
        });

        return array_slice(array_values($results), 0, $limit);
    }

    /**
     * Get font family names only.
     *
     * @return array<int, string>
     */
    public function getFontNames(): array
    {
        return array_column($this->getFonts(), 'family');
    }

    /**
     * Get popular/recommended fonts.
     *
     * @return array<int, array{family: string, category: string}>
     */
    public function getPopularFonts(): array
    {
        $popular = [
            'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
            'Poppins', 'Nunito', 'Raleway', 'Ubuntu', 'Playfair Display',
            'Source Sans 3', 'Merriweather', 'PT Sans', 'Noto Sans',
            'Fira Sans', 'Work Sans', 'Quicksand', 'Barlow', 'Mulish',
            'DM Sans',
        ];

        $fonts = $this->getFonts();

        return array_values(array_filter($fonts, function ($font) use ($popular) {
            return in_array($font['family'], $popular);
        }));
    }

    /**
     * Validate if a font family exists.
     */
    public function fontExists(string $family): bool
    {
        $names = $this->getFontNames();
        return in_array($family, $names);
    }

    /**
     * Fetch fonts from the Google Fonts API.
     *
     * @return array<int, array{family: string, category: string, variants: array}>
     */
    protected function fetchFromApi(): array
    {
        try {
            if (empty($this->apiKey)) {
                Log::warning('Google Fonts API key not configured. Using fallback fonts.');
                return $this->getFallbackFonts();
            }

            $response = Http::timeout(10)->get($this->apiUrl, [
                'key' => $this->apiKey,
                'sort' => 'popularity',
            ]);

            if ($response->successful()) {
                $items = $response->json('items', []);

                return array_map(function ($item) {
                    return [
                        'family' => $item['family'],
                        'category' => $item['category'] ?? 'sans-serif',
                        'variants' => $item['variants'] ?? ['regular'],
                    ];
                }, $items);
            }

            Log::error('Google Fonts API returned error: ' . $response->status());
            return $this->getFallbackFonts();

        } catch (\Exception $e) {
            Log::error('Google Fonts API exception: ' . $e->getMessage());
            return $this->getFallbackFonts();
        }
    }

    /**
     * Fallback fonts when API is unavailable.
     *
     * @return array<int, array{family: string, category: string, variants: array}>
     */
    protected function getFallbackFonts(): array
    {
        $fonts = [
            'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
            'Poppins', 'Nunito', 'Raleway', 'Ubuntu', 'Playfair Display',
            'Source Sans 3', 'Merriweather', 'PT Sans', 'Noto Sans',
            'Fira Sans', 'Work Sans', 'Quicksand', 'Barlow', 'Mulish',
            'DM Sans', 'Rubik', 'Karla', 'Josefin Sans', 'Libre Franklin',
            'Cabin', 'Oxygen', 'Overpass', 'Hind', 'Archivo', 'Manrope',
        ];

        return array_map(function ($name) {
            return [
                'family' => $name,
                'category' => 'sans-serif',
                'variants' => ['regular', '500', '600', '700'],
            ];
        }, $fonts);
    }

    /**
     * Clear the fonts cache.
     */
    public function clearCache(): void
    {
        Cache::forget('google_fonts_list');
    }
}
