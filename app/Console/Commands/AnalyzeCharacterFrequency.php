<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;

class AnalyzeCharacterFrequency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:analyze-frequency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze character frequency in texts of specific author and category';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $author = 'Ҫеҫпӗл Мишши';
        $category = 'Поэтические тексты';

        // Фильтрация записей по автору и категории
        $records = MainTable::where('Autor', $author)
                            ->where('category', $category)
                            ->get();

        if ($records->isEmpty()) {
            $this->info("No records found for the author '$author' in the category '$category'.");
            return 0;
        }

        $totalFrequency = [];
        $this->info("Analyzing records for the author '$author' in the category '$category'...");

        foreach ($records as $record) {
            $this->info("Analyzing: " . $record->title_article);

            $content = $record->content;
            $frequency = $this->analyzeFrequency($content);

            // Сортировка частоты символов по убыванию
            arsort($frequency);

            // Вывод данных по отдельному произведению
            $this->info("Character frequency for '{$record->title_article}':");
            foreach ($frequency as $char => $count) {
                $this->line("Character: '$char' - Count: $count");
            }

            // Суммируем частоту для итогового анализа
            foreach ($frequency as $char => $count) {
                if (isset($totalFrequency[$char])) {
                    $totalFrequency[$char] += $count;
                } else {
                    $totalFrequency[$char] = $count;
                }
            }
        }

        // Сортировка общей частоты символов по убыванию
        arsort($totalFrequency);

        // Вывод общего итога по всем произведениям
        $this->info("Total character frequency for all works:");
        foreach ($totalFrequency as $char => $count) {
            $this->line("Character: '$char' - Count: $count");
        }

        return 0;
    }

    /**
     * Analyze character frequency in a given text.
     *
     * @param string $text
     * @return array
     */
    private function analyzeFrequency($text)
    {
        $frequency = [];

        // Проходим по каждому символу текста
        $length = mb_strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1);
            if (isset($frequency[$char])) {
                $frequency[$char]++;
            } else {
                $frequency[$char] = 1;
            }
        }

        return $frequency;
    }
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
//php artisan content:analyze-frequency
