<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CountWordsInMainTableCopy extends Command
{
    protected $signature = 'count:words-main-table-copy';
    protected $description = 'Counts the total number of words and sentences in the content field of main_table_copy';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting word count...');

        $totalWords = 0;
        $totalSentences = 0;

        // Получите все записи из таблицы main_table_copy
        $records = DB::table('main_table')->get();

        // Переберите каждую запись и подсчитайте количество слов и предложений
        foreach ($records as $record) {
            $content = $record->content;

            // Подсчитайте количество слов в содержимом
            $wordCount = str_word_count($content, 0, ' ,:;1234567890()[]');

            // Подсчитайте количество предложений
            $sentenceCount = preg_match_all('/[.!?]+/u', $content);

            $totalWords += $wordCount;
            $totalSentences += $sentenceCount;
        }

        // Отображение результатов в консоли
        $this->info("Total words: $totalWords");
        $this->info("Total sentences: $totalSentences");

        // Запишите значения в файл .env, если нужно
        // $this->updateEnv([
        //     'TOTAL_WORDS' => $totalWords,
        //     'TOTAL_SENTENCES' => $totalSentences,
        // ]);

        return 0;
    }

  
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ

//php artisan count:words-main-table-copy
