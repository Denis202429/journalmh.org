<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ParallelChvRu;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportParallelChvRuData extends Command
{
    protected $signature = 'import:parallel-chv-ru {file : Path to the JSON file}';
    protected $description = 'Import data from JSON file to parallel_chv_ru table';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalLines = count($lines);
        $imported = 0;
        $failed = 0;

        $this->output->progressStart($totalLines);

        foreach ($lines as $line) {
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                ParallelChvRu::create([
                    'chuvash_text' => $data['chv'] ?? null,
                    'russian_text' => $data['ru'] ?? null,
                    // Остальные поля можно оставить null или добавить значения по умолчанию
                ]);

                $imported++;
            } catch (\Exception $e) {
                Log::error("Failed to import line: {$line}. Error: {$e->getMessage()}");
                $failed++;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info("\nImport completed!");
        $this->info("Total lines processed: {$totalLines}");
        $this->info("Successfully imported: {$imported}");
        $this->info("Failed to import: {$failed}");

        return 0;
    }
}


// php artisan import:parallel-chv-ru "storage/app/corpus/train-00000-of-00001 (1).json"

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
