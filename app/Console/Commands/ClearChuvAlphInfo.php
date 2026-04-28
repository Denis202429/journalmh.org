<?php

namespace App\Console\Commands;

use App\Models\ChuvAlph;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearChuvAlphInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chuvash:clear-info 
                            {--dry-run : Показать, что будет очищено, но не выполнять}
                            {--pos= : Очистить только для указанных частей речи (через запятую)}
                            {--word= : Очистить только для конкретного слова}
                            {--ids= : Очистить только для указанных ID (через запятую)}
                            {--contains= : Очистить только записи, где Info содержит указанный текст}
                            {--empty-only : Очистить только пустые значения (NULL или пустая строка)}
                            {--non-empty-only : Очистить только непустые значения}
                            {--limit=0 : Ограничить количество обрабатываемых записей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает поле Info в таблице chuv_alph';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю очистку поля Info в таблице chuv_alph...');
        $this->newLine();
        
        // Получаем записи для обработки
        $query = $this->buildQuery();
        
        // Получаем статистику ДО очистки
        // $this->showStatistics('ДО очистки', $query);
        
        if ($this->option('dry-run')) {
            $this->showSampleRecords($query);
            $this->info('Режим dry-run: изменения НЕ были сохранены.');
            return 0;
        }
        
        if (!$this->confirm('Вы уверены, что хотите очистить поле Info у выбранных записей?', false)) {
            $this->warn('Очистка отменена.');
            return 0;
        }
        
        // Выполняем очистку
        $affected = $this->performCleanup($query);
        
        // Показываем статистику ПОСЛЕ очистки
        // $this->showStatistics('ПОСЛЕ очистки', ChuvAlph::query());
        
        $this->info("Готово! Очищено записей: {$affected}");
        
        return 0;
    }
    
    /**
     * Строит запрос для выборки записей
     */
    private function buildQuery()
    {
        $query = ChuvAlph::query();
        
        // Фильтр по части речи
        if ($this->option('pos')) {
            $posList = array_map('trim', explode(',', $this->option('pos')));
            $query->whereIn('Pos', $posList);
        }
        
        // Фильтр по конкретному слову
        if ($this->option('word')) {
            $query->where('Word', $this->option('word'));
        }
        
        // Фильтр по ID
        if ($this->option('ids')) {
            $ids = array_map('intval', explode(',', $this->option('ids')));
            $query->whereIn('id', $ids);
        }
        
        // Фильтр по содержимому Info
        if ($this->option('contains')) {
            $query->where('Info', 'LIKE', '%' . $this->option('contains') . '%');
        }
        
        // Фильтр по пустым значениям
        if ($this->option('empty-only')) {
            $query->where(function($q) {
                $q->whereNull('Info')
                  ->orWhere('Info', '=', '')
                  ->orWhere('Info', '=', ' ');
            });
        }
        
        // Фильтр по непустым значениям
        if ($this->option('non-empty-only')) {
            $query->whereNotNull('Info')
                  ->where('Info', '!=', '')
                  ->where('Info', '!=', ' ');
        }
        
        // Лимит
        $limit = (int)$this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }
        
        return $query;
    }
    
    /**
     * Показывает статистику
     */
    // private function showStatistics(string $stage, $query): void
    // {
    //     $this->info("=== Статистика {$stage} ===");
        
    //     // Общее количество записей
    //     $total = ChuvAlph::count();
    //     $this->line("Всего записей в таблице: {$total}");
        
    //     // Количество записей в выборке
    //     $selected = $query->count();
    //     $this->line("Записей в выборке: {$selected}");
        
    //     // Статистика по заполненности Info
    //     $stats = DB::table('chuv_alph')
    //         ->select(
    //             DB::raw('COUNT(*) as total'),
    //             DB::raw('SUM(CASE WHEN Info IS NULL OR Info = "" OR Info = " " THEN 1 ELSE 0 END) as empty'),
    //             DB::raw('SUM(CASE WHEN Info IS NOT NULL AND Info != "" AND Info != " " THEN 1 ELSE 0 END) as filled')
    //         )
    //         ->when($query->getQuery()->wheres, function($q) use ($query) {
    //             // Применяем те же условия, что и в основном запросе
    //             return $q->where($query->getQuery()->wheres);
    //         })
    //         ->first();
        
    //     if ($stats) {
    //         $emptyPercent = $stats->total > 0 ? round(($stats->empty / $stats->total) * 100, 2) : 0;
    //         $filledPercent = $stats->total > 0 ? round(($stats->filled / $stats->total) * 100, 2) : 0;
            
    //         $this->line("Пустых значений Info: {$stats->empty} ({$emptyPercent}%)");
    //         $this->line("Заполненных значений Info: {$stats->filled} ({$filledPercent}%)");
    //     }
        
    //     // Топ-5 самых длинных значений Info
    //     $longestInfo = ChuvAlph::select('Word', 'Info', 'Pos')
    //         ->whereNotNull('Info')
    //         ->where('Info', '!=', '')
    //         ->where('Info', '!=', ' ')
    //         ->when($query->getQuery()->wheres, function($q) use ($query) {
    //             return $q->where($query->getQuery()->wheres);
    //         })
    //         ->orderBy(DB::raw('CHAR_LENGTH(Info)'), 'DESC')
    //         ->limit(5)
    //         ->get();
        
    //     if ($longestInfo->isNotEmpty()) {
    //         $this->newLine();
    //         $this->info("Топ-5 самых длинных значений Info:");
            
    //         $tableData = [];
    //         foreach ($longestInfo as $record) {
    //             $tableData[] = [
    //                 'Слово' => $record->Word,
    //                 'ЧР' => $record->Pos,
    //                 'Длина Info' => mb_strlen($record->Info),
    //                 'Preview' => mb_substr($record->Info, 0, 50) . (mb_strlen($record->Info) > 50 ? '...' : '')
    //             ];
    //         }
            
    //         $this->table(['Слово', 'ЧР', 'Длина', 'Предпросмотр'], $tableData);
    //     }
        
    //     $this->newLine();
    // }
    
    /**
     * Показывает примеры записей (для dry-run)
     */
    private function showSampleRecords($query): void
    {
        $sample = $query->limit(10)->get();
        
        if ($sample->isEmpty()) {
            $this->warn('Нет записей для очистки по указанным критериям.');
            return;
        }
        
        $this->info('Примеры записей, которые будут очищены (первые 10):');
        
        $tableData = [];
        foreach ($sample as $record) {
            $tableData[] = [
                'ID' => $record->id,
                'Слово' => $record->Word,
                'ЧР' => $record->Pos,
                'Info (сейчас)' => $record->Info ?? '(пусто)',
                'Info (после)' => '(будет NULL)'
            ];
        }
        
        $this->table(['ID', 'Слово', 'ЧР', 'Info (сейчас)', 'Info (после)'], $tableData);
    }
    
    /**
     * Выполняет очистку поля Info
     */
    private function performCleanup($query): int
    {
        $startTime = microtime(true);
        
        // Используем chunk для обработки больших объемов данных
        $affected = 0;
        $query->chunk(1000, function($records) use (&$affected) {
            foreach ($records as $record) {
                $record->Info = null;
                $record->save();
                $affected++;
            }
            
            // Прогресс
            $this->line("Обработано: {$affected} записей...");
        });
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        $this->newLine();
        $this->info("Время выполнения: {$executionTime} секунд");
        
        return $affected;
    }
    
    /**
     * Возвращает читаемое название части речи
     */
    private function getPosLabel(string $posCode): string
    {
        $labels = [
            'noun' => 'сущ.',
            'verb' => 'глаг.',
            'adj' => 'прил.',
            'adv' => 'нар.',
            'num' => 'числ.',
            'pron' => 'мест.',
            'part' => 'прич.',
            'conj' => 'союз',
            'prep' => 'предл.',
            'interj' => 'межд.',
            'unknown' => 'неизв.',
        ];

        return $labels[$posCode] ?? $posCode;
    }
}
// php artisan chuvash:clear-info
// в заимствованиях у,о не выдадает 
// теперь мне надо написать консольную команду которая в таблице chuv_alph делает следующие изменения - 
// если слово оканчивается на ӗ,ӑ,у,ӳ,а,о,я то надо добавить после этого слова новую запись в которой в поле Word 
// надо записать слово без гласной на конце а в поле Perv_sl надо записать то самое первоначальное слово в котором мы убрали гласную в конце.
// Например слово 'абразивлӑ' и ID его поля равно 17, 
// надо доавить за этой записью новую запись в которой в поле Word будет слово без главной 
// то есть 'абразивл' а в поле Perv_sl будет слово 'абразивлӑ'.
