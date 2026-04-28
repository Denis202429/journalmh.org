<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\DB;

class NormalizeChuvashUrls extends Command
{
    protected $signature = 'scraped:normalize-chuvash-urls';
    
    protected $description = 'Normalize Chuvash.org URLs to consistent format';

    public function handle()
    {
        $this->info('Normalizing Chuvash.org URLs...');
        
        // Исправляем http://chuvash.org на https://chuvash.org
        $updated1 = ScrapedData::where('url', 'LIKE', 'http://chuvash.org%')
            ->update([
                'url' => DB::raw("REPLACE(url, 'http://chuvash.org', 'https://chuvash.org')")
            ]);
            
        $this->info("Updated {$updated1} records from http to https");
        
        // Исправляем http://www.chuvash.org на https://chuvash.org  
        $updated2 = ScrapedData::where('url', 'LIKE', 'http://www.chuvash.org%')
            ->update([
                'url' => DB::raw("REPLACE(url, 'http://www.chuvash.org', 'https://chuvash.org')")
            ]);
            
        $this->info("Updated {$updated2} records from http://www to https");
        
        // Исправляем https://www.chuvash.org на https://chuvash.org
        $updated3 = ScrapedData::where('url', 'LIKE', 'https://www.chuvash.org%')
            ->update([
                'url' => DB::raw("REPLACE(url, 'https://www.chuvash.org', 'https://chuvash.org')")
            ]);
            
        $this->info("Updated {$updated3} records from https://www to https");
        
        // Удаляем дубликаты
        $this->removeDuplicates();
        
        $this->info('URL normalization completed!');
    }
    
    private function removeDuplicates()
    {
        $this->info('Removing duplicate records...');
        
        $duplicates = DB::select('
            SELECT url, COUNT(*) as count, GROUP_CONCAT(id) as ids 
            FROM scraped_data 
            GROUP BY url 
            HAVING COUNT(*) > 1
        ');
        
        $this->info("Found " . count($duplicates) . " duplicate URLs");
        
        $totalDeleted = 0;
        
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            // Оставляем первый ID, удаляем остальные
            $idsToDelete = array_slice($ids, 1);
            
            $deleted = ScrapedData::whereIn('id', $idsToDelete)->delete();
            $totalDeleted += $deleted;
            
            $this->line("Deleted {$deleted} duplicates for: {$duplicate->url}");
        }
        
        $this->info("Total deleted: {$totalDeleted} duplicate records");
    }
}
// php artisan scraped:normalize-chuvash-urls