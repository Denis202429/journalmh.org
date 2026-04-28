<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\DB;

class CountContentCharacters extends Command
{
    protected $signature = 'count:content-characters';
    protected $description = 'Counts the total number of characters in the content field of the ScrapedData table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Выполнение запроса для подсчета количества символов в поле content
        $totalCharacters = ScrapedData::sum(DB::raw('LENGTH(content)'));

        // Вывод результата в консоль
        $this->info("Total number of characters in content field: " . $totalCharacters);
    }
}


//php artisan count:content-characters

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ  ӐӖӲҪ
