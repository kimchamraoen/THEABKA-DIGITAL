<?php

namespace App\Services;

use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BladeTranslationScanner
{
    /** Paths to never scan (relative to resources/views/) */
    protected array $excludedPaths = [
        'admin/translations/',  // Only exclude translation views to avoid self-referencing
        'vendor/',
    ];

    /**
     * Scan a directory recursively for hardcoded English text in Blade files.
     */
    public function scan(string $directory): array
    {
        $results = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getRealPath();
            if (! str_ends_with($filePath, '.blade.php')) {
                continue;
            }

            if ($this->isExcludedPath($filePath)) {
                continue;
            }

            $found = $this->scanFile($filePath);
            foreach ($found as &$item) {
                $item['file'] = $this->relativePath($filePath);
                $item['suggested_key'] = $this->suggestKey($filePath, $item['text']);
                $item['confidence'] = $this->scoreConfidence($item['text']);
            }
            unset($item);

            $results = array_merge($results, $found);
        }

        // Deduplicate by text+file
        $seen = [];
        $unique = [];
        foreach ($results as $r) {
            $hash = $r['file'] . '|' . $r['text'];
            if (! isset($seen[$hash])) {
                $seen[$hash] = true;
                $unique[] = $r;
            }
        }

        // Filter: only return high-confidence results by default
        return array_values(array_filter($unique, fn($r) => $r['confidence'] === 'high'));
    }

    /**
     * Score the confidence that a string is real translatable text.
     * "high"  = full sentences (3+ words), capitalized labels, clear UI text
     * "low"   = single words, fragments, or ambiguous strings
     */
    protected function scoreConfidence(string $text): string
    {
        $wordCount = str_word_count($text);

        // Full sentences/phrases with 3+ words are high confidence
        if ($wordCount >= 3 && preg_match('/^[A-Z]/', $text)) {
            return 'high';
        }

        // Two-word phrases starting with capital letter
        if ($wordCount === 2 && preg_match('/^[A-Z][a-z]+ [A-Z]?/', $text)) {
            return 'high';
        }

        // Single capitalized word of 4+ chars (common UI labels)
        if ($wordCount === 1 && preg_match('/^[A-Z][a-z]{3,}$/', $text)) {
            return 'high';
        }

        // Anything else is low confidence
        return 'low';
    }

    /**
     * Check if a file path should be excluded from scanning.
     */
    protected function isExcludedPath(string $filePath): bool
    {
        $viewsDir = resource_path('views') . '/';
        $relative = str_replace($viewsDir, '', $filePath);

        foreach ($this->excludedPaths as $excluded) {
            if (str_starts_with($relative, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scan a single file for hardcoded English text.
     */
    protected function scanFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        $results = [];
        $inScriptTag = false;
        $inStyleTag = false;
        $inPhpBlock = false;

        foreach ($lines as $lineNum => $line) {
            $trimmed = trim($line);

            // Track <script> blocks
            if (preg_match('/<script\b/i', $trimmed)) {
                $inScriptTag = true;
            }
            if (preg_match('/<\/script>/i', $trimmed)) {
                $inScriptTag = false;
                continue;
            }

            // Track <style> blocks
            if (preg_match('/<style\b/i', $trimmed)) {
                $inStyleTag = true;
            }
            if (preg_match('/<\/style>/i', $trimmed)) {
                $inStyleTag = false;
                continue;
            }

            // Track @php blocks
            if (preg_match('/^@php\b/', $trimmed)) {
                $inPhpBlock = true;
            }
            if (preg_match('/^@endphp\b/', $trimmed)) {
                $inPhpBlock = false;
                continue;
            }

            // Skip lines inside script/style/php blocks entirely
            if ($inScriptTag || $inStyleTag || $inPhpBlock) {
                continue;
            }

            $found = $this->extractHardcodedStrings($line);
            foreach ($found as $text) {
                $results[] = [
                    'line' => $lineNum + 1,
                    'text' => $text,
                ];
            }
        }

        return $results;
    }

    /**
     * Extract hardcoded English strings from a single line.
     */
    protected function extractHardcodedStrings(string $line): array
    {
        $trimmed = trim($line);

        // Skip blank lines, comments, pure Blade directives, PHP lines
        if ($trimmed === ''
            || str_starts_with($trimmed, '{{--')
            || str_starts_with($trimmed, '<!--')
            || str_starts_with($trimmed, '//')
            || str_starts_with($trimmed, '/*')
            || str_starts_with($trimmed, '*')
            || str_starts_with($trimmed, '<?php')
            || str_starts_with($trimmed, 'use ')
            || preg_match('/^@(php|endphp|if|elseif|else|endif|foreach|endforeach|for|endfor|while|endwhile|switch|case|break|default|endswitch|forelse|empty|endforelse|unless|endunless|isset|endisset|empty|endempty|auth|endauth|guest|endguest|can|endcan|cannot|endcannot|section|endsection|yield|extends|include|push|endpush|stack|once|endonce|prepend|endprepend|slot|endslot|component|endcomponent|props|aware|class|style|error|enderror|production|endproduction|env|endenv|method|csrf|dd|dump|vite|livewireStyles|livewireScripts|livewire|persist|endpersist|teleport|endteleport)\b/', $trimmed)
        ) {
            return [];
        }

        $found = [];

        $cleaned = $line;

        // Remove Blade echo statements {{ ... }}, {!! ... !!}
        $cleaned = preg_replace('/\{\{.*?\}\}/', '', $cleaned);
        $cleaned = preg_replace('/\{!!.*?!!\}/', '', $cleaned);

        // Remove Blade directives with arguments @directive(...)
        $cleaned = preg_replace('/@\w+\s*\([^)]*\)/', '', $cleaned);
        $cleaned = preg_replace('/@\w+/', '', $cleaned);

        // Remove HTML comments
        $cleaned = preg_replace('/<!--.*?-->/', '', $cleaned);

        // Remove ALL attribute="value" and attribute='value' patterns (catches everything)
        $cleaned = preg_replace('/[\w:.@-]+\s*=\s*"[^"]*"/', '', $cleaned);
        $cleaned = preg_replace("/[\\w:.@-]+\\s*=\\s*'[^']*'/", '', $cleaned);

        // Remove any remaining quoted strings (JS strings, etc.)
        $cleaned = preg_replace('/"[^"]*"/', '', $cleaned);
        $cleaned = preg_replace("/(?<!=)'[^']*'/", '', $cleaned);

        // Remove self-closing and opening/closing HTML tags but keep inner text
        $cleaned = preg_replace('/<\/?[\w-]+[^>]*?>/', ' ', $cleaned);

        // Remove URLs
        $cleaned = preg_replace('#https?://\S+#', '', $cleaned);

        // Remove HTML entities
        $cleaned = preg_replace('/&[\w#]+;/', '', $cleaned);

        // Now extract text fragments
        $fragments = preg_split('/\s{2,}/', trim($cleaned));

        foreach ($fragments as $fragment) {
            $text = trim($fragment, " \t\n\r\0\x0B|·,;:\"'");
            // Strip leading dashes, em-dashes, bullets
            $text = preg_replace('/^\s*[\x{2014}\x{2013}\x{2022}\-\*]+\s*/u', '', $text);
            // Strip leading/trailing punctuation fragments
            $text = preg_replace('/^[?!,.:;]+\s*/', '', $text);
            $text = trim($text);

            if ($this->isValidEnglishText($text)) {
                $found[] = $text;
            }
        }

        return $found;
    }

    /**
     * Check if a string qualifies as valid hardcoded English text for translation.
     *
     * CORE RULE: A string is valid only if it contains at least one word starting
     * with a capital letter (or is a recognized English phrase), and does NOT
     * contain code-like characters.
     */
    protected function isValidEnglishText(string $text): bool
    {
        // ── Length gate ──
        if (mb_strlen($text) < 3) {
            return false;
        }

        // ── Must contain letters ──
        if (! preg_match('/[a-zA-Z]{2,}/', $text)) {
            return false;
        }

        // ── HARD REJECT: any of these characters means it's code, not text ──
        // Reject: . = ; { } ( ) [ ] \ / : + * | < > $ @ # ^ ~ ` !
        // Exception: period at end of sentence is OK, ? and ! at end are OK,
        // comma and apostrophe inside words are OK, colon in "Error: message" style,
        // hyphen in compound words, / in "Terms of Service / Privacy"
        if ($this->containsCodeCharacters($text)) {
            return false;
        }

        // ── HARD REJECT: looks like JS/code ──
        // Contains = with assignment patterns
        if (str_contains($text, '=')) {
            return false;
        }

        // Contains semicolons (JS statement separator)
        if (str_contains($text, ';')) {
            return false;
        }

        // Contains curly braces, square brackets, backslash
        if (preg_match('/[{}\[\]\\\\]/', $text)) {
            return false;
        }

        // Contains parentheses
        if (preg_match('/[()]/', $text)) {
            return false;
        }

        // Contains $ (PHP variables)
        if (str_contains($text, '$')) {
            return false;
        }

        // Contains @ (Blade directives)
        if (str_contains($text, '@')) {
            return false;
        }

        // ── HARD REJECT: dot notation (JS property access) ──
        // "btn.disabled", "r.style", "data.total", "img.src" etc.
        if (preg_match('/\b[a-z]\w*\.\w+/', $text)) {
            return false;
        }

        // ── HARD REJECT: colon patterns that are code ──
        // "key: value", "word: something", CSS properties, JS objects
        if (preg_match('/\w+\s*:\s*\S/', $text)) {
            return false;
        }
        // Standalone colon (unless "Error:" style at start followed by a space and capital letter)
        if (str_contains($text, ':') && ! preg_match('/^[A-Z][a-z]+:\s+[A-Z]/', $text)) {
            return false;
        }

        // ── HARD REJECT: Tailwind/CSS patterns ──
        if (preg_match('/\b(px-|py-|pt-|pb-|pl-|pr-|mt-|mb-|ml-|mr-|mx-|my-|w-\d|h-\d|rounded|border-|shadow-|bg-|hover:|focus:|transition|duration-|outline-|ring-|gap-|space-|max-w-|min-w-|max-h-|min-h-|overflow-|opacity-|z-\d|backdrop-|placeholder-|inset-|leading-|tracking-|font-mono|whitespace-|animate-|grid-|flex-|justify-|items-|self-|text-\[|text-xs|text-sm|text-lg|text-xl|text-2xl)/', $text)) {
            return false;
        }

        // ── HARD REJECT: CSS property values ──
        if (preg_match('/\b\d+(px|rem|em|vh|vw|%|ms|s)\b/', $text)) {
            return false;
        }

        // ── HARD REJECT: hex colors ──
        if (preg_match('/#[0-9a-fA-F]{3,8}\b/', $text)) {
            return false;
        }

        // ── HARD REJECT: strings starting/ending with + (concatenation) ──
        if (str_starts_with($text, '+') || str_ends_with($text, '+')) {
            return false;
        }

        // ── HARD REJECT: file paths, extensions ──
        if (preg_match('/\.\w{2,4}$/', $text) && ! preg_match('/[A-Z].*\.\s*$/', $text)) {
            return false;
        }
        if (preg_match('#\w/\w#', $text)) {
            return false;
        }

        // ── HARD REJECT: PHP/Blade/code that leaked ──
        if (preg_match('/[\$@{}]/', $text)) {
            return false;
        }
        if (preg_match('/^<|>/', $text)) {
            return false;
        }

        // ── HARD REJECT: single words that aren't Capitalized or are too short ──
        $wordCount = str_word_count($text);
        if ($wordCount < 2) {
            // Only allow single capitalized English words of 4+ chars like "Submit", "Dashboard"
            // Reject: "The", "You", "Off", "Tip" (too short/common to be standalone translations)
            if (! preg_match('/^[A-Z][a-z]{3,}$/', $text)) {
                return false;
            }
        }

        // ── CORE REQUIREMENT: must contain a capitalized word ──
        // Real English UI text has at least one word with a capital letter,
        // OR it's a well-known lowercase phrase that starts a sentence.
        if (! preg_match('/[A-Z]/', $text)) {
            return false;
        }

        // ── HARD REJECT: no spaces and has mixed case = camelCase variable ──
        if (! str_contains($text, ' ') && preg_match('/[a-z][A-Z]/', $text)) {
            return false;
        }

        // ── HARD REJECT: all lowercase with no spaces (variable name) ──
        if (! str_contains($text, ' ') && preg_match('/^[a-z]/', $text)) {
            return false;
        }

        // ── Validate that the majority of "words" are real words ──
        // Reject if more than half the words contain digits or special chars
        $words = preg_split('/\s+/', $text);
        $codeWordCount = 0;
        foreach ($words as $word) {
            $w = trim($word, '.,!?');
            // Words with digits mixed in, or very short fragments
            if (preg_match('/\d/', $w) && ! preg_match('/^(2FA|2fa|3D|3d|24\/7|1st|2nd|3rd|4th|5th)$/i', $w)) {
                $codeWordCount++;
            }
            // Words with hyphens that look like CSS (word-word-word)
            if (substr_count($w, '-') >= 2 && ! preg_match('/^[A-Z]/', $w)) {
                $codeWordCount++;
            }
        }
        if (count($words) > 0 && $codeWordCount / count($words) > 0.4) {
            return false;
        }

        return true;
    }

    /**
     * Check if text contains characters that indicate code rather than natural language.
     */
    protected function containsCodeCharacters(string $text): bool
    {
        // Allow: letters, numbers, spaces, comma, apostrophe (in words),
        //        hyphen (compound words), period at end, ? and ! at end,
        //        ampersand in "Terms & Privacy"
        // Reject everything else that appears mid-string

        // Strip allowed trailing punctuation for testing
        $testText = rtrim($text, '.?!');

        // These characters in the middle of text = code
        if (preg_match('/[{}()\[\]\\\\<>$@#^~`|]/', $testText)) {
            return true;
        }

        // Equals sign anywhere = code
        if (str_contains($testText, '=')) {
            return true;
        }

        // Semicolons anywhere = code
        if (str_contains($testText, ';')) {
            return true;
        }

        // Period NOT at end of word boundary (i.e., dot notation like obj.prop)
        // Allow: "end of sentence." and "e.g." and abbreviations
        if (preg_match('/\.[a-zA-Z]/', $testText)) {
            // But allow common abbreviations
            if (! preg_match('/\b(e\.g\.|i\.e\.|etc\.|vs\.|Mr\.|Mrs\.|Dr\.|Sr\.|Jr\.)\b/i', $testText)) {
                return true;
            }
        }

        // Forward slash mid-word (file paths) — but allow "Terms / Privacy" style
        if (preg_match('#\w/\w#', $testText)) {
            return true;
        }

        // Colon followed by non-space (CSS prop:value, but not "Error: msg")
        if (preg_match('/:\S/', $testText) && ! preg_match('/^https?:/', $testText)) {
            return true;
        }

        return false;
    }

    /**
     * Generate a suggested translation key from the file path and text.
     */
    public function suggestKey(string $filePath, string $text): string
    {
        $prefix = $this->prefixFromPath($filePath);
        $key = $this->textToKey($text);

        return $prefix . $key;
    }

    /**
     * Derive a key prefix from the Blade file path.
     */
    protected function prefixFromPath(string $filePath): string
    {
        $viewsDir = resource_path('views');
        $relative = str_replace($viewsDir . '/', '', $filePath);
        $relative = str_replace($viewsDir . DIRECTORY_SEPARATOR, '', $relative);

        $relative = preg_replace('/\.blade\.php$/', '', $relative);

        $parts = explode('/', $relative);
        $parts = array_map(fn($p) => Str::snake(Str::camel($p)), $parts);

        if (count($parts) <= 1) {
            return $parts[0] . '.';
        }

        return $parts[0] . '.';
    }

    /**
     * Convert text to a snake_case key.
     */
    protected function textToKey(string $text): string
    {
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', trim($key));

        if (strlen($key) > 40) {
            $key = substr($key, 0, 40);
            $key = preg_replace('/_[^_]*$/', '', $key);
        }

        return $key;
    }

    /**
     * Get relative path from the project root.
     */
    protected function relativePath(string $absolutePath): string
    {
        $base = base_path() . '/';

        return str_replace($base, '', $absolutePath);
    }
}
