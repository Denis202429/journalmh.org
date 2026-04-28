<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MainTable;

class MigrateMainTableData extends Command
{
    protected $signature = 'migrate:maintabledata';
    protected $description = 'Migrate data from MainTable to main_table_copy';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->info('Starting data migration...');

            // Имя целевой таблицы
            $newTableName = 'main_table_copy1';

            // Проверка, существует ли таблица
            if (!DB::getSchemaBuilder()->hasTable($newTableName)) {
                $this->error('Table ' . $newTableName . ' does not exist.');
                return;
            }

            $this->info('Table ' . $newTableName . ' exists.');

            // Получаем все данные из MainTable
            $mainTableData = MainTable::all(['Autor', 'title_article', 'year_creation', 'content', 'year_publication', 'place_publication', 'genre', 'category', 'url', 'tags', 'page'])->toArray();

            $this->info('Data retrieved from MainTable.');
            $this->info('Total records: ' . count($mainTableData));

            // Инициализация прогресс-бара
            $total = count($mainTableData);
            $this->output->progressStart($total);

            // Пакетная вставка данных
            foreach (array_chunk($mainTableData, 1000) as $chunk) {
                // Обрезка данных перед вставкой и замена недопустимых значений
                foreach ($chunk as &$record) {
                 //   $record['content'] = mb_substr($record['content'], 0, 65535);

                    if (is_null($record['place_publication'])) {
                        $record['place_publication'] = '';
                    }

                    if (!is_numeric($record['year_creation']) || $record['year_creation'] === '') {
                        $record['year_creation'] = null;
                    }
                }

                try {
                    $this->info('Inserting chunk of size: ' . count($chunk));
                    DB::table($newTableName)->insert($chunk);
                    $this->info('Chunk inserted successfully.');
                } catch (\Exception $e) {
                    $this->error('Failed to insert chunk: ' . $e->getMessage());
                }
                $this->output->progressAdvance(count($chunk));
            }

            $this->output->progressFinish();
            $this->info('Data migrated to ' . $newTableName . ' successfully.');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to migrate data: ' . $e->getMessage());
        }
    }
}

//php artisan migrate:maintabledata

// // мне надо сделать полностью новую копию таблицы main_table, которая имеет следующую структуру, в комментариях я указал какой тип должен быть у полей.
// //Мне надо создать миграцию которая создает таблицу main_table_copy.  
// class MainTable extends Model
// {
//     use HasFactory;
//     // use Searchable;

//     protected $table = 'main_table';
//     protected $fillable = [
//         'Autor',       //varchar(255) 
//         'title_article', //varchar(255)
//         'year_creation', //varchar(255) 
//         'content',        ///longtext
//         'year_publication', //varchar(255)
//         'place_publication', //varchar(255)
//         'genre',              // varchar(255)
//         'category',           // varchar(255)
//         'url',                // varchar(255)
//         'help_url',            // varchar(255)
//         'tags',                //varchar(255)
//         'page',                // int unsigned
//         'totalWords',         // int  
//         'totalSentences',     // int
//         'help1',             // varchar(255)
//     ];

//     // Значения по умолчанию
//     protected $attributes = [
//         'year_publication' => '', 
//         'place_publication' => '', 
//         'genre' => '', 
//         'category' => '', 
//     ];

// }

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
