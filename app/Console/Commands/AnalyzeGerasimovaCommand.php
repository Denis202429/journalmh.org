<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\DB;

class AnalyzeGerasimovaCommand extends Command
{
    protected $signature = 'analyze:gerasimova';
    protected $description = 'Анализирует тексты Герасимовой Н.Г. на наличие лишних пробелов и других аномалий';

    public function handle()
    {
        $correctorName = 'Герасимова Н.Г.';
        
        $this->info("========================================");
        $this->info("АНАЛИЗ ТЕКСТОВ КОРРЕКТОРА: {$correctorName}");
        $this->info("========================================");

        // Получаем все статьи Герасимовой
        $articles = MainTable::where('corrector', $correctorName)
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->get();

        $totalArticles = $articles->count();
        
        if ($totalArticles === 0) {
            $this->error("Статьи не найдены!");
            return;
        }

        $this->info("Найдено статей: {$totalArticles}");
        $this->newLine();

        // Общая статистика
        $totalSymbols = $articles->sum('totalSymbols');
        $totalWords = $articles->sum('totalWords');
        
        $this->table(
            ['Показатель', 'Значение'],
            [
                ['Всего статей', $totalArticles],
                ['Всего символов', number_format($totalSymbols, 0, ',', ' ')],
                ['Всего слов', number_format($totalWords, 0, ',', ' ')],
                ['Средняя длина слова (символов)', round($totalSymbols / $totalWords, 2)],
            ]
        );
        
        $this->newLine();
        $this->info("========================================");
        $this->info("ДЕТАЛЬНЫЙ АНАЛИЗ ПРОБЕЛОВ");
        $this->info("========================================");

        $stats = [
            'articles_with_multiple_spaces' => 0,
            'articles_with_tabs' => 0,
            'articles_with_line_breaks' => 0,
            'total_multiple_spaces_count' => 0,
            'total_tabs_count' => 0,
            'total_line_breaks_count' => 0,
            'articles_with_leading_trailing_spaces' => 0,
            'articles_with_extra_spaces_around_punctuation' => 0,
        ];

        $problematicArticles = [];

        foreach ($articles as $article) {
            $content = $article->content;
            $problems = [];
            
            // Проверка на множественные пробелы (2 и более подряд)
            $multipleSpacesCount = preg_match_all('/ {2,}/', $content, $matches);
            if ($multipleSpacesCount > 0) {
                $stats['articles_with_multiple_spaces']++;
                $stats['total_multiple_spaces_count'] += $multipleSpacesCount;
                $problems[] = "множественные пробелы: {$multipleSpacesCount}";
            }
            
            // Проверка на табуляции
            $tabsCount = preg_match_all('/\t/', $content);
            if ($tabsCount > 0) {
                $stats['articles_with_tabs']++;
                $stats['total_tabs_count'] += $tabsCount;
                $problems[] = "табуляции: {$tabsCount}";
            }
            
            // Проверка на переносы строк
            $lineBreaksCount = preg_match_all('/\n/', $content);
            if ($lineBreaksCount > 0) {
                $stats['articles_with_line_breaks']++;
                $stats['total_line_breaks_count'] += $lineBreaksCount;
                $problems[] = "переносы строк: {$lineBreaksCount}";
            }
            
            // Проверка на пробелы в начале и конце
            if (preg_match('/^\s+|\s+$/', $content)) {
                $stats['articles_with_leading_trailing_spaces']++;
                $problems[] = "пробелы в начале/конце";
            }
            
            // Проверка на пробелы вокруг знаков препинания (например, "слово ,слово" или "слово, слово")
            if (preg_match('/\s+[.,!?;:]|[.,!?;:]\s{2,}/', $content)) {
                $stats['articles_with_extra_spaces_around_punctuation']++;
                $problems[] = "лишние пробелы вокруг знаков препинания";
            }

            if (!empty($problems)) {
                $problematicArticles[] = [
                    'id' => $article->id,
                    'title' => mb_substr($article->title_article ?? 'Без названия', 0, 50),
                    'symbols' => $article->totalSymbols,
                    'words' => $article->totalWords,
                    'ratio' => round($article->totalSymbols / max($article->totalWords, 1), 2),
                    'problems' => implode(', ', $problems)
                ];
            }
        }

        // Выводим общую статистику по пробелам
        $this->table(
            ['Тип проблемы', 'Статей с проблемой', 'Всего вхождений'],
            [
                ['Множественные пробелы (2 и более)', $stats['articles_with_multiple_spaces'], $stats['total_multiple_spaces_count']],
                ['Табуляции', $stats['articles_with_tabs'], $stats['total_tabs_count']],
                ['Переносы строк', $stats['articles_with_line_breaks'], $stats['total_line_breaks_count']],
                ['Пробелы в начале/конце', $stats['articles_with_leading_trailing_spaces'], '-'],
                ['Лишние пробелы вокруг знаков препинания', $stats['articles_with_extra_spaces_around_punctuation'], '-'],
            ]
        );

        // Выводим проблемные статьи
        if (!empty($problematicArticles)) {
            $this->newLine();
            $this->info("========================================");
            $this->info("ПРОБЛЕМНЫЕ СТАТЬИ");
            $this->info("========================================");
            
            // Сортируем по соотношению символов к словам (от наибольшего)
            usort($problematicArticles, function($a, $b) {
                return $b['ratio'] <=> $a['ratio'];
            });

            $this->table(
                ['ID', 'Название', 'Символы', 'Слова', 'Симв/слово', 'Проблемы'],
                array_map(function($article) {
                    return [
                        $article['id'],
                        $article['title'],
                        number_format($article['symbols'], 0, ',', ' '),
                        number_format($article['words'], 0, ',', ' '),
                        $article['ratio'],
                        $article['problems']
                    ];
                }, array_slice($problematicArticles, 0, 20)) // Показываем первые 20 самых проблемных
            );
            
            if (count($problematicArticles) > 20) {
                $this->line("... и еще " . (count($problematicArticles) - 20) . " статей");
            }
        }

        // Анализ средней длины слова по каждой статье
        $this->newLine();
        $this->info("========================================");
        $this->info("СТАТИСТИКА ПО СООТНОШЕНИЮ СИМВОЛЫ/СЛОВА");
        $this->info("========================================");

        $ratios = $articles->map(function($article) {
            return [
                'id' => $article->id,
                'ratio' => $article->totalSymbols / max($article->totalWords, 1),
                'symbols' => $article->totalSymbols,
                'words' => $article->totalWords
            ];
        })->sortByDesc('ratio')->values();

        // Вычисляем статистику вручную
        $ratioValues = $ratios->pluck('ratio')->filter()->values();
        $avgRatio = $ratioValues->avg();
        $maxRatio = $ratioValues->max();
        $minRatio = $ratioValues->min();
        
        // Вычисляем медиану вручную
        $sortedRatios = $ratioValues->sort()->values();
        $count = $sortedRatios->count();
        $medianRatio = 0;
        
        if ($count > 0) {
            $middle = floor(($count - 1) / 2);
            if ($count % 2) {
                $medianRatio = $sortedRatios->get($middle);
            } else {
                $medianRatio = ($sortedRatios->get($middle) + $sortedRatios->get($middle + 1)) / 2;
            }
        }

        $this->table(
            ['Статистика', 'Значение'],
            [
                ['Среднее соотношение символов к словам', round($avgRatio, 2)],
                ['Максимальное соотношение', round($maxRatio, 2)],
                ['Минимальное соотношение', round($minRatio, 2)],
                ['Медианное соотношение', round($medianRatio, 2)],
            ]
        );

        // Показываем статьи с аномально высоким соотношением (> 10 символов на слово)
        $this->newLine();
        $anomalies = $ratios->filter(function($item) {
            return $item['ratio'] > 10;
        });

        if ($anomalies->isNotEmpty()) {
            $this->warn("Найдено статей с аномально высоким соотношением символов к словам (> 10): " . $anomalies->count());
            $this->table(
                ['ID', 'Символы', 'Слова', 'Симв/слово'],
                $anomalies->take(10)->map(function($item) {
                    return [
                        $item['id'],
                        number_format($item['symbols'], 0, ',', ' '),
                        number_format($item['words'], 0, ',', ' '),
                        round($item['ratio'], 2)
                    ];
                })->toArray()
            );
        }

        // Дополнительный анализ: проверка на нестандартные символы
        $this->newLine();
        $this->info("========================================");
        $this->info("АНАЛИЗ НЕСТАНДАРТНЫХ СИМВОЛОВ");
        $this->info("========================================");

        $specialCharsStats = [];
        foreach ($articles as $article) {
            $content = $article->content;
            
            // Ищем нестандартные символы (не буквы, не цифры, не пробелы, не стандартные знаки препинания)
            preg_match_all('/[^\p{L}\p{N}\s\.,!?;:\'\"\-\(\)]/u', $content, $matches);
            
            foreach ($matches[0] as $char) {
                if (!isset($specialCharsStats[$char])) {
                    $specialCharsStats[$char] = 0;
                }
                $specialCharsStats[$char]++;
            }
        }

        if (!empty($specialCharsStats)) {
            arsort($specialCharsStats);
            $this->table(
                ['Символ', 'Количество вхождений', 'Описание'],
                array_map(function($char, $count) {
                    $description = $this->getCharDescription($char);
                    return [$char, $count, $description];
                }, array_keys($specialCharsStats), $specialCharsStats)
            );
        } else {
            $this->info("Нестандартные символы не найдены");
        }
    }

    private function getCharDescription($char)
    {
        $descriptions = [
            '«' => 'левая кавычка-елочка',
            '»' => 'правая кавычка-елочка',
            '—' => 'длинное тире',
            '–' => 'среднее тире',
            '…' => 'многоточие',
            '•' => 'маркер списка',
            '№' => 'знак номера',
            '©' => 'знак copyright',
            '®' => 'знак зарегистрированной торговой марки',
            '™' => 'знак торговой марки',
            '°' => 'знак градуса',
            '±' => 'плюс-минус',
            '×' => 'знак умножения',
            '÷' => 'знак деления',
            '√' => 'знак квадратного корня',
            '∞' => 'знак бесконечности',
            '≈' => 'приблизительно равно',
            '≠' => 'не равно',
            '≤' => 'меньше или равно',
            '≥' => 'больше или равно',
        ];
        
        return $descriptions[$char] ?? 'нестандартный символ';
    }
}


// php artisan analyze:gerasimova