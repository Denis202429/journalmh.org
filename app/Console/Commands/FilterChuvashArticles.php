<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;

class FilterChuvashArticles extends Command
{
    protected $signature = 'filter:chuvash-articles';
    protected $description = 'Remove records without Chuvash characters';

    public function handle()
    {
        $this->info('Filtering Chuvash articles...');
        
        $total = ScrapedData::count();
        $this->info("Total records: {$total}");
        
        $toDelete = ScrapedData::where(function($query) {
            $query->where('content', 'NOT LIKE', '%ӑ%')
                  ->where('content', 'NOT LIKE', '%ӗ%')
                  ->where('content', 'NOT LIKE', '%ҫ%')
                  ->where('content', 'NOT LIKE', '%ӳ%');
        })->count();
        
        $this->info("Records to delete: {$toDelete}");
        
        if ($this->confirm("Delete {$toDelete} records?")) {
            ScrapedData::where(function($query) {
                $query->where('content', 'NOT LIKE', '%ӑ%')
                      ->where('content', 'NOT LIKE', '%ӗ%')
                      ->where('content', 'NOT LIKE', '%ҫ%')
                      ->where('content', 'NOT LIKE', '%ӳ%');
            })->delete();
            
            $this->info("Deleted {$toDelete} records");
        }
    }
}

// php artisan filter:chuvash-articles

// теперь мне надо написать консольную команду которая вычисляет сколько
//  слов содержат чувашские символы и если это количество слов меньше 10-ти процентов от общего количества слов в статье в поле content 
//  то надо удалить эту запись. Как это сделать? 
