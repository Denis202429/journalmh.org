<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateRangeToMainTable extends Command
{
    protected $signature = 'migrate:rangedata {start_id} {end_id}';
    protected $description = 'Migrate specific records from main_table_copy to main_table, appending new records.';

    public function __construct()
    {
        parent::__construct();
    }

    // public function handle()
    // {
    //     DB::beginTransaction();
    //     try {
    //         $startId = (int) $this->argument('start_id');
    //         $endId = (int) $this->argument('end_id');

    //         $this->info("Starting data migration for records with ID from $startId to $endId...");

    //         $sourceTable = 'main_table_copy';
    //         $targetTable = 'main_table';

    //         if (!DB::getSchemaBuilder()->hasTable($sourceTable)) {
    //             $this->error("Source table $sourceTable does not exist.");
    //             return;
    //         }

    //         if (!DB::getSchemaBuilder()->hasTable($targetTable)) {
    //             $this->error("Target table $targetTable does not exist.");
    //             return;
    //         }

    //         // Выбираем данные без ID (чтобы избежать конфликтов с автоинкрементом)
    //         $copyTableData = DB::table($sourceTable)
    //             ->whereBetween('id', [$startId, $endId])
    //             ->get([
    //                 'Autor', 'title_article', 'year_creation', 'content', 
    //                 'year_publication', 'place_publication', 'genre', 
    //                 'category', 'url', 'tags', 'page'
    //             ])->toArray();

    //         $total = count($copyTableData);
    //         $this->info("Total records to migrate: $total");

    //         if ($total === 0) {
    //             $this->error("No records found in the given range ($startId - $endId).");
    //             return;
    //         }

    //         // Инициализация прогресс-бара
    //         $this->output->progressStart($total);

    //         foreach (array_chunk($copyTableData, 500) as $chunk) {
    //             $chunkArray = array_map(function ($record) {
    //                 return (array) $record;
    //             }, $chunk);

    //             try {
    //                 DB::table($targetTable)->insert($chunkArray);
    //             } catch (\Exception $e) {
    //                 $this->error('Failed to insert chunk: ' . $e->getMessage());
    //             }
    //             $this->output->progressAdvance(count($chunk));
    //         }
    //         $this->output->progressFinish();
    //         $this->info("Data successfully migrated from $startId to $endId.");
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $this->error("Error during migration: " . $e->getMessage());
    //     }
    // }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $startId = (int) $this->argument('start_id');
            $endId = (int) $this->argument('end_id');

            $this->info("Starting data migration for records with ID from $startId to $endId...");

            $sourceTable = 'main_table_copy';
            $targetTable = 'main_table';

            if (!DB::getSchemaBuilder()->hasTable($sourceTable)) {
                $this->error("Source table $sourceTable does not exist.");
                return;
            }

            if (!DB::getSchemaBuilder()->hasTable($targetTable)) {
                $this->error("Target table $targetTable does not exist.");
                return;
            }

            // СЧИТАЕМ общее количество
            $total = DB::table($sourceTable)
                ->whereBetween('id', [$startId, $endId])
                ->count();

            $this->info("Total records to migrate: $total");

            if ($total === 0) {
                $this->error("No records found in the given range ($startId - $endId).");
                return;
            }

            $this->output->progressStart($total);

            // УМЕНЬШАЕМ размер чанка для экономии памяти
            $chunkSize = 100; // Было 500, уменьшаем до 100

            // Используем cursor() для экономии памяти вместо get()
            $processed = 0;
            $page = 1;

            do {
                $records = DB::table($sourceTable)
                    ->whereBetween('id', [$startId, $endId])
                    ->select([
                        'Autor',
                        'title_article',
                        'year_creation',
                        'content',
                        'year_publication',
                        'place_publication',
                        'genre',
                        'category',
                        'url',
                        'tags',
                        'page'
                    ])
                    ->orderBy('id')
                    ->offset(($page - 1) * $chunkSize)
                    ->limit($chunkSize)
                    ->get();

                $count = $records->count();

                if ($count > 0) {
                    $insertData = [];
                    foreach ($records as $record) {
                        $insertData[] = [
                            'Autor' => $record->Autor,
                            'title_article' => $record->title_article,
                            'year_creation' => $record->year_creation,
                            'content' => $record->content,
                            'year_publication' => $record->year_publication,
                            'place_publication' => $record->place_publication,
                            'genre' => $record->genre,
                            'category' => $record->category,
                            'url' => $record->url,
                            'tags' => $record->tags,
                            'page' => $record->page,
                        ];

                        // Освобождаем память после обработки каждой записи
                        unset($record);
                    }

                    DB::table($targetTable)->insert($insertData);

                    // Освобождаем память
                    unset($insertData, $records);

                    // Принудительный сбор мусора
                    if ($page % 10 === 0) {
                        gc_collect_cycles();
                    }

                    $processed += $count;
                    $this->output->progressAdvance($count);
                }

                $page++;
            } while ($count === $chunkSize);

            $this->output->progressFinish();
            $this->info("Data successfully migrated from $startId to $endId. Processed: $processed records.");
            DB::commit();

            // Очищаем память в конце
            gc_collect_cycles();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during migration: " . $e->getMessage());
            throw $e;
        }
    }
    
}

// php artisan migrate:rangedata 1 1000

//php artisan migrate:rangedata 29564 38946
//после чего удаляем из таблицы main_table_copy записи с командой php artisan delete:rangedata 100 500

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
// php artisan migrate:rangedata 5001 29563
// $ php artisan migrate:rangedata 5001 5002