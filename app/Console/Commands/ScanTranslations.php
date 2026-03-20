<?php

namespace App\Console\Commands;

use App\Services\BladeTranslationScanner;
use Illuminate\Console\Command;

class ScanTranslations extends Command
{
    protected $signature = 'translations:scan
                            {--path=resources/views : Directory to scan}
                            {--json : Output results as JSON}';

    protected $description = 'Scan Blade files for hardcoded English text not wrapped in __()';

    public function handle(BladeTranslationScanner $scanner): int
    {
        $path = base_path($this->option('path'));

        if (! is_dir($path)) {
            $this->error("Directory not found: {$path}");
            return self::FAILURE;
        }

        if (! $this->option('json')) {
            $this->info('Scanning Blade files for hardcoded English text...');
        }

        $results = $scanner->scan($path);

        if (empty($results)) {
            $this->info('✅ No hardcoded English text found!');
            return self::SUCCESS;
        }

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return self::SUCCESS;
        }

        $this->warn(count($results) . ' hardcoded string(s) found:');
        $this->newLine();

        $rows = [];
        foreach ($results as $item) {
            $rows[] = [
                $item['file'],
                $item['line'],
                mb_strlen($item['text']) > 50 ? mb_substr($item['text'], 0, 50) . '…' : $item['text'],
                $item['suggested_key'],
            ];
        }

        $this->table(['File', 'Line', 'Text', 'Suggested Key'], $rows);

        $this->newLine();
        $this->info("Replace hardcoded text with {{ __('key.name') }} in your Blade files.");

        return self::SUCCESS;
    }
}
