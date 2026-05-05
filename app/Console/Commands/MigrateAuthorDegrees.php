<?php

namespace App\Console\Commands;

use App\Models\ArticleAuthor;
use Illuminate\Console\Command;

class MigrateAuthorDegrees extends Command
{
    protected $signature = 'migrate:author-degrees';
    protected $description = 'Перенос данных из полей degree и rank в degree_ru и rank_ru';

    public function handle()
    {
        $this->info('🚀 Начинаем перенос данных авторов...');
        $this->newLine();

        // Получаем всех авторов, у которых есть degree или rank
        $authors = ArticleAuthor::whereNotNull('degree')
            ->orWhereNotNull('rank')
            ->get();

        $this->info("📊 Найдено авторов с данными: {$authors->count()}");
        $this->newLine();

        if ($authors->isEmpty()) {
            $this->warn('Нет авторов для обновления.');
            return 0;
        }

        $stats = ['degree' => 0, 'rank' => 0];
        $bar = $this->output->createProgressBar($authors->count());
        $bar->start();

        foreach ($authors as $author) {
            $updated = false;

            // Перенос degree в degree_ru
            if (!empty($author->degree) && empty($author->degree_ru)) {
                $author->degree_ru = $author->degree;
                $stats['degree']++;
                $updated = true;
            }

            // Перенос rank в rank_ru
            if (!empty($author->rank) && empty($author->rank_ru)) {
                $author->rank_ru = $author->rank;
                $stats['rank']++;
                $updated = true;
            }

            if ($updated) {
                $author->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Результаты
        $this->table(
            ['Параметр', 'Обновлено'],
            [
                ['degree → degree_ru', $stats['degree']],
                ['rank → rank_ru', $stats['rank']],
            ]
        );

        $this->newLine();
        $this->info('✅ Перенос данных успешно завершен!');

        return 0;
    }
}