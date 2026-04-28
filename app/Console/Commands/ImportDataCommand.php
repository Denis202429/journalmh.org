<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\Storage;
use App\Models\ScrapedData;

class ImportDataCommand extends Command
{
    protected $signature = 'import:data';
    protected $description = 'Import data from scraped_data.txt into the main_table';

    public function __construct()
    {
        parent::__construct();
    }

    // public function handle()
    // {
    //     $filePath = storage_path('scraped_data.txt');

    //     if (!file_exists($filePath)) {
    //         $this->error('File not found');
    //         return 1;
    //     }

    //     $fileContents = file_get_contents($filePath);
    //     $lines = preg_split('/\r\n|\r|\n/', $fileContents);

    //     $jsonObjects = [];
    //     $currentJson = '';

    //     foreach ($lines as $line) {
    //         $trimmedLine = trim($line);
    //         if (empty($trimmedLine)) {
    //             continue;
    //         }
    //         $currentJson .= $trimmedLine;
    //         if ($trimmedLine === '}') {
    //             $jsonObjects[] = $currentJson;
    //             $currentJson = '';
    //         }
    //     }

    //     $totalLines = count($jsonObjects);
    //     $lineNumber = 0;

    //     foreach ($jsonObjects as $jsonObject) {
    //         $lineNumber++;
    //         $data = json_decode($jsonObject, true);

    //         if (json_last_error() === JSON_ERROR_NONE) {
    //             try {
    //                 MainTable::create([
    //                     'Autor' => $data['author'] ?? '',
    //                     'title_article' => $data['title'] ?? '',
    //                     'year_creation' => '',
    //                     'content' => $data['content'] ?? '',
    //                     'year_publication' => $data['year'] ?? '',
    //                     'place_publication' => '',
    //                     'genre' => 'Не определен',
    //                     'category' => 'Публицистические тексты',
    //                     'url' => $data['url'] ?? '',
    //                     'tags' => $data['tags'] ?? '',
    //                     'page' => is_numeric($data['page']) ? $data['page'] : 0, // Установить значение по умолчанию для пустого или некорректного значения
    //                 ]);
    //             } catch (\Exception $e) {
    //                 $this->error('Error inserting record: ' . $e->getMessage());
    //             }
    //         } else {
    //             $this->error("JSON decode error: " . json_last_error_msg() . " on line $lineNumber: $jsonObject");
    //         }

    //         $progress = ($lineNumber / $totalLines) * 100;
    //         $this->output->write("\rProgress: " . round($progress) . "%");
    //     }

    //     $this->info("\nData successfully imported");
    //     return 0;
    // }

    public function handle()
    {
        $scrapedData = ScrapedData::all();

        foreach ($scrapedData as $data) {
            MainTable::create([
                'Autor' => $data->author,
                'title_article' => $data->title,
                'year_creation' => null,
                'content' => $data->content,
                'year_publication' => $data->year,
                'place_publication' => '',
                'genre' => $data->genre,
                'category' => $data->category,
                'url' => $data->url,
                'tags' => $data->tags,
                'page' => $data->page,
            ]);

            $this->info("Imported record ID: {$data->id}");
        }

        $this->info('Data successfully imported from ScrapedData to MainTable');
        return 0;
    }
}

// $ php artisan import:data  импортиуем данные с JSON файла в MainTable

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
