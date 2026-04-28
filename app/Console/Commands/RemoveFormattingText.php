<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\DB;

class RemoveFormattingText extends Command
{
    protected $signature = 'remove:formatting-text {--dry-run : Check without actually updating}';
    
    protected $description = 'Remove formatting explanation text from content field';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - no records will be updated');
        }
        
        $this->info('Starting removal of formatting explanation text...');
        
        // Массив строк для удаления
        $textsToRemove = [
            '__aaa|...__ - сӑмахӑн каҫине тепӗр сӑмахпа хатӗрлесси («...» вырӑнне «ааа» пулӗ).',
            '__https://chuvash.org|...__ - сӑмах ҫине тулаш каҫӑ лартасси.',
            '**...** - хулӑм шрифтпа палӑртасси.',
            '~~...~~ - тайлӑк шрифтпа палӑртасси.',
            '___...___ - аялтан чӗрнӗ йӗрпе палӑртасси.'
        ];

        // Поиск записей, содержащих любую из этих строк
        $query = ScrapedData::query();
        foreach ($textsToRemove as $text) {
            $query->orWhere('content', 'LIKE', '%' . $text . '%');
        }
        
        $recordsWithText = $query->get();
        $countToUpdate = $recordsWithText->count();
        
        $this->info("Records containing formatting text found: {$countToUpdate}");
        
        if ($countToUpdate === 0) {
            $this->info('✅ No records contain the formatting text.');
            return;
        }
        
        // Показать несколько примеров
        if ($countToUpdate > 0) {
            $this->info("\n📋 Sample of records to be updated:");
            $recordsWithText->take(3)->each(function($record, $index) use ($textsToRemove) {
                $this->info("   {$index}. ID: {$record->id}, URL: {$record->url}");
                
                // Показать какие строки найдены в этом контенте
                foreach ($textsToRemove as $text) {
                    if (strpos($record->content, $text) !== false) {
                        $this->info("      Found: " . substr($text, 0, 50) . "...");
                    }
                }
                
                $contentPreview = substr($record->content, 0, 150);
                $this->info("      Content preview: {$contentPreview}...");
            });
            
            if ($countToUpdate > 3) {
                $this->info("   ... and " . ($countToUpdate - 3) . " more");
            }
        }
        
        if ($isDryRun) {
            $this->info("\n✅ DRY RUN COMPLETED. {$countToUpdate} records would be updated.");
            return;
        }
        
        // Подтверждение обновления
        if (!$this->confirm("\n⚠️  Do you really want to remove formatting text from {$countToUpdate} records?")) {
            $this->info('Operation cancelled.');
            return;
        }
        
        $this->info('Updating records...');
        
        $updatedCount = 0;
        $totalReplacements = 0;
        
        foreach ($recordsWithText as $record) {
            $newContent = $record->content;
            $replacementsInRecord = 0;
            
            // Заменяем каждую строку на пробел
            foreach ($textsToRemove as $text) {
                $newContent = str_replace($text, ' ', $newContent);
                if ($newContent !== $record->content) {
                    $replacementsInRecord++;
                }
            }
            
            // Если контент изменился, обновляем запись
            if ($replacementsInRecord > 0) {
                $record->update(['content' => $newContent]);
                $updatedCount++;
                $totalReplacements += $replacementsInRecord;
            }
        }
        
        $this->info("✅ Successfully updated {$updatedCount} records.");
        $this->info("📊 Total replacements made: {$totalReplacements}");
        
        // Статистика по каждой строке
        $this->info("\n📋 Replacements by text:");
        foreach ($textsToRemove as $text) {
            $count = ScrapedData::where('content', 'LIKE', '%' . $text . '%')->count();
            $this->info("   - " . substr($text, 0, 30) . "...: {$count} records");
        }
    }
}

// php artisan remove:formatting-text

// '__aaa|...__ - сӑмахӑн каҫине тепӗр сӑмахпа хатӗрлесси («...» вырӑнне «ааа» пулӗ).',

// '__https://chuvash.org|...__ - сӑмах ҫине тулаш каҫӑ лартасси.',

// '**...** - хулӑм шрифтпа палӑртасси.',

// '~~...~~ - тайлӑк шрифтпа палӑртасси.',

// '___...___ - аялтан чӗрнӗ йӗрпе палӑртасси.'