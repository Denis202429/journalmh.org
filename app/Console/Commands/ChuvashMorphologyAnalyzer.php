<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChuvashMorphologyAnalyzer extends Command
{
    protected $signature = 'analyze:chuvash {word : Чувашское слово для анализа}';
    protected $description = 'Производит морфологический анализ чувашского слова';

    // public function handle()
    // {
    //     $word = $this->argument('word');

    //     try {
    //         $service = app(\App\Services\ChuvashMorphologyService::class);
    //         $result = $service->analyze($word);

    //         // Основная информация
    //         $this->info("Анализ слова '{$word}':");
    //         $this->line("Основа: " . $result['root']);
    //         $this->line("Часть речи: " . $result['pos']);

    //         // Новые характеристики
    //         $this->line("Число: " . $result['plural']);
    //         $this->line("Время: " . $result['time']);
    //         $this->line("Лицо: " . $result['face']);
    //         $this->line("Падеж: " . $result['case']);
    //         $this->line("Отрицательность: " . $result['negative']);
    //         $this->line("Инфинитив: " . $result['infinitiv']);

    //         // Вывод аффиксов
    //         if (!empty($result['affixes'])) {
    //             $this->newLine();
    //             $this->info("Аффиксы:");

    //             $rows = array_map(function ($affix) {
    //                 return [
    //                     $affix['affix'] ?? '-',
    //                     $affix['name'] ?? 'не указано',
    //                     $affix['type'] ?? '-',
    //                     $affix['position'] ?? '-',
    //                     $affix['level'] ?? '-'
    //                 ];
    //             }, $result['affixes']);

    //             $this->table(
    //                 ['Аффикс', 'Название', 'Тип', 'Позиция', 'Уровень'],
    //                 $rows
    //             );
    //         } else {
    //             $this->line("Аффиксы не обнаружены");
    //         }

    //         return 0;
    //     } catch (\Exception $e) {
    //         $this->error("Ошибка: " . $e->getMessage());
    //         Log::error("Ошибка анализа слова '{$word}': " . $e->getMessage(), [
    //             'exception' => $e
    //         ]);
    //         return 1;
    //     }
    // }

    public function handle()
    {
        $word = $this->argument('word');

        try {
            $service = app(\App\Services\ChuvashMorphologyService::class);
            $result = $service->analyze($word);

            $this->info("Анализ слова '{$result['word']}':");
            $this->line("Найдено вариантов разбора: " . $result['total_analyses']);
            $this->newLine();

            // Выводим каждый вариант разбора
            foreach ($result['analyses'] as $analysis) {
                $this->info("=== Вариант {$analysis['analysis_number']} ===");
                $this->line("Основа: " . $analysis['root']);
                $this->line("Часть речи: " . $analysis['pos']);

                if (!empty($analysis['affixes_string'])) {
                    $this->line("Аффиксы после основы: " . $analysis['affixes_string']);
                }

                // Основные характеристики
                $this->line("Число: " . $analysis['plural']);
                $this->line("Время: " . $analysis['time']);
                $this->line("Лицо: " . $analysis['face']);
                $this->line("Падеж: " . $analysis['case']);

                // Только для глаголов
                if ($analysis['pos'] === 'verb') {
                    $this->line("Отрицательность: " . $analysis['negative']);
                    $this->line("Инфинитив: " . $analysis['infinitiv']);
                }

                // Вывод детальных аффиксов
                if (!empty($analysis['affixes'])) {
                    $this->newLine();
                    $this->info("Разбор аффиксов:");

                    $rows = array_map(function ($affix) {
                        return [
                            $affix['affix'] ?? '-',
                            $affix['name'] ?? 'не указано',
                            $affix['type'] ?? '-',
                            $affix['position'] ?? '-',
                            $affix['level'] ?? '-'
                        ];
                    }, $analysis['affixes']);

                    $this->table(
                        ['Аффикс', 'Название', 'Тип', 'Позиция', 'Уровень'],
                        $rows
                    );
                } else {
                    $this->line("Аффиксы не обнаружены");
                }

                if (count($result['analyses']) > 1 && $analysis['analysis_number'] < count($result['analyses'])) {
                    $this->newLine();
                    $this->line(str_repeat('-', 60));
                    $this->newLine();
                }
            }

            // Вывод сводной таблицы всех вариантов (если больше одного)
            if ($result['total_analyses'] > 1) {
                $this->newLine();
                $this->info("Сводная таблица всех вариантов:");

                $summaryRows = array_map(function ($analysis) {
                    $row = [
                        $analysis['analysis_number'],
                        $analysis['root'],
                        $analysis['pos'],
                        $analysis['affixes_string'] ?: '(нет)',
                        $analysis['plural'],
                        $analysis['case']
                    ];

                    // Добавляем время, только если оно определено
                    if ($analysis['time'] !== 'не определенно') {
                        $row[] = $analysis['time'];
                    } else {
                        $row[] = '-';
                    }

                    $row[] = $analysis['face'];

                    // Только для глаголов
                    if ($analysis['pos'] === 'verb') {
                        $row[] = $analysis['negative'];
                        $row[] = $analysis['infinitiv'];
                    } else {
                        $row[] = '-';
                        $row[] = '-';
                    }

                    return $row;
                }, $result['analyses']);

                $headers = ['№', 'Основа', 'ЧР', 'Аффиксы', 'Число', 'Падеж', 'Время', 'Лицо'];

                if ($result['analyses'][0]['pos'] === 'verb') {
                    $headers[] = 'Отриц.';
                    $headers[] = 'Инф.';
                }

                $this->table($headers, $summaryRows);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            Log::error("Ошибка анализа слова '{$word}': " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}

// php artisan analyze:chuvash "кӗнекесем"  ҫатӑлтат лашанӑн
// php artisan analyze:chuvash "кайӑпин"
// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
