<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;

class FillAddedBy extends Command
{
    protected $signature = 'fill:added_by';
    protected $description = 'Заполняет поле added_by значением "Леонтьев Д.М.", если оно пустое';

    public function handle()
    {
        $updatedCount = MainTable::whereNull('added_by')
            ->orWhere('added_by', '')
            ->update(['added_by' => 'Леонтьев Д.М.']);

        $this->info("Обновлено записей: $updatedCount");
    }
}

// php artisan fill:added_by

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
