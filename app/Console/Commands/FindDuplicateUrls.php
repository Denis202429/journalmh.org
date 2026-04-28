<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\Storage;

class FindDuplicateUrls extends Command
{
    // Название и описание команды
    protected $signature = 'find:duplicate-urls';
    protected $description = 'Finds duplicate records in ScrapedData by URL and logs them to a file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Finding duplicate URLs in the ScrapedData table...');

        // Получение всех записей из таблицы ScrapedData, сгруппированных по полю url
        $duplicates = ScrapedData::select('url')
            ->groupBy('url')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        // Проверка, найдены ли дубликаты
        if ($duplicates->isEmpty()) {
            $this->info('No duplicate URLs found.');
            return;
        }

        // Открытие файла для записи результатов
        $filePath = storage_path('duplicates.txt');
        $fileHandle = fopen($filePath, 'w');

        // Запись заголовка в файл
        fwrite($fileHandle, "Duplicate URLs and their occurrences:\n\n");

        // Обработка каждого дубликата
        foreach ($duplicates as $duplicate) {
            $url = $duplicate->url;
            $records = ScrapedData::where('url', $url)->get();
            $count = $records->count();
            $ids = $records->pluck('id')->implode(', ');

            // Запись информации о дубликате в файл
            fwrite($fileHandle, "URL: $url\nOccurrences: $count\nIDs: $ids\n\n");
        }

        // Закрытие файла
        fclose($fileHandle);

        $this->info("Duplicate URLs have been logged to $filePath");
    }
}


//php artisan find:duplicate-urls

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
