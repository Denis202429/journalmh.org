<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveUrlToHelpUrl extends Command
{
    // Название команды, используемое в Artisan
    protected $signature = 'migrate:moveurl';

    // Описание команды
    protected $description = 'Move data from url to help_url if url starts with "https://chuvash.org/"';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Начало выполнения команды
        $this->info('Starting to move URLs...');

        // Начало транзакции
        DB::beginTransaction();

        try {
            // Получение записей, где url начинается с "https://chuvash.org/"
            $records = DB::table('main_table')
                ->where('url', 'like', 'https://chuvash.org/%')
                ->get();

            // Вывод количества найденных записей
            $this->info('Found ' . $records->count() . ' records to update.');

            // Обновление записей
            foreach ($records as $record) {
                DB::table('main_table')
                    ->where('id', $record->id)
                    ->update([
                        'help_url' => $record->url, // Перемещение URL в help_url
                        'url' => null               // Очистка поля url
                    ]);

                $this->info('Updated record ID: ' . $record->id);
            }

            // Подтверждение транзакции
            DB::commit();
            $this->info('URLs moved successfully.');

        } catch (\Exception $e) {
            // Откат транзакции в случае ошибки
            DB::rollBack();
            $this->error('Failed to move URLs: ' . $e->getMessage());
        }
    }
}


// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
