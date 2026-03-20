<?php

namespace App\Console\Commands;

use App\Services\BladeTranslationScanner;
use Illuminate\Console\Command;

class ApplyTranslations extends Command
{
    protected $signature = 'translations:apply
                            {--dry-run : Show what would be changed without modifying files}
                            {--file= : Only process a specific file}';

    protected $description = 'Replace hardcoded English text in Blade files with __() translation calls';

    public function handle(BladeTranslationScanner $scanner): int
    {
        $path = resource_path('views');
        $dryRun = $this->option('dry-run');
        $specificFile = $this->option('file');

        $this->info('Scanning for hardcoded text...');
        $results = $scanner->scan($path);

        if (empty($results)) {
            $this->info('✅ No hardcoded English text found!');
            return self::SUCCESS;
        }

        // Group by file
        $byFile = [];
        foreach ($results as $item) {
            $file = base_path($item['file']);
            if ($specificFile && !str_ends_with($file, $specificFile)) {
                continue;
            }
            if (!isset($byFile[$file])) {
                $byFile[$file] = [];
            }
            $byFile[$file][] = $item;
        }

        $totalReplaced = 0;
        $totalFailed = 0;

        foreach ($byFile as $file => $items) {
            $this->newLine();
            $this->info("Processing: " . basename($file));

            $content = file_get_contents($file);
            $originalContent = $content;
            $replacedInFile = 0;

            // Sort by line number descending so replacements don't shift line numbers
            usort($items, fn($a, $b) => $b['line'] - $a['line']);

            foreach ($items as $item) {
                $text = $item['text'];
                $line = $item['line'];

                // Skip if already wrapped in __()
                if (preg_match('/\{\{\s*__\s*\([\'"]' . preg_quote($text, '/') . '[\'"]\s*\)\s*\}\}/', $content)) {
                    $this->line("  <fg=yellow>⊘</> L{$line}: Already translated: " . substr($text, 0, 40));
                    continue;
                }

                // Try different replacement patterns
                $replaced = false;

                // Pattern 1: Text inside HTML tags like <h1>Text</h1>
                $pattern1 = '/(>[^<]*?)(' . preg_quote($text, '/') . ')([^<]*?<)/s';
                if (preg_match($pattern1, $content) && !$replaced) {
                    $translationCall = "{{ __('" . $this->escapeForBlade($text) . "') }}";
                    $newContent = preg_replace($pattern1, '$1' . $translationCall . '$3', $content, 1);
                    if ($newContent !== $content) {
                        $content = $newContent;
                        $replaced = true;
                    }
                }

                // Pattern 2: Text as HTML content (standalone or with whitespace)
                if (!$replaced) {
                    $pattern2 = '/(\s*)' . preg_quote($text, '/') . '(\s*<)/s';
                    if (preg_match($pattern2, $content)) {
                        $translationCall = "{{ __('" . $this->escapeForBlade($text) . "') }}";
                        $newContent = preg_replace($pattern2, '$1' . $translationCall . '$2', $content, 1);
                        if ($newContent !== $content) {
                            $content = $newContent;
                            $replaced = true;
                        }
                    }
                }

                // Pattern 3: Text inside option tags
                if (!$replaced) {
                    $pattern3 = '/(<option[^>]*>)' . preg_quote($text, '/') . '(<\/option>)/';
                    if (preg_match($pattern3, $content)) {
                        $translationCall = "{{ __('" . $this->escapeForBlade($text) . "') }}";
                        $newContent = preg_replace($pattern3, '$1' . $translationCall . '$2', $content, 1);
                        if ($newContent !== $content) {
                            $content = $newContent;
                            $replaced = true;
                        }
                    }
                }

                // Pattern 4: Plain text between tags
                if (!$replaced) {
                    $lines = explode("\n", $content);
                    $targetLine = $lines[$line - 1] ?? null;
                    if ($targetLine && str_contains($targetLine, $text)) {
                        $translationCall = "{{ __('" . $this->escapeForBlade($text) . "') }}";
                        $lines[$line - 1] = str_replace($text, $translationCall, $targetLine);
                        $newContent = implode("\n", $lines);
                        if ($newContent !== $content) {
                            $content = $newContent;
                            $replaced = true;
                        }
                    }
                }

                if ($replaced) {
                    $replacedInFile++;
                    $this->line("  <fg=green>✓</> L{$line}: " . substr($text, 0, 50));
                } else {
                    $totalFailed++;
                    $this->line("  <fg=red>✗</> L{$line}: Could not replace: " . substr($text, 0, 40));
                }
            }

            if ($replacedInFile > 0) {
                if (!$dryRun) {
                    file_put_contents($file, $content);
                    $this->info("  → Saved {$replacedInFile} replacement(s)");
                } else {
                    $this->warn("  → Would save {$replacedInFile} replacement(s) [DRY RUN]");
                }
                $totalReplaced += $replacedInFile;
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Total replaced: {$totalReplaced}");
        if ($totalFailed > 0) {
            $this->warn("Failed to replace: {$totalFailed}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment("This was a dry run. Run without --dry-run to apply changes.");
        }

        return self::SUCCESS;
    }

    /**
     * Escape text for use in Blade __() calls
     */
    protected function escapeForBlade(string $text): string
    {
        // Escape single quotes
        return str_replace("'", "\\'", $text);
    }
}
