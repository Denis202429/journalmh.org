<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MainTable;

class MigrateBackToMainTable extends Command
{
    protected $signature = 'migrate:backtomaintable';
    protected $description = 'Migrate data from main_table_copy back to MainTable';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->info('Starting data migration back to main_table...');

            // Имя исходной таблицы
            $sourceTableName = 'main_table_copy';
            // Имя целевой таблицы
            $targetTableName = 'main_table';

            // Проверка, существует ли таблица
            if (!DB::getSchemaBuilder()->hasTable($sourceTableName)) {
                $this->error('Source table ' . $sourceTableName . ' does not exist.');
                return;
            }

            if (!DB::getSchemaBuilder()->hasTable($targetTableName)) {
                $this->error('Target table ' . $targetTableName . ' does not exist.');
                return;
            }

            $this->info('Source table and Target table exist.');

            // Получаем все данные из main_table_copy
            $copyTableData = DB::table($sourceTableName)->get(['Autor', 'title_article', 'year_creation', 'content', 'year_publication', 'place_publication', 'genre', 'category', 'url', 'tags', 'page'])->toArray();

            $this->info('Data retrieved from ' . $sourceTableName . '.');
            $this->info('Total records: ' . count($copyTableData));

            // Инициализация прогресс-бара
            $total = count($copyTableData);
            $this->output->progressStart($total);

            // Пакетная вставка данных
            foreach (array_chunk($copyTableData, 1000) as $chunk) {
                // Преобразование объектов в массивы
                $chunkArray = array_map(function ($record) {
                    return (array) $record;
                }, $chunk);

                try {
                    $this->info('Inserting chunk of size: ' . count($chunk));
                    DB::table($targetTableName)->insert($chunkArray);
                    $this->info('Chunk inserted successfully.');
                } catch (\Exception $e) {
                    $this->error('Failed to insert chunk: ' . $e->getMessage());
                }
                $this->output->progressAdvance(count($chunk));
            }

            $this->output->progressFinish();
            $this->info('Data migrated back to ' . $targetTableName . ' successfully.');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to migrate data: ' . $e->getMessage());
        }
    }
}


//php artisan migrate:backtomaintable

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
