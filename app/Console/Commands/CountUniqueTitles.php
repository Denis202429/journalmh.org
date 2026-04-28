<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;

class CountUniqueTitles extends Command
{
    protected $signature = 'count:unique-titles';
    protected $description = 'Counts the number of unique titles in the ScrapedData table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Выполнение запроса для подсчета уникальных значений в поле title
        $uniqueTitlesCount = ScrapedData::distinct('title')->count('title');

        // Вывод результата в консоль
        $this->info("Total number of unique titles: " . $uniqueTitlesCount);
    }
}
//php artisan count:unique-titles

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
