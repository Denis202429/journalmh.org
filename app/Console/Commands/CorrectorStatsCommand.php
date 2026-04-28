<?php
// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Models\MainTable;
// use Illuminate\Support\Facades\DB;

// class CorrectorStatsCommand extends Command
// {
//     protected $signature = 'stats:correctors';
//     protected $description = 'Выводит статистику по корректорам (количество проверенных статей, символов, слов и процентное соотношение символов)';

//     public function handle()
//     {
//         // Вычисляем общее количество символов по всем проверенным статьям
//         // $totalSymbolsAll = MainTable::where('status', 1)->sum('totalSymbols');
//         $totalSymbolsAll = MainTable::sum('totalSymbols');
//         if ($totalSymbolsAll == 0) {
//             $this->info('Нет данных о проверенных статьях.');
//             return;
//         }

//         // Получаем статистику по корректорам
//         $stats = MainTable::where('status', 1)
//             ->whereNotNull('corrector')
//             ->groupBy('corrector')
//             ->select( 
//                 'corrector',
//                 DB::raw('COUNT(*) as total_articles'),
//                 DB::raw('SUM(totalSymbols) as total_symbols'),
//                 DB::raw('SUM(totalWords) as total_words')
//             )
//             ->orderByDesc('total_symbols')
//             ->get();


//         // Выводим таблицу с данными
//         $this->table(
//             ['Корректор', 'Количество статей', 'Общее количество символов', 'Общее количество слов', 'Доля символов, %'],
//             $stats->map(function ($stat) use ($totalSymbolsAll) {
//                 return [
//                     $stat->corrector,

//                     $stat->total_articles,
//                     number_format($stat->total_symbols, 0, ',', ' '),
//                     number_format($stat->total_words, 0, ',', ' '),
//                     round(($stat->total_symbols / $totalSymbolsAll) * 100, 2) . '%',
//                 ];
//             })->toArray()
//         );
//     }
// }

// php artisan stats:correctors

// namespace App\Console\Commands;
// use Illuminate\Console\Command;
// use App\Models\MainTable;
// use Illuminate\Support\Facades\DB;

// class CorrectorStatsCommand extends Command
// {
//     protected $signature = 'stats:correctors';
//     protected $description = 'Выводит статистику по организациям: для каждого человека количество добавленных и откорректированных текстов';

//     public function handle()
//     {
//         // Для диагностики - покажем общее количество статей в базе
//         $totalAllArticles = MainTable::count();
//         $totalStatus1 = MainTable::where('status', 1)->count();
//         $totalStatus0 = MainTable::where('status', 0)->count();
//         $totalOtherStatus = MainTable::whereNotIn('status', [0, 1])->count();
        
//         $this->info("========== ДИАГНОСТИКА ==========");
//         $this->info("Всего статей в базе: {$totalAllArticles}");
//         $this->info("Статей со статусом 1: {$totalStatus1}");
//         $this->info("Статей со статусом 0: {$totalStatus0}");
//         $this->info("Статей с другими статусами: {$totalOtherStatus}");
        
//         // Покажем распределение по статусам
//         $statusStats = MainTable::select('status', DB::raw('COUNT(*) as count'))
//             ->groupBy('status')
//             ->orderBy('status')
//             ->get();
        
//         foreach ($statusStats as $stat) {
//             $this->line("Статус {$stat->status}: {$stat->count} статей");
//         }
//         $this->newLine();

//         // Получаем все уникальные организации (без фильтра по статусу)
//         $organizations = MainTable::whereNotNull('organization')
//             ->where('organization', '!=', '')
//             ->distinct()
//             ->pluck('organization')
//             ->sort()
//             ->values()
//             ->toArray();

//         if (empty($organizations)) {
//             $this->info('Нет данных об организациях.');
//             return;
//         }

//         $this->info("Найдено организаций: " . count($organizations));
//         $this->newLine();

//         foreach ($organizations as $organization) {
//             // $this->info("========================================");
//             $this->info("Организация: {$organization}");
//             // $this->info("========================================");

//             // Статистика по организации (все статьи, без фильтра по статусу)
//             $orgTotal = MainTable::where('organization', $organization)->count();
//             $orgStatus1 = MainTable::where('organization', $organization)->where('status', 1)->count();
            
//             // Получаем статистику по добавленным текстам (added_by) - БЕЗ ФИЛЬТРА ПО СТАТУСУ
//             $addedStats = MainTable::where('organization', $organization)
//                 ->whereNotNull('added_by')
//                 ->where('added_by', '!=', '')
//                 ->groupBy('added_by')
//                 ->select(
//                     'added_by as person',
//                     DB::raw('COUNT(*) as added_articles'),
//                     DB::raw('SUM(totalSymbols) as added_symbols'),
//                     DB::raw('SUM(totalWords) as added_words')
//                 )
//                 ->orderByDesc('added_articles')
//                 ->get()
//                 ->keyBy('person');

//             // Получаем статистику по откорректированным текстам (corrector) - БЕЗ ФИЛЬТРА ПО СТАТУСУ
//             $correctedStats = MainTable::where('organization', $organization)
//                 ->whereNotNull('corrector')
//                 ->where('corrector', '!=', '')
//                 ->groupBy('corrector')
//                 ->select(
//                     'corrector as person',
//                     DB::raw('COUNT(*) as corrected_articles'),
//                     DB::raw('SUM(totalSymbols) as corrected_symbols'),
//                     DB::raw('SUM(totalWords) as corrected_words')
//                 )
//                 ->orderByDesc('corrected_articles')
//                 ->get()
//                 ->keyBy('person');

//             // Объединяем статистику по всем людям
//             $allPersons = $addedStats->keys()->merge($correctedStats->keys())->unique()->sort();

//             if ($allPersons->isEmpty()) {
//                 $this->warn("Нет данных о сотрудниках в этой организации");
//                 $this->newLine(2);
//                 continue;
//             }

//             $tableData = [];
//             foreach ($allPersons as $person) {
//                 $added = $addedStats->get($person);
//                 $corrected = $correctedStats->get($person);
                
//                 $tableData[] = [
//                     'person' => $person,
//                     'added_articles' => $added ? $added->added_articles : 0,
//                     'added_symbols' => $added ? number_format($added->added_symbols, 0, ',', ' ') : '0',
//                     'added_words' => $added ? number_format($added->added_words, 0, ',', ' ') : '0',
//                     'corrected_articles' => $corrected ? $corrected->corrected_articles : 0,
//                     'corrected_symbols' => $corrected ? number_format($corrected->corrected_symbols, 0, ',', ' ') : '0',
//                     'corrected_words' => $corrected ? number_format($corrected->corrected_words, 0, ',', ' ') : '0',
//                 ];
//             }

//             // Сортируем по общему количеству добавленных + откорректированных статей
//             usort($tableData, function($a, $b) {
//                 $totalA = $a['added_articles'] + $a['corrected_articles'];
//                 $totalB = $b['added_articles'] + $b['corrected_articles'];
//                 return $totalB <=> $totalA;
//             });

//             // Выводим таблицу для организации
//             $this->table(
//                 ['Сотрудник', 'Добавлено статей', 'Добавлено символов', 'Добавлено слов', 'Откорректировано статей', 'Откорректировано символов', 'Откорректировано слов'],
//                 $tableData
//             );

//             // Добавляем итоговую строку по организации
//             $totalAddedArticles = array_sum(array_column($tableData, 'added_articles'));
//             $totalAddedSymbols = array_sum(array_map(function($item) {
//                 return (int)str_replace(' ', '', $item['added_symbols']);
//             }, $tableData));
//             $totalAddedWords = array_sum(array_map(function($item) {
//                 return (int)str_replace(' ', '', $item['added_words']);
//             }, $tableData));
            
//             $totalCorrectedArticles = array_sum(array_column($tableData, 'corrected_articles'));
//             $totalCorrectedSymbols = array_sum(array_map(function($item) {
//                 return (int)str_replace(' ', '', $item['corrected_symbols']);
//             }, $tableData));
//             $totalCorrectedWords = array_sum(array_map(function($item) {
//                 return (int)str_replace(' ', '', $item['corrected_words']);
//             }, $tableData));
//         }

//         // Общая статистика по всем организациям (БЕЗ ФИЛЬТРА ПО СТАТУСУ)
//         $this->info("========================================");
//         $this->info("ОБЩАЯ СТАТИСТИКА ПО ВСЕМ ОРГАНИЗАЦИЯМ");
//         $this->info("========================================");

//         // Статистика по добавлениям
//         $totalAddedStats = MainTable::whereNotNull('added_by')
//             ->where('added_by', '!=', '')
//             ->select(
//                 DB::raw('COUNT(DISTINCT added_by) as total_adders'),
//                 DB::raw('COUNT(*) as total_added_articles'),
//                 DB::raw('SUM(totalSymbols) as total_added_symbols'),
//                 DB::raw('SUM(totalWords) as total_added_words')
//             )
//             ->first();

//         // Статистика по корректировкам
//         $totalCorrectedStats = MainTable::whereNotNull('corrector')
//             ->where('corrector', '!=', '')
//             ->select(
//                 DB::raw('COUNT(DISTINCT corrector) as total_correctors'),
//                 DB::raw('COUNT(*) as total_corrected_articles'),
//                 DB::raw('SUM(totalSymbols) as total_corrected_symbols'),
//                 DB::raw('SUM(totalWords) as total_corrected_words')
//             )
//             ->first();

//         // Общая статистика по всем статьям
//         $totalAllStats = MainTable::select(
//             DB::raw('COUNT(*) as total_articles'),
//             DB::raw('SUM(totalSymbols) as total_symbols'),
//             DB::raw('SUM(totalWords) as total_words'),
//             DB::raw('COUNT(DISTINCT organization) as total_organizations')
//         )->first();

//         $this->table(
//             ['Показатель', 'Значение'],
//             [
//                 ['Всего организаций (уникальных)', $totalAllStats->total_organizations],
//                 ['Всего статей в базе', $totalAllStats->total_articles],
//                 ['Статей с указанным added_by', $totalAddedStats->total_added_articles],
//                 ['Статей с указанным corrector', $totalCorrectedStats->total_corrected_articles],
//                 ['Уникальных добавлятелей', $totalAddedStats->total_adders],
//                 ['Уникальных корректоров', $totalCorrectedStats->total_correctors],
//                 ['Общее количество символов', number_format($totalAllStats->total_symbols, 0, ',', ' ')],
//                 ['Общее количество слов', number_format($totalAllStats->total_words, 0, ',', ' ')],
//             ]
//         );
        
//         // Дополнительная информация
//         $this->newLine();
//         $this->info("Дополнительная информация:");
        
//         // Статьи без added_by
//         $withoutAddedBy = MainTable::whereNull('added_by')->orWhere('added_by', '')->count();
//         $this->line("Статей без указания добавлятеля: {$withoutAddedBy}");
        
//         // Статьи без corrector
//         $withoutCorrector = MainTable::whereNull('corrector')->orWhere('corrector', '')->count();
//         $this->line("Статей без указания корректора: {$withoutCorrector}");
        
//         // Статьи без организации
//         $withoutOrg = MainTable::whereNull('organization')->orWhere('organization', '')->count();
//         $this->line("Статей без указания организации: {$withoutOrg}");
//     }
// }
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use Illuminate\Support\Facades\DB;

class CorrectorStatsCommand extends Command
{
    protected $signature = 'stats:correctors';
    protected $description = 'Выводит статистику по всем сотрудникам, отсортированную по добавленным символам';

    public function handle()
    {
        // Для диагностики - покажем общее количество статей в базе
        $totalAllArticles = MainTable::count();
        $totalStatus1 = MainTable::where('status', 1)->count();
        $totalStatus0 = MainTable::where('status', 0)->count();
        $totalOtherStatus = MainTable::whereNotIn('status', [0, 1])->count();
        
        $this->info("========== ДИАГНОСТИКА ==========");
        $this->info("Всего статей в базе: {$totalAllArticles}");
        $this->info("Статей со статусом 1: {$totalStatus1}");
        $this->info("Статей со статусом 0: {$totalStatus0}");
        $this->info("Статей с другими статусами: {$totalOtherStatus}");
        
        // Покажем распределение по статусам
        $statusStats = MainTable::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();
        
        foreach ($statusStats as $stat) {
            $this->line("Статус {$stat->status}: {$stat->count} статей");
        }
        $this->newLine();

        $this->info("========== СТАТИСТИКА ПО ВСЕМ СОТРУДНИКАМ ==========");
        $this->info("(Отсортировано по убыванию добавленных символов)");
        $this->newLine();

        // Получаем всех уникальных людей из added_by и corrector
        $allAddedPersons = MainTable::whereNotNull('added_by')
            ->where('added_by', '!=', '')
            ->distinct()
            ->pluck('added_by')
            ->toArray();
            
        $allCorrectedPersons = MainTable::whereNotNull('corrector')
            ->where('corrector', '!=', '')
            ->distinct()
            ->pluck('corrector')
            ->toArray();
            
        $allPersons = array_unique(array_merge($allAddedPersons, $allCorrectedPersons));
        sort($allPersons);

        if (empty($allPersons)) {
            $this->info('Нет данных о сотрудниках.');
            return;
        }

        $this->info("Найдено сотрудников: " . count($allPersons));
        $this->newLine();

        $tableData = [];
        
        foreach ($allPersons as $person) {
            // Статистика по добавленным текстам
            $addedStats = MainTable::where('added_by', $person)
                ->select(
                    DB::raw('COUNT(*) as added_articles'),
                    DB::raw('SUM(totalSymbols) as added_symbols'),
                    DB::raw('SUM(totalWords) as added_words')
                )
                ->first();

            // Статистика по откорректированным текстам
            $correctedStats = MainTable::where('corrector', $person)
                ->select(
                    DB::raw('COUNT(*) as corrected_articles'),
                    DB::raw('SUM(totalSymbols) as corrected_symbols'),
                    DB::raw('SUM(totalWords) as corrected_words')
                )
                ->first();

            // Статистика по организации (где человек работал)
            $organizations = MainTable::where(function($query) use ($person) {
                    $query->where('added_by', $person)
                          ->orWhere('corrector', $person);
                })
                ->whereNotNull('organization')
                ->where('organization', '!=', '')
                ->distinct()
                ->pluck('organization')
                ->implode(', ');

            $tableData[] = [
                'person' => $person,
                'added_articles' => $addedStats->added_articles ?? 0,
                'added_symbols_raw' => (int)($addedStats->added_symbols ?? 0),
                'added_symbols' => $addedStats->added_symbols ? number_format($addedStats->added_symbols, 0, ',', ' ') : '0',
                'added_words' => $addedStats->added_words ? number_format($addedStats->added_words, 0, ',', ' ') : '0',
                'corrected_articles' => $correctedStats->corrected_articles ?? 0,
                'corrected_symbols' => $correctedStats->corrected_symbols ? number_format($correctedStats->corrected_symbols, 0, ',', ' ') : '0',
                'corrected_words' => $correctedStats->corrected_words ? number_format($correctedStats->corrected_words, 0, ',', ' ') : '0',
                'organizations' => $organizations ?: 'Не указана',
            ];
        }

        // СОРТИРУЕМ ПО УБЫВАНИЮ ДОБАВЛЕННЫХ СИМВОЛОВ
        usort($tableData, function($a, $b) {
            return $b['added_symbols_raw'] <=> $a['added_symbols_raw'];
        });

        // Выводим общую таблицу
        $this->table(
            ['Сотрудник', 'Доб. статей', 'Доб. символов', 'Доб. слов', 
             'Отк. статей', 'Отк. символов', 'Отк. слов', 'Организации'],
            array_map(function($item) {
                return [
                    $item['person'],
                    $item['added_articles'],
                    $item['added_symbols'],
                    $item['added_words'],
                    $item['corrected_articles'],
                    $item['corrected_symbols'],
                    $item['corrected_words'],
                    $item['organizations'],
                ];
            }, $tableData)
        );

        // Общие итоги
        $this->newLine();
        $this->info("========== ОБЩИЕ ИТОГИ ==========");
        
        $totalAddedArticles = array_sum(array_column($tableData, 'added_articles'));
        $totalAddedSymbols = array_sum(array_column($tableData, 'added_symbols_raw'));
        $totalAddedWords = array_sum(array_map(function($item) {
            return (int)str_replace(' ', '', $item['added_words']);
        }, $tableData));
        
        $totalCorrectedArticles = array_sum(array_column($tableData, 'corrected_articles'));
        $totalCorrectedSymbols = array_sum(array_map(function($item) {
            return (int)str_replace(' ', '', $item['corrected_symbols']);
        }, $tableData));
        $totalCorrectedWords = array_sum(array_map(function($item) {
            return (int)str_replace(' ', '', $item['corrected_words']);
        }, $tableData));

        $this->table(
            ['Показатель', 'Значение'],
            [
                ['Всего сотрудников', count($tableData)],
                ['Всего добавлено статей', $totalAddedArticles],
                ['Всего добавлено символов', number_format($totalAddedSymbols, 0, ',', ' ')],
                ['Всего добавлено слов', number_format($totalAddedWords, 0, ',', ' ')],
                ['Всего откорректировано статей', $totalCorrectedArticles],
                ['Всего откорректировано символов', number_format($totalCorrectedSymbols, 0, ',', ' ')],
                ['Всего откорректировано слов', number_format($totalCorrectedWords, 0, ',', ' ')],
            ]
        );

        // Дополнительная информация
        $this->newLine();
        $this->info("========== ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ ==========");
        
        // Статьи без added_by
        $withoutAddedBy = MainTable::whereNull('added_by')->orWhere('added_by', '')->count();
        $this->line("Статей без указания добавлятеля: {$withoutAddedBy}");
        
        // Статьи без corrector
        $withoutCorrector = MainTable::whereNull('corrector')->orWhere('corrector', '')->count();
        $this->line("Статей без указания корректора: {$withoutCorrector}");
        
        // Статьи без организации
        $withoutOrg = MainTable::whereNull('organization')->orWhere('organization', '')->count();
        $this->line("Статей без указания организации: {$withoutOrg}");
        
        // Общая статистика по базе
        $totalAllStats = MainTable::select(
            DB::raw('COUNT(*) as total_articles'),
            DB::raw('SUM(totalSymbols) as total_symbols'),
            DB::raw('SUM(totalWords) as total_words'),
            DB::raw('COUNT(DISTINCT organization) as total_organizations')
        )->first();
        
        $this->newLine();
        $this->line("Всего статей в базе: {$totalAllStats->total_articles}");
        $this->line("Всего символов в базе: " . number_format($totalAllStats->total_symbols, 0, ',', ' '));
        $this->line("Всего слов в базе: " . number_format($totalAllStats->total_words, 0, ',', ' '));
        $this->line("Всего организаций: {$totalAllStats->total_organizations}");
    }
}

// php artisan stats:correctors
// По яндекс стандарту - ӐӑӖӗӲӳҪҫ   ӑӗӳҫ  ӐӖӲҪ

