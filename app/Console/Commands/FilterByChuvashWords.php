<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\Log;

class FilterByChuvashWords extends Command
{
    protected $signature = 'filter:chuvash-words {--threshold=10 : Minimum percentage of Chuvash words} {--dry-run : Check without actually deleting}';
    
    protected $description = 'Remove records where Chuvash words percentage is below threshold';
    
    protected $logFile = 'deleted_chuvash_records.log';

    public function handle()
    {
        $threshold = (int)$this->option('threshold');
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - no records will be deleted');
        }
        
        $this->info("Starting filtering by Chuvash words (threshold: {$threshold}%)...");
        
        // Инициализация лог-файла
        $this->initializeLogFile();
        
        // Подсчет общего количества записей
        $totalRecords = ScrapedData::count();
        $this->info("Total records in database: {$totalRecords}");
        
        if ($totalRecords === 0) {
            $this->info('No records to process.');
            return;
        }
        
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();
        
        $recordsToDelete = [];
        $stats = [
            'total_words' => 0,
            'chuvash_words' => 0,
            'processed' => 0
        ];
        
        // Обрабатываем записи пачками для экономии памяти
        ScrapedData::chunk(100, function ($records) use (&$recordsToDelete, &$stats, $threshold, $progressBar) {
            foreach ($records as $record) {
                $wordStats = $this->analyzeChuvashWords($record->content);
                $stats['total_words'] += $wordStats['total_words'];
                $stats['chuvash_words'] += $wordStats['chuvash_words'];
                $stats['processed']++;
                
                $percentage = $wordStats['total_words'] > 0 ? 
                    ($wordStats['chuvash_words'] / $wordStats['total_words']) * 100 : 0;
                
                if ($percentage < $threshold) {
                    $recordsToDelete[] = [
                        'id' => $record->id,
                        'url' => $record->url,
                        'total_words' => $wordStats['total_words'],
                        'chuvash_words' => $wordStats['chuvash_words'],
                        'percentage' => round($percentage, 2),
                        'title' => $record->title,
                        'author' => $record->author
                    ];
                }
                
                $progressBar->advance();
            }
        });
        
        $progressBar->finish();
        $this->newLine();
        
        $countToDelete = count($recordsToDelete);
        $this->info("Records with less than {$threshold}% Chuvash words: {$countToDelete}");
        
        // Общая статистика
        if ($stats['processed'] > 0) {
            $avgPercentage = $stats['total_words'] > 0 ? 
                ($stats['chuvash_words'] / $stats['total_words']) * 100 : 0;
            
            $this->info("📊 Overall statistics:");
            $this->info("   - Processed records: {$stats['processed']}");
            $this->info("   - Total words: {$stats['total_words']}");
            $this->info("   - Chuvash words: {$stats['chuvash_words']}");
            $this->info("   - Average Chuvash words: " . round($avgPercentage, 2) . "%");
        }
        
        if ($countToDelete === 0) {
            $this->info('✅ No records to delete.');
            return;
        }
        
        // Показать примеры для удаления
        if ($countToDelete > 0) {
            $this->info("\n📋 Sample of records to be deleted (worst first):");
            usort($recordsToDelete, function($a, $b) {
                return $a['percentage'] <=> $b['percentage'];
            });
            
            foreach (array_slice($recordsToDelete, 0, 5) as $index => $record) {
                $this->info("   {$index}. ID: {$record['id']}, URL: {$record['url']}");
                $this->info("      Words: {$record['total_words']}, Chuvash: {$record['chuvash_words']}, Percentage: {$record['percentage']}%");
                $this->info("      Title: " . ($record['title'] ? substr($record['title'], 0, 50) : 'N/A'));
            }
            
            if ($countToDelete > 5) {
                $this->info("   ... and " . ($countToDelete - 5) . " more");
            }
        }
        
        if ($isDryRun) {
            $this->info("\n✅ DRY RUN COMPLETED. {$countToDelete} records would be deleted.");
            
            // Логируем в тестовом режиме (с пометкой DRY RUN)
            $this->logDryRunDeletions($recordsToDelete, $threshold);
            return;
        }
        
        // Подтверждение удаления
        if (!$this->confirm("\n⚠️  Do you really want to delete {$countToDelete} records with less than {$threshold}% Chuvash words?")) {
            $this->info('Operation cancelled.');
            return;
        }
        
        $this->info('Deleting records...');
        
        // Удаляем записи и логируем
        $deletedCount = 0;
        foreach ($recordsToDelete as $record) {
            $deleted = ScrapedData::where('id', $record['id'])->delete();
            
            if ($deleted) {
                $deletedCount++;
                $this->logDeletion($record, $threshold);
            }
        }
        
        $this->info("✅ Successfully deleted {$deletedCount} records.");
        $this->info("📝 Deletion log saved to: " . storage_path($this->logFile));
        
        // Финальная статистика
        $remainingRecords = ScrapedData::count();
        $this->info("📊 Final statistics:");
        $this->info("   - Original total: {$totalRecords}");
        $this->info("   - Deleted: {$deletedCount}");
        $this->info("   - Remaining: {$remainingRecords}");
    }
    
    /**
     * Инициализация лог-файла
     */
    private function initializeLogFile()
    {
        $logPath = storage_path($this->logFile);
        
        // Добавляем заголовок если файл новый
        if (!file_exists($logPath)) {
            $header = "=== Chuvash Words Filter Deletion Log ===\n";
            $header .= "Generated: " . date('Y-m-d H:i:s') . "\n";
            $header .= "Threshold: " . $this->option('threshold') . "%\n";
            $header .= str_repeat("=", 50) . "\n\n";
            
            file_put_contents($logPath, $header, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Логирование удаленной записи
     */
    private function logDeletion(array $record, int $threshold)
    {
        $logPath = storage_path($this->logFile);
        
        $logEntry = "🚫 DELETED RECORD\n";
        $logEntry .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $logEntry .= "ID: " . $record['id'] . "\n";
        $logEntry .= "URL: " . $record['url'] . "\n";
        $logEntry .= "Title: " . ($record['title'] ?: 'N/A') . "\n";
        $logEntry .= "Author: " . ($record['author'] ?: 'N/A') . "\n";
        $logEntry .= "Word Statistics:\n";
        $logEntry .= "  - Total words: " . $record['total_words'] . "\n";
        $logEntry .= "  - Chuvash words: " . $record['chuvash_words'] . "\n";
        $logEntry .= "  - Percentage: " . $record['percentage'] . "% (threshold: {$threshold}%)\n";
        $logEntry .= str_repeat("-", 80) . "\n\n";
        
        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Логирование для тестового режима
     */
    private function logDryRunDeletions(array $records, int $threshold)
    {
        $logPath = storage_path('dry_run_' . $this->logFile);
        
        $header = "=== DRY RUN - Chuvash Words Filter Deletion Log ===\n";
        $header .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $header .= "Threshold: " . $threshold . "%\n";
        $header .= "Total records that WOULD be deleted: " . count($records) . "\n";
        $header .= str_repeat("=", 60) . "\n\n";
        
        file_put_contents($logPath, $header, FILE_APPEND | LOCK_EX);
        
        foreach ($records as $record) {
            $logEntry = "🚫 WOULD DELETE RECORD\n";
            $logEntry .= "ID: " . $record['id'] . "\n";
            $logEntry .= "URL: " . $record['url'] . "\n";
            $logEntry .= "Title: " . ($record['title'] ?: 'N/A') . "\n";
            $logEntry .= "Word Statistics:\n";
            $logEntry .= "  - Total words: " . $record['total_words'] . "\n";
            $logEntry .= "  - Chuvash words: " . $record['chuvash_words'] . "\n";
            $logEntry .= "  - Percentage: " . $record['percentage'] . "% (threshold: {$threshold}%)\n";
            $logEntry .= str_repeat("-", 80) . "\n\n";
            
            file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
        }
        
        $this->info("📝 Dry run log saved to: " . $logPath);
    }
    
    /**
     * Анализирует текст и возвращает статистику по словам
     */
    private function analyzeChuvashWords($content): array
    {
        if (empty($content)) {
            return ['total_words' => 0, 'chuvash_words' => 0];
        }
        
        // Очищаем текст от лишних символов и разбиваем на слова
        $cleanedContent = preg_replace('/[^\p{Cyrillic}\s]/u', ' ', $content);
        $words = preg_split('/\s+/', $cleanedContent, -1, PREG_SPLIT_NO_EMPTY);
        
        $totalWords = count($words);
        $chuvashWords = 0;
        
        // Чувашские символы
        $chuvashChars = ['ӑ', 'ӗ', 'ҫ', 'ӳ', 'Ă', 'Ĕ', 'Ç', 'Ÿ'];
        
        foreach ($words as $word) {
            // Слово должно содержать хотя бы один чувашский символ
            foreach ($chuvashChars as $char) {
                if (mb_stripos($word, $char) !== false) {
                    $chuvashWords++;
                    break;
                }
            }
        }
        
        return [
            'total_words' => $totalWords,
            'chuvash_words' => $chuvashWords
        ];
    }
}

// php artisan filter:chuvash-words