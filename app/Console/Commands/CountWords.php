<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;

class CountWords extends Command
{
    // Название и описание команды
    protected $signature = 'count:words';
    protected $description = 'Counts the number of words in the content field of the ScrapedData table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Counting words in the content field...');

        // Извлечение всех записей из таблицы ScrapedData
        $allData = ScrapedData::all();
        $totalWordCount = 0;

        foreach ($allData as $data) {
            // Извлечение содержимого поля content
            $content = $data->content;

            // Удаление HTML-тегов и переводов строк
            $content = strip_tags($content);
           // $content = preg_replace('/\s+/', ' ', $content);

            // Разделение содержимого на слова по пробелам
            $words = explode(' ', $content);

            // Подсчет количества слов, исключая пустые элементы
            $wordCount = count(array_filter($words, fn($word) => !empty($word)));
            $totalWordCount += $wordCount;
        }

        // Вывод общего количества слов
        $this->info("Total word count in content field: $totalWordCount");
    }
}

//php artisan count:words

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
