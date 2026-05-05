<?php

namespace App\Console\Commands;

use App\Models\ArticleAuthor;
use Illuminate\Console\Command;

class MigrateAuthorDegrees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:author-degrees
                            {--dry-run : Показать что будет обновлено без фактического обновления}
                            {--force : Принудительно обновить даже если поля уже заполнены}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Перенос данных из полей degree и rank в degree_ru, degree_en, degree_cv и rank_ru, rank_en, rank_cv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Начинаем перенос данных авторов...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('⚠️  Режим DRY-RUN: изменения не будут сохранены в базу данных');
            $this->newLine();
        }

        // Статистика
        $stats = [
            'total' => 0,
            'degree_updated' => 0,
            'rank_updated' => 0,
            'skipped_degree' => 0,
            'skipped_rank' => 0,
        ];

        // Получаем всех авторов, у которых есть degree или rank
        $query = ArticleAuthor::query();
        
        if (!$force) {
            // Обновляем только тех, у кого есть старые данные и нет новых
            $query->where(function ($q) {
                $q->whereNotNull('degree')
                  ->orWhereNotNull('rank');
            });
        }
        
        $authors = $query->get();
        $stats['total'] = $authors->count();

        if ($stats['total'] === 0) {
            $this->info('✅ Нет авторов для обновления.');
            return 0;
        }

        $this->info("📊 Найдено авторов: {$stats['total']}");
        $this->newLine();

        $bar = $this->output->createProgressBar($stats['total']);
        $bar->start();

        foreach ($authors as $author) {
            $updated = false;
            $degreeUpdated = false;
            $rankUpdated = false;

            // Перенос degree
            if (!empty($author->degree)) {
                $shouldUpdateDegree = $force || (
                    empty($author->degree_ru) && 
                    empty($author->degree_en) && 
                    empty($author->degree_cv)
                );

                if ($shouldUpdateDegree) {
                    if (!$dryRun) {
                        $author->degree_ru = $author->degree;
                        $author->degree_en = $author->degree;
                        $author->degree_cv = $author->degree;
                    }
                    $degreeUpdated = true;
                    $stats['degree_updated']++;
                    $updated = true;
                } else {
                    $stats['skipped_degree']++;
                }
            }

            // Перенос rank
            if (!empty($author->rank)) {
                $shouldUpdateRank = $force || (
                    empty($author->rank_ru) && 
                    empty($author->rank_en) && 
                    empty($author->rank_cv)
                );

                if ($shouldUpdateRank) {
                    if (!$dryRun) {
                        $author->rank_ru = $author->rank;
                        $author->rank_en = $author->rank;
                        $author->rank_cv = $author->rank;
                    }
                    $rankUpdated = true;
                    $stats['rank_updated']++;
                    $updated = true;
                } else {
                    $stats['skipped_rank']++;
                }
            }

            // Сохраняем изменения
            if ($updated && !$dryRun) {
                $author->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Выводим статистику
        $this->table(
            ['Параметр', 'Значение'],
            [
                ['Всего обработано авторов', $stats['total']],
                ['Обновлено degree', $stats['degree_updated']],
                ['Обновлено rank', $stats['rank_updated']],
                ['Пропущено degree (уже есть данные)', $stats['skipped_degree']],
                ['Пропущено rank (уже есть данные)', $stats['skipped_rank']],
            ]
        );

        $this->newLine();

        if ($dryRun) {
            $this->info('✅ Режим DRY-RUN завершен. Для применения изменений запустите без параметра --dry-run');
        } else {
            $this->info('✅ Перенос данных успешно завершен!');
            
            // Показываем пример обновленных данных
            $this->newLine();
            $this->info('📝 Пример обновленных данных (первые 5 записей):');
            
            $examples = ArticleAuthor::where(function ($q) {
                    $q->whereNotNull('degree_ru')->orWhereNotNull('rank_ru');
                })
                ->limit(5)
                ->get(['id', 'degree', 'degree_ru', 'rank', 'rank_ru']);
            
            if ($examples->count() > 0) {
                $this->table(
                    ['ID', 'Старая degree', 'Новая degree_ru', 'Старый rank', 'Новый rank_ru'],
                    $examples->map(fn($a) => [
                        $a->id,
                        $a->degree ?? '-',
                        $a->degree_ru ?? '-',
                        $a->rank ?? '-',
                        $a->rank_ru ?? '-',
                    ])->toArray()
                );
            }
        }

        return 0;
    }
}