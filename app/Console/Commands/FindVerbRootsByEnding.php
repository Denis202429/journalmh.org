<?php

namespace App\Console\Commands;

use App\Models\ChuvAlph;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FindVerbRootsByEnding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chuvash:find-roots 
                            {--endings=ӑ,ӗ,у,ÿ,а,о,я,е : Список окончаний через запятую}
                            {--pos=verb,noun,adj,part,pronoun : Части речи через запятую}
                            {--limit=0 : Лимит вывода (0 = без ограничения)}
                            {--min-length=2 : Минимальная длина основы}
                            {--exclude-infinitiv : Исключить инфинитивы (слова на -ма/-ме)}
                            {--group-by-ending : Группировать результаты по окончаниям}
                            {--export : Экспортировать результаты в CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Находит все основы (корни) слов из словаря с указанными окончаниями для заданных частей речи';

    /**
     * Массив частей речи для фильтрации
     */
    protected array $targetPos = [];

    /**
     * Массив окончаний для поиска
     */
    protected array $targetEndings = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Парсим параметры
        $this->parseParameters();

        $this->showSearchParameters();

        // Выполняем поиск
        $results = $this->findRoots();

        if ($results->isEmpty()) {
            $this->warn('Основы с указанными параметрами не найдены!');
            return 1;
        }

        // Выводим результаты
        $this->showResults($results);

        // Экспорт если нужно
        if ($this->option('export')) {
            $this->exportResults($results);
        }

        return 0;
    }

    /**
     * Парсит параметры из командной строки
     */
    private function parseParameters(): void
    {
        // Парсим окончания
        $endingsInput = $this->option('endings');
        $this->targetEndings = array_map('trim', explode(',', $endingsInput));

        // Парсим части речи
        $posInput = $this->option('pos');
        $this->targetPos = array_map('trim', explode(',', $posInput));

        // Валидация
        if (empty($this->targetEndings)) {
            $this->error('Не указаны окончания для поиска!');
            exit(1);
        }

        if (empty($this->targetPos)) {
            $this->error('Не указаны части речи для поиска!');
            exit(1);
        }
    }

    /**
     * Показывает параметры поиска
     */
    private function showSearchParameters(): void
    {
        $this->info('=== ПАРАМЕТРЫ ПОИСКА ===');
        $this->line("Окончания: " . implode(', ', $this->targetEndings));
        $this->line("Части речи: " . implode(', ', $this->targetPos));
        $this->line("Минимальная длина: " . $this->option('min-length'));

        if ($this->option('exclude-infinitiv')) {
            $this->line("Исключаются инфинитивы (слова на -ма/-ме)");
        }

        if ($this->option('group-by-ending')) {
            $this->line("Результаты будут сгруппированы по окончаниям");
        }

        $this->newLine();
    }

    /**
     * Ищет основы по заданным критериям
     */
    private function findRoots()
    {
        $query = ChuvAlph::query();

        // Фильтр по частям речи
        $query->whereIn('Pos', $this->targetPos);

        // Фильтр по окончаниям
        $query->where(function ($q) {
            foreach ($this->targetEndings as $ending) {
                $q->orWhere('Word', 'LIKE', '%' . $ending);
            }
        });

        // Минимальная длина
        $minLength = (int)$this->option('min-length');
        if ($minLength > 1) {
            $query->whereRaw('CHAR_LENGTH(Word) >= ?', [$minLength]);
        }

        // Исключение инфинитивов
        if ($this->option('exclude-infinitiv')) {
            $query->where(function ($q) {
                $q->where('Word', 'NOT LIKE', '%ма')
                    ->where('Word', 'NOT LIKE', '%ме');
            });
        }

        // Сортировка
        $query->orderBy('Pos')
            ->orderBy('Word');

        // Лимит
        $limit = (int)$this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Отображает результаты поиска
     */
    private function showResults($results): void
    {
        $total = $results->count();
        $this->info("Найдено основ: {$total}");
        $this->newLine();

        if ($this->option('group-by-ending')) {
            $this->showGroupedResults($results);
        } else {
            $this->showAllResults($results);
        }

        // Статистика
        $this->showStatistics($results);
    }

    /**
     * Показывает все результаты в одной таблице
     */
    private function showAllResults($results): void
    {
        $tableData = [];

        foreach ($results as $index => $word) {
            // $ending = mb_substr($word->Word, -1);

            $tableData[] = [
                '№' => $index + 1,
                'Основа' => $word->Word,

                'ЧР' => $this->getPosLabel($word->Pos),

            ];
        }

        $this->table(
            ['№', 'Основа', 'Часть речи'],
            $tableData
        );
    }

    /**
     * Показывает результаты, сгруппированные по окончаниям
     */
    private function showGroupedResults($results): void
    {
        // Группируем по окончаниям
        $grouped = [];
        foreach ($results as $word) {
            $ending = mb_substr($word->Word, -1);
            if (!isset($grouped[$ending])) {
                $grouped[$ending] = [];
            }
            $grouped[$ending][] = $word;
        }

        // Сортируем группы по количеству слов
        uksort($grouped, function ($a, $b) use ($grouped) {
            return count($grouped[$b]) <=> count($grouped[$a]);
        });

        foreach ($grouped as $ending => $words) {
            $count = count($words);
            $this->info("=== Окончание '{$ending}' ({$count} слов) ===");

            $tableData = [];
            foreach ($words as $index => $word) {
                $tableData[] = [
                    '№' => $index + 1,
                    'Основа' => $word->Word,
                    'ЧР' => $this->getPosLabel($word->Pos),
                    'Инфо' => $word->Info ?? '-',
                ];
            }

            $this->table(['№', 'Основа', 'Часть речи', 'Информация'], $tableData);
            $this->newLine();
        }
    }

    /**
     * Показывает статистику
     */
    private function showStatistics($results): void
    {
        $this->info('=== СТАТИСТИКА ===');

        // Общая статистика
        $totalWords = ChuvAlph::count();
        $foundWords = $results->count();

        $this->line("Всего слов в словаре: {$totalWords}");
        $this->line("Найдено основ: {$foundWords}");
        $this->line("Процент от словаря: " . number_format(($foundWords / $totalWords * 100), 2) . '%');
        $this->newLine();

        // Статистика по частям речи
        $posStats = $results->groupBy('Pos')->map->count();

        $this->info('Распределение по частям речи:');
        $posTable = [];
        foreach ($posStats as $pos => $count) {
            $posTable[] = [
                'Часть речи' => $this->getPosLabel($pos),
                'Количество' => $count,
                'Процент' => number_format(($count / $foundWords * 100), 2) . '%'
            ];
        }

        $this->table(['Часть речи', 'Количество', 'Доля'], $posTable);
        $this->newLine();

        // Статистика по окончаниям
        $endingStats = [];
        foreach ($results as $word) {
            $ending = mb_substr($word->Word, -1);
            if (!isset($endingStats[$ending])) {
                $endingStats[$ending] = 0;
            }
            $endingStats[$ending]++;
        }

        arsort($endingStats);

        $this->info('Распределение по окончаниям:');
        $endingTable = [];
        foreach ($endingStats as $ending => $count) {
            $endingTable[] = [
                'Окончание' => $ending,
                'Количество' => $count,
                'Процент' => number_format(($count / $foundWords * 100), 2) . '%'
            ];
        }

        $this->table(['Окончание', 'Количество', 'Доля'], $endingTable);
        $this->newLine();

        // Самые длинные основы
        $longest = $results->sortByDesc(function ($word) {
            return mb_strlen($word->Word);
        })->take(10);

        $this->info('Топ-10 самых длинных основ:');
        $longestTable = [];
        foreach ($longest as $word) {
            $longestTable[] = [
                'Основа' => $word->Word,
                'Длина' => mb_strlen($word->Word),
                'ЧР' => $this->getPosLabel($word->Pos),
                'Оконч.' => mb_substr($word->Word, -1)
            ];
        }

        $this->table(['Основа', 'Длина', 'Часть речи', 'Окончание'], $longestTable);
    }

    /**
     * Возвращает читаемое название части речи
     */
    private function getPosLabel(string $posCode): string
    {
        $labels = [
            'noun' => 'сущ.',
            'verb' => 'глаг.',
            'adj' => 'прил.',
            'adv' => 'нар.',
            'num' => 'числ.',
            'pron' => 'мест.',
            'part' => 'прич.',
            'pronoun' => 'мест.', // alias для pronoun
            'conj' => 'союз',
            'prep' => 'предл.',
            'interj' => 'межд.',
            'unknown' => 'неизв.',
        ];

        return $labels[$posCode] ?? $posCode;
    }

    /**
     * Экспортирует результаты в CSV
     */
    private function exportResults($results): void
    {
        $endingsStr = implode('_', $this->targetEndings);
        $posStr = implode('_', $this->targetPos);
        $filename = "roots_{$posStr}_{$endingsStr}_" . date('Y-m-d') . '.csv';
        $path = storage_path('app/exports/' . $filename);

        // Создаем директорию, если не существует
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // UTF-8 BOM для корректного отображения кириллицы в Excel
        fwrite($file, "\xEF\xBB\xBF");

        // Заголовки CSV
        fputcsv($file, [
            'Основа',
            'Окончание',
            'Часть речи',
            'Код части речи',
            'Информация',
            'Длина',
            'ID записи'
        ], ';');

        // Данные
        foreach ($results as $word) {
            fputcsv($file, [
                $word->Word,
                mb_substr($word->Word, -1),
                $this->getPosLabel($word->Pos),
                $word->Pos,
                $word->Info ?? '',
                mb_strlen($word->Word),
                $word->id
            ], ';');
        }
        fclose($file);
        $this->newLine();
        $this->info("Результаты экспортированы в: {$path}");
        $this->line("Всего записей: " . $results->count());
    }
}

// php artisan chuvash:find-roots