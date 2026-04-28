<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\DB;

class RecalculateTextStatsCommand extends Command
{
    protected $signature = 'stats:recalculate 
                            {--id= : ID конкретной статьи для перерасчета}
                            {--limit= : Ограничить количество статей для обработки}
                            {--offset= : Пропустить указанное количество статей}
                            {--force : Применить изменения без подтверждения}';
    
    protected $description = 'Пересчитывает totalSymbols, totalWords и totalSentences на основе поля content';

    public function handle()
    {
        $startTime = microtime(true);
        
        // Базовый запрос
        $query = MainTable::query();
        
        // Фильтр по ID
        if ($id = $this->option('id')) {
            $query->where('id', $id);
            $this->info("Перерасчет для статьи ID: {$id}");
        }
        
        // Применяем limit и offset
        if ($limit = $this->option('limit')) {
            $query->limit((int)$limit);
        }
        
        if ($offset = $this->option('offset')) {
            $query->offset((int)$offset);
        }
        
        $totalRecords = $query->count();
        
        if ($totalRecords === 0) {
            $this->error('Статьи для обработки не найдены!');
            return 1;
        }
        
        $this->warn("Найдено статей для обработки: {$totalRecords}");
        
        // Запрашиваем подтверждение
        if (!$this->option('force') && !$this->confirm('Вы действительно хотите выполнить перерасчет? Это изменит данные в базе.')) {
            $this->info('Операция отменена');
            return 0;
        }
        
        // Создаем прогресс-бар
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->setFormat('verbose');
        $bar->start();
        
        $updated = 0;
        $errors = 0;
        $changes = [];
        
        // Обрабатываем статьи пакетами для оптимизации памяти
        $query->chunk(100, function ($articles) use (&$updated, &$errors, &$changes, $bar) {
            foreach ($articles as $article) {
                try {
                    $oldStats = [
                        'symbols' => $article->totalSymbols,
                        'words' => $article->totalWords,
                        'sentences' => $article->totalSentences,
                    ];
                    
                    // Пересчитываем статистику
                    $newStats = $this->calculateStats($article->content);
                    
                    // Сохраняем изменения
                    $article->totalSymbols = $newStats['symbols'];
                    $article->totalWords = $newStats['words'];
                    $article->totalSentences = $newStats['sentences'];
                    $article->save();
                    
                    // Проверяем, были ли изменения
                    if ($oldStats['symbols'] != $newStats['symbols'] || 
                        $oldStats['words'] != $newStats['words'] || 
                        $oldStats['sentences'] != $newStats['sentences']) {
                        
                        $updated++;
                        $changes[] = [
                            'id' => $article->id,
                            'old' => $oldStats,
                            'new' => $newStats,
                        ];
                    }
                    
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("\nОшибка при обработке статьи ID {$article->id}: " . $e->getMessage());
                }
                
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine(2);
        
        // Выводим статистику
        $this->info('========== РЕЗУЛЬТАТЫ ПЕРЕРАСЧЕТА ==========');
        $this->table(
            ['Показатель', 'Значение'],
            [
                ['Всего обработано статей', $totalRecords],
                ['Статей с изменениями', $updated],
                ['Ошибок', $errors],
                ['Время выполнения', round(microtime(true) - $startTime, 2) . ' сек'],
            ]
        );
        
        // Если были изменения, показываем детали
        if (!empty($changes) && $this->option('verbose')) {
            $this->newLine();
            $this->info('Детали изменений (первые 20):');
            
            $this->table(
                ['ID', 'Было симв.', 'Стало симв.', 'Было слов', 'Стало слов', 'Было предл.', 'Стало предл.'],
                array_map(function($change) {
                    return [
                        $change['id'],
                        number_format($change['old']['symbols'], 0, ',', ' '),
                        number_format($change['new']['symbols'], 0, ',', ' '),
                        number_format($change['old']['words'], 0, ',', ' '),
                        number_format($change['new']['words'], 0, ',', ' '),
                        number_format($change['old']['sentences'], 0, ',', ' '),
                        number_format($change['new']['sentences'], 0, ',', ' '),
                    ];
                }, array_slice($changes, 0, 20))
            );
            
            if (count($changes) > 20) {
                $this->line("... и еще " . (count($changes) - 20) . " изменений");
            }
        }
        
        return 0;
    }
    
    /**
     * Расчет статистики текста
     */
    private function calculateStats($content)
    {
        if (empty($content)) {
            return [
                'symbols' => 0,
                'words' => 0,
                'sentences' => 0,
            ];
        }
        
        // Подсчет символов (включая пробелы)
        $symbols = mb_strlen($content);
        
        // Подсчет слов
        // Убираем лишние пробелы и разбиваем на слова
        $text = preg_replace('/\s+/', ' ', $content);
        $text = trim($text);
        
        if (empty($text)) {
            $words = 0;
        } else {
            $words = str_word_count($text, 0, 'ӳӑӗҫӲӐӖҪ0123456789абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ-');
        }
        
        // Подсчет предложений
        // Учитываем разные варианты окончаний предложений
        $sentences = preg_match_all('/[.!?…]+(?:\s|$)/u', $content, $matches);
        
        // Если не нашли предложений, но текст не пустой - считаем как одно предложение
        if ($sentences === 0 && !empty(trim($content))) {
            $sentences = 1;
        }
        
        return [
            'symbols' => $symbols,
            'words' => $words,
            'sentences' => $sentences,
        ];
    }
}

// # Пересчитать статистику для всех статей (с подтверждением)
// php artisan stats:recalculate

// # Пересчитать для всех статей без подтверждения
// php artisan stats:recalculate --force

// # Пересчитать для конкретной статьи по ID
// php artisan stats:recalculate --id=123

// # Пересчитать с ограничением количества
// php artisan stats:recalculate --limit=100

// # Пересчитать со смещением (например, пропустить первые 500)
// php artisan stats:recalculate --offset=500 --limit=100

// # Пересчитать и показать детали изменений
// php artisan stats:recalculate --verbose