<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\DB;

class CalculateTextMetrics extends Command
{
    protected $signature = 'calculate:text-metrics';
    protected $description = 'Подсчитывает количество символов, слов и предложений в поле content таблицы main_table.';

    public function handle()
    {
        $this->info("Начинаем обработку записей...");

        // Подсчет общего количества записей в таблице
        $totalRecords = MainTable::count();
        $this->info("Всего записей в main_table: $totalRecords");

        // Проверяем соединение с базой данных
        try {
            DB::connection()->getPdo();
            $this->info("Соединение с базой данных успешно!");
        } catch (\Exception $e) {
            $this->error("Ошибка подключения к базе данных: " . $e->getMessage());
            return;
        }

        // Выведем 5 записей, чтобы понять их состояние перед обработкой
        $exampleRecords = MainTable::select('id', 'content', 'totalSymbols', 'totalWords', 'totalSentences')->limit(5)->get();
        foreach ($exampleRecords as $record) {
            $this->info("🔍 ID: {$record->id}, Символов: {$record->totalSymbols}, Слов: {$record->totalWords}, Предложений: {$record->totalSentences}");
        }

        // Поиск записей с пустыми полями
        $records = MainTable::whereNull('totalSymbols')
            ->orWhereNull('totalWords')
            ->orWhereNull('totalSentences')
            ->get();

        $this->info("Найдено записей для обновления: " . $records->count());

        if ($records->isEmpty()) {
            $this->warn("⚠ Нет записей для обновления. Завершаем выполнение.");
            return;
        }

        foreach ($records as $record) {
            $this->info("Обрабатываем запись ID: {$record->id}");

            if (empty($record->content)) {
                $this->warn("⚠ Запись ID {$record->id} имеет пустое поле content. Пропускаем.");
                continue;
            }

            $this->info("📜 Content (первые 100 символов): " . mb_substr($record->content, 0, 100));

            // Подсчет символов, слов и предложений
            $totalSymbols = mb_strlen($record->content);
            $totalWords = str_word_count($record->content);
            $totalSentences = preg_match_all('/[.!?]+/', $record->content);

            $this->info("🖊 ID: {$record->id}, Символов: $totalSymbols, Слов: $totalWords, Предложений: $totalSentences");

            // Обновление записи
            try {
                $affectedRows = $record->update([
                    'totalSymbols' => $totalSymbols,
                    'totalWords' => $totalWords,
                    'totalSentences' => $totalSentences,
                ]);

                if ($affectedRows) {
                    $this->info("✅ Запись ID {$record->id} успешно обновлена!");
                } else {
                    $this->warn("❌ Запись ID {$record->id} не обновилась! Возможно, данные остались без изменений.");
                }
            } catch (\Exception $e) {
                $this->error("❌ Ошибка обновления записи ID {$record->id}: " . $e->getMessage());
            }
        }

        $this->info("🎉 Обновление завершено!");
    }
}
// По яндекс стандарту - ӐӑӖӗӲӳҪҫ

// php artisan calculate:text-metrics

