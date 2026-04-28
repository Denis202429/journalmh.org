<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\Morphology\MorphologyAnalyzerFactory;
use App\Libraries\Morphology\MorfParser;

class MorphologyAnalyzeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morphology:analyze 
                            {word : Слово для анализа} 
                            {--language=chuvash : Язык для анализа (по умолчанию: chuvash)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выполняет морфологический анализ слова';

    /**
     * Execute the console command.
     *
     * @return int
     */
  public function handle()
{
    $word = $this->argument('word');
    $result = app(\App\Services\ChuvashMorphologyService::class)->analyze($word);

    $this->info("Анализ слова '{$word}':");
    $this->line("Основа: " . $result['root']);
    $this->line("Часть речи: " . $result['pos']);
    
    if (!empty($result['affixes'])) {
        $this->newLine();
        $this->info("Аффиксы:");
        
        // Явно указываем ВСЕ столбцы для вывода
        $this->table(
            ['Аффикс', 'Название', 'Тип', 'Позиция', 'Уровень'], // Все столбцы
            array_map(function($affix) {
                return [
                    $affix['affix'] ?? '-',
                    $affix['name'] ?? '-', // Важно: добавляем название
                    $affix['type'] ?? '-',
                    $affix['position'] ?? '-',
                    $affix['level'] ?? '-'
                ];
            }, $result['affixes'])
        );
    } else {
        $this->line("Аффиксы не обнаружены");
    }
}
    protected function analyzeWord(string $word, string $language): void
    {
        $this->info("Анализ слова '$word' на языке '$language'...");

        try {
            $parser = new MorfParser();
            $result = $parser->analyze($word);

            $this->info("Результаты анализа:");
            $this->info("Слово: " . $result['word']);

            if (empty($result['variants'])) {
                $this->warn("Варианты анализа не найдены");
                return;
            }

            foreach ($result['variants'] as $index => $variant) {
                $this->info("\nВариант " . ($index + 1) . ":");
                $this->info("Лемма: " . $variant['root']);
                $this->info("Часть речи: " . $variant['chastRechi']);
                $this->info("Аффиксы: " . $variant['affix']);
                $this->info("Грамматические признаки:");
                $this->info("  Число: " . $variant['pluralOrNot']);
                $this->info("  Время: " . $variant['vremya']);
                $this->info("  Падеж: " . $variant['padezh']);
                $this->info("  Лицо: " . $variant['face']);
                $this->info("  Отрицательность: " . $variant['negativ']);
                $this->info("  Инфинитив: " . $variant['infinitiv']);
                if (!empty($variant['affixInfo'])) {
                    $this->info("Информация об аффиксах: " . $variant['affixInfo']);
                }
            }
        } catch (\Exception $e) {
            $this->error("Ошибка при анализе слова: " . $e->getMessage());
        }
    }
} 

// $ php artisan morphology:analyze "пултаратпăр" --language=chuvash

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
