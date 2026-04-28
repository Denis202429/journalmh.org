<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\DB;

class UpdateEmptyOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:empty-organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет все записи в базе данных, где поле organization пустое, устанавливая значение "Чувашский государственный институт гуманитарных наук"';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organizationName = 'Чувашский государственный институт гуманитарных наук';
        
        $this->info('Начинаю обновление записей с пустым полем organization...');
        
        // Подсчитываем количество записей с пустым organization
        $count = MainTable::where(function($query) {
            $query->whereNull('organization')
                  ->orWhere('organization', '')
                  ->orWhere('organization', ' ');
        })->count();
        
        if ($count == 0) {
            $this->info('Записей с пустым полем organization не найдено.');
            return Command::SUCCESS;
        }
        
        $this->info("Найдено записей для обновления: {$count}");
        
        if (!$this->confirm('Продолжить обновление?')) {
            $this->info('Операция отменена.');
            return Command::SUCCESS;
        }
        
        // Обновляем записи
        DB::beginTransaction();
        try {
            $updated = MainTable::where(function($query) {
                $query->whereNull('organization')
                      ->orWhere('organization', '')
                      ->orWhere('organization', ' ');
            })->update(['organization' => $organizationName]);
            
            DB::commit();
            
            $this->info("Успешно обновлено записей: {$updated}");
            $this->info("Установлено значение: {$organizationName}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Ошибка при обновлении: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

// php artisan update:empty-organizations