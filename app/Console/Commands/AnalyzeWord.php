<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;

class AnalyzeWord extends Command
{
    protected $signature = 'analyze:words {inputFile}';
    protected $description = 'Собрать общую статистику для всех слов из файла и вывести в виде таблицы';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $inputFile = $this->argument('inputFile');

        // Проверяем наличие входного файла
        if (!file_exists($inputFile)) {
            $this->error("Файл {$inputFile} не найден.");
            return;
        }

        // Читаем строки из входного файла
        $lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->error("Файл {$inputFile} пуст.");
            return;
        }

        $summaryTable = [];

        // Обрабатываем каждую строку
        foreach ($lines as $line) {
            $words = array_map('trim', explode(',', $line)); // Разделяем строку на слова

            foreach ($words as $word) {
                // $records = MainTable::where('content', 'LIKE', "%{$word}%")->get();
                $records = MainTable::mustContain($word)->get();

                $totalRecords = $records->count();
                $categoryStats = $this->getStatistics($records, 'category');
                $yearStats = $this->getYearStatistics($records);

                $summaryTable[] = [
                    'word' => $word,
                    'total' => $totalRecords,
                    'categories' => $this->formatStatSummary($categoryStats),
                    'years' => $this->formatStatSummary($yearStats),
                ];
            }
        }

        // Выводим общую таблицу
        $this->outputTable($summaryTable);
    }

    private function getStatistics($records, $field)
    {
        return $records->groupBy($field)->map(function ($group) {
            return $group->count();
        })->toArray();
    }

    private function getYearStatistics($records)
    {
        // Группируем записи по годам
        $grouped = $records->groupBy(function ($record) {
            $year = $this->extractYear($record->year_publication);
    
            if ($year === null) {
                return 'Не указано';
            }
    
            if ($year < 1938) {
                return 'До 1938 года';
            } elseif ($year >= 1938 && $year <= 1985) {
                return '1938–1985';
            } elseif ($year >= 1986 && $year <= 2024) {
                return '1986–2024';
            } else {
                return 'После 2024 года';
            }
        });
    
        // Подсчитываем количество записей в каждой группе
        $counts = $grouped->map(function ($group) {
            return $group->count();
        });
    
        // Явно задаём порядок сортировки групп
        $sortedOrder = ['До 1938 года', '1938–1985', '1986–2024', 'После 2024 года', 'Не указано'];
    
        // Сортируем статистику по заданному порядку
        $sorted = collect($sortedOrder)->mapWithKeys(function ($key) use ($counts) {
            return [$key => $counts->get($key, 0)];
        });
        return $sorted->toArray();
    }

    private function extractYear($yearString)
    {
        // Ищем год в строке (обычно после запятой)
        if (preg_match('/,?\s*(\d{4})\b/', $yearString, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    private function formatStatSummary($statistics)
    {
        return implode(', ', array_map(
            fn($key, $value) => "{$key}: {$value}",
            array_keys($statistics),
            $statistics
        ));
    }

    private function outputTable($summaryTable)
    {
        $headers = ['Слово', 'Общее количество', 'Категории', 'Годы'];
        $rows = array_map(function ($entry) {
            return [
                $entry['word'],
                $entry['total'],
                $entry['categories'] ?: '—',
                $entry['years'] ?: '—',
            ];
        }, $summaryTable);

        $this->table($headers, $rows);
    }
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ   ӑӗӳҫ

// php artisan analyze:words C:\1\1.txt C:\1\out.txt


// Ниже приведена у меня консольная команда -
// Мне надо сделать так чтобы она считывала данные с файла с:\1\1.txt 
// и записывала статистические данные в файл с:\1\out.txt. 
// Надо записать статистические данные по всем словам в виде обобщенной таблицы, туда данные про контекст не надо добавлять. 
// Как это можно сделать? 


// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Models\MainTable;

// class AnalyzeWord extends Command
// {
//     // Добавляем флаг --no-context
//     protected $signature = 'analyze:word {word} {--no-context : Не выводить контекстную статистику}';

//     protected $description = 'Статистический анализ и поиск контекста для вводимого слова в таблице MainTable';

//     public function __construct()
//     {
//         parent::__construct();
//     }

//     public function handle()
//     {
//         $word = $this->argument('word');
//         $noContext = $this->option('no-context'); // Получаем значение флага
    
//         $records = MainTable::where('content', 'LIKE', "%{$word}%")->get();
//         //  $records = MainTable::whereRaw("BINARY content LIKE ?", ["%{$word}%"])->get();
    
//         if ($records->isEmpty()) {
//             $this->info("Слово '{$word}' не найдено в таблице MainTable.");
//             return;
//         }
    
//         $totalRecords = $records->count();  // Общее количество записей
    
//         // Получаем статистику
//         $genreStats = $this->getStatistics($records, 'genre', $totalRecords);
//         $categoryStats = $this->getStatistics($records, 'category', $totalRecords);
//         $yearStats = $this->getYearStatistics($records, $totalRecords);
    
//         $this->info("Слово '{$word}' найдено в {$totalRecords} записях.");
    
//         // Выводим статистику по жанру, категории и году публикации
//      //s   $this->outputStatistics('genre', $genreStats);
//         $this->outputStatistics('category', $categoryStats);
//         $this->outputStatistics('year_publication', $yearStats);
    
//         // Если флаг --no-context НЕ указан, выводим контексты
//         if (!$noContext) {
//             $this->info("\n=== Контекстная информация ===");
//             $this->outputContexts($records, $word);
//         }
//     }
    
//     private function getStatistics($records, $field, $totalRecords)
//     {
//         return $records->groupBy($field)->map(function ($group) use ($totalRecords) {
//             $count = $group->count();
//             $percentage = ($count / $totalRecords) * 100;
//             return [
//                 'count' => $count,
//                 'percentage' => round($percentage, 2)  // Округляем до двух знаков после запятой
//             ];
//         });
//     }
    
//     private function getYearStatistics($records, $totalRecords)
//     {
//         return $records->groupBy(function ($record) {
//             $year = trim($record->year_publication);
    
//             if (preg_match('/, (\d{4})$/', $year, $matches)) {
//                 return $matches[1];
//             }
    
//             if (preg_match('/^\d{4}$/', $year)) {
//                 return $year;
//             }
    
//             return 'Не указано';
//         })->map(function ($group) use ($totalRecords) {
//             $count = $group->count();
//             $percentage = ($count / $totalRecords) * 100;
//             return [
//                 'count' => $count,
//                 'percentage' => round($percentage, 2)
//             ];
//         })->sortKeys();
//     }
    
//     private function outputStatistics($fieldName, $statistics)
//     {
//         $this->info("\n=== Статистика по полю '{$fieldName}' ===");
//         foreach ($statistics as $fieldValue => $data) {
//             $displayValue = $fieldValue ?: 'Не указано';
//             $this->info("{$displayValue}: {$data['count']} записей ({$data['percentage']}%)");
//         }
//     }

//     private function outputContexts($records, $word)
//     {
//         foreach ($records as $record) {
//             $context = $this->getWordContext($record->content, $word);
//             $this->line("- ID: {$record->id}, Контекст: {$context}");
//         }
//     }

//     private function getWordContext($content, $word, $contextLength = 30)
//     {
//         $content = strip_tags($content);
//         $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
//         $content = preg_replace('/\s+/', ' ', $content);
//         $content = trim($content);

//         $word = trim($word, "'\"");

//         // $content = mb_strtolower($content, 'UTF-8');
//         // $word = mb_strtolower($word, 'UTF-8');

//         $position = mb_stripos($content, $word, 0, 'UTF-8');

//         if ($position === false) {
//             return "Контекст не найден.";
//         }

//         $start = max(0, $position - $contextLength);
//         $end = min(mb_strlen($content, 'UTF-8'), $position + mb_strlen($word, 'UTF-8') + $contextLength);
//         $context = mb_substr($content, $start, $end - $start, 'UTF-8');

//         return "..." . trim($context) . "...";
//     }
// }

// php artisan analyze:word адвокат
// php artisan analyze:word адвокат --no-context


