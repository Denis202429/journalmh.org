<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteRangeFromMainTableCopy extends Command
{
    protected $signature = 'delete:rangedata {start_id} {end_id}';
    protected $description = 'Delete records from main_table_copy in a specific ID range';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $startId = (int) $this->argument('start_id');
            $endId = (int) $this->argument('end_id');

            $this->info("Starting deletion of records with ID from $startId to $endId...");

            $table = 'main_table_copy';

            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->error("Table $table does not exist.");
                return;
            }

            // Подсчет записей перед удалением
            $total = DB::table($table)
                ->whereBetween('id', [$startId, $endId])
                ->count();

            if ($total === 0) {
                $this->error("No records found in the given range ($startId - $endId).");
                return;
            }

            $this->info("Total records to delete: $total");

            // Инициализация прогресс-бара
            $this->output->progressStart($total);

            // Удаление данных порциями по 500 записей
            while ($total > 0) {
                $deleted = DB::table($table)
                    ->whereBetween('id', [$startId, $endId])
                    ->limit(500)
                    ->delete();

                $total -= $deleted;
                $this->output->progressAdvance($deleted);
            }

            $this->output->progressFinish();
            $this->info("Records from $startId to $endId successfully deleted.");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during deletion: " . $e->getMessage());
        }
    }
}
//php artisan delete:rangedata 100 500

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
