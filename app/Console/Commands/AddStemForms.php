<?php

namespace App\Console\Commands;

use App\Models\ChuvAlph;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddStemForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chuvash:add-stem-forms 
                            {--vowels=ӗ,ӑ,у,ӳ,а,о,я : Гласные для обработки через запятую}
                            {--pos= : Обработать только указанные части речи (через запятую)}
                            {--start-id= : Начать с указанного ID}
                            {--end-id= : Закончить на указанном ID}
                            {--dry-run : Показать, что будет добавлено, но не выполнять}
                            {--batch-size=100 : Размер пачки для обработки}
                            {--skip-existing : Пропустить слова, для которых уже существует форма без гласной}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет формы слов без конечной гласной в таблицу chuv_alph';

    /**
     * Массив гласных для обработки
     */
    protected array $vowels = [];

    /**
     * Счетчики
     */
    protected array $counters = [
        'processed' => 0,
        'added' => 0,
        'skipped' => 0,
        'errors' => 0
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->parseParameters();
        $this->showParameters();

        $this->info('Начинаю обработку слов...');
        $this->newLine();

        // Получаем слова для обработки
        $query = $this->buildQuery();
        $totalWords = $query->count();

        if ($totalWords === 0) {
            $this->warn('Нет слов для обработки по указанным критериям.');
            return 1;
        }

        $this->info("Найдено слов для обработки: {$totalWords}");

        if ($this->option('dry-run')) {
            $this->processDryRun($query);
        } else {
            if (!$this->confirm("Добавить {$totalWords} новых записей? Это может занять время.", false)) {
                $this->warn('Операция отменена.');
                return 0;
            }

            $this->processWords($query, $totalWords);
        }

        $this->showSummary();
        return 0;
    }

    /**
     * Парсит параметры из командной строки
     */
    private function parseParameters(): void
    {
        // Парсим гласные
        $vowelsInput = $this->option('vowels');
        $this->vowels = array_map('trim', explode(',', $vowelsInput));

        // Валидация
        if (empty($this->vowels)) {
            $this->error('Не указаны гласные для обработки!');
            exit(1);
        }
    }

    /**
     * Показывает параметры обработки
     */
    private function showParameters(): void
    {
        $this->info('=== ПАРАМЕТРЫ ОБРАБОТКИ ===');
        $this->line("Гласные для удаления: " . implode(', ', $this->vowels));

        if ($this->option('pos')) {
            $this->line("Части речи: " . $this->option('pos'));
        }

        if ($this->option('start-id')) {
            $this->line("Начальный ID: " . $this->option('start-id'));
        }

        if ($this->option('end-id')) {
            $this->line("Конечный ID: " . $this->option('end-id'));
        }

        $this->line("Размер пачки: " . $this->option('batch-size'));

        if ($this->option('dry-run')) {
            $this->warn("РЕЖИМ ПРЕДПРОСМОТРА: изменения НЕ будут сохранены!");
        }

        if ($this->option('skip-existing')) {
            $this->line("Будут пропущены слова, для которых уже существует форма без гласной");
        }

        $this->newLine();
    }

    /**
     * Строит запрос для выборки слов
     */
    private function buildQuery()
    {
        $query = ChuvAlph::query();

        // Фильтр по гласным в конце слова
        $query->where(function ($q) {
            foreach ($this->vowels as $vowel) {
                $q->orWhere('Word', 'LIKE', '%' . $vowel);
            }
        });

        // Фильтр по части речи, если указан
        if ($this->option('pos')) {
            $posList = array_map('trim', explode(',', $this->option('pos')));
            $query->whereIn('Pos', $posList);
        }

        // Фильтр по ID
        if ($this->option('start-id')) {
            $query->where('id', '>=', (int)$this->option('start-id'));
        }

        if ($this->option('end-id')) {
            $query->where('id', '<=', (int)$this->option('end-id'));
        }

        // Сортировка по ID для последовательной обработки
        $query->orderBy('id');

        return $query;
    }

    /**
     * Обработка в режиме предпросмотра (dry-run)
     */
    private function processDryRun($query): void
    {
        $batchSize = min(20, (int)$this->option('batch-size'));
        $words = $query->limit($batchSize)->get();

        $this->info("Пример первых {$batchSize} слов для обработки:");

        $tableData = [];
        foreach ($words as $word) {
            $originalWord = $word->Word;
            $stemForm = $this->removeLastVowel($originalWord);

            if ($stemForm === $originalWord) {
                continue; // Не удалось удалить гласной
            }

            $tableData[] = [
                'ID' => $word->id,
                'Оригинал' => $originalWord,
                'Без гласной' => $stemForm,
                'ЧР' => $word->Pos,
                'Действие' => 'Добавить'
            ];

            $this->counters['processed']++;
        }

        if (!empty($tableData)) {
            $this->table(['ID', 'Оригинал', 'Без гласной', 'Часть речи', 'Действие'], $tableData);
        } else {
            $this->warn('Не удалось найти слова с указанными гласными в конце.');
        }

        $this->counters['added'] = count($tableData);
    }

    /**
     * Обработка слов и добавление новых записей
     */
    private function processWords($query, $totalWords): void
    {
        $batchSize = (int)$this->option('batch-size');
        $progressBar = $this->output->createProgressBar($totalWords);
        $progressBar->start();

        // Используем транзакцию для безопасности
        DB::beginTransaction();

        try {
            // Обрабатываем пачками
            $query->chunk($batchSize, function ($words) use ($progressBar) {
                foreach ($words as $word) {
                    $progressBar->advance();
                    $this->counters['processed']++;

                    $originalWord = $word->Word;
                    $stemForm = $this->removeLastVowel($originalWord);

                    // Проверяем, удалось ли удалить гласную
                    if ($stemForm === $originalWord) {
                        $this->counters['skipped']++;
                        continue;
                    }

                    // Проверяем, существует ли уже такая форма
                    if ($this->option('skip-existing')) {
                        $exists = ChuvAlph::where('Word', $stemForm)->exists();
                        if ($exists) {
                            $this->counters['skipped']++;
                            continue;
                        }
                    }

                    // Создаем новую запись через модель
                    try {
                        $newWord = new ChuvAlph();
                        $newWord->Word = $stemForm;          // Слово без гласной
                        $newWord->Pos = $word->Pos;          // Та же часть речи
                        $newWord->Perv_sl = $originalWord;   // Первоначальное слово
                        $newWord->save();

                        $this->counters['added']++;

                        // Показываем прогресс
                        if ($this->counters['added'] % 100 === 0) {
                            $this->line("Добавлено: {$this->counters['added']} записей...");
                        }
                    } catch (\Exception $e) {
                        $this->counters['errors']++;
                        $this->error("Ошибка ID {$word->id}: " . $e->getMessage());
                        $this->error("Слово: {$originalWord}, Форма: {$stemForm}");
                    }
                }
            });

            DB::commit();
            $this->info("Транзакция успешно завершена.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка транзакции: " . $e->getMessage());
            $this->error("Все изменения отменены.");
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Удаляет последнюю гласную из слова
     */
    private function removeLastVowel(string $word): string
    {
        $lastChar = mb_substr($word, -1);

        // Проверяем, является ли последний символ одной из указанных гласных
        if (in_array($lastChar, $this->vowels, true)) {
            return mb_substr($word, 0, -1);
        }

        return $word; // Возвращаем оригинал, если последний символ не гласная
    }

    /**
     * Показывает сводку результатов
     */
    private function showSummary(): void
    {
        $this->info('=== РЕЗУЛЬТАТЫ ОБРАБОТКИ ===');

        $tableData = [
            ['Обработано слов', $this->counters['processed']],
            ['Добавлено новых записей', $this->counters['added']],
            ['Пропущено', $this->counters['skipped']],
            ['Ошибок', $this->counters['errors']],
        ];

        $this->table(['Действие', 'Количество'], $tableData);

        if ($this->counters['added'] > 0 && !$this->option('dry-run')) {
            // Показываем примеры добавленных записей
            $this->newLine();
            $this->info('Примеры добавленных записей:');

            $examples = ChuvAlph::whereNotNull('Perv_sl')
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get(['Word', 'Perv_sl', 'Pos']);

            $exampleTable = [];
            foreach ($examples as $example) {
                $exampleTable[] = [
                    'Слово без гласной' => $example->Word,
                    'Первоначальное' => $example->Perv_sl,
                    'ЧР' => $example->Pos
                ];
            }

            $this->table(['Слово без гласной', 'Первоначальное', 'Часть речи'], $exampleTable);
        }

        if ($this->counters['errors'] > 0) {
            $this->newLine();
            $this->warn("Было {$this->counters['errors']} ошибок. Проверьте логи для деталей.");
        }
    }
}
// php artisan chuvash:add-stem-forms