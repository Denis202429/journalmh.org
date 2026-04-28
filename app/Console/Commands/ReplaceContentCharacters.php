<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;

class ReplaceContentCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:replace-characters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace specific characters in content, Autor, title_article, and year_publication fields of MainTable and show progress';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $replacements = [
            'Ç' => 'Ҫ',
            'ç' => 'ҫ',
            'Ă' => 'Ӑ',
            'ă' => 'ӑ',
            'Ĕ' => 'Ӗ',
            'ĕ' => 'ӗ',
        ];

        $totalRecords = MainTable::count();
        $this->info("Total records: $totalRecords");

        if ($totalRecords == 0) {
            $this->info("No records to process.");
            return 0;
        }

        $updatedRecords = 0;
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        MainTable::chunk(100, function($records) use ($replacements, &$updatedRecords, $progressBar) {
            foreach ($records as $record) {
                $originalContent = $record->content;
                $originalAutor = $record->Autor;
                $originalTitleArticle = $record->title_article;
                $originalYearPublication = $record->year_publication;

                $newContent = strtr($originalContent, $replacements);
                $newAutor = strtr($originalAutor, $replacements);
                $newTitleArticle = strtr($originalTitleArticle, $replacements);
                $newYearPublication = strtr($originalYearPublication, $replacements);

                if (
                    $originalContent !== $newContent ||
                    $originalAutor !== $newAutor ||
                    $originalTitleArticle !== $newTitleArticle ||
                    $originalYearPublication !== $newYearPublication
                ) {
                    $record->content = $newContent;
                    $record->Autor = $newAutor;
                    $record->title_article = $newTitleArticle;
                    $record->year_publication = $newYearPublication;
                    $record->save();
                    $updatedRecords++;
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->info("\nProcessed records: $totalRecords");
        $this->info("Updated records: $updatedRecords");

        return 0;
    }
}
// По яндекс стандарту - ӐӑӖӗӲӳҪҫ


//php artisan content:replace-characters , заменяем в базе данных символы

// - вот у меня команда которая заменяет символы в таблице в поле content,
//  мне надо сделать так чтобы она заменяла еще символы в полях - Autor , title_article, year_publication
// как это сделать? 



// мне надо написать команду которая анализирует частоту вхождения символов в тексте в таблице MainTable. 
// Надо провести анализ определенного автора и в определенных произведениях, а именно:
// Autor = Ҫеҫпӗл Мишши
// category = Прозаические тексты
// Надо предоставить данные по каждому произведению и общий итог по всем произведениям указанной категории и автора.
// Как это сделать? 
