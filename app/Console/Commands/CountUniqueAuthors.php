<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;

class CountUniqueAuthors extends Command
{
    protected $signature = 'count:unique-authors';
    protected $description = 'Counts the number of unique authors in the ScrapedData table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Выполнение запроса для подсчета уникальных авторов
        $uniqueAuthorsCount = ScrapedData::distinct('author')->count('author');

        // Вывод результата в консоль
        $this->info("Total unique authors: " . $uniqueAuthorsCount);
    }
}


//php artisan count:unique-authors

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ

