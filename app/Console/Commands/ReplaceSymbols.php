<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ReplaceSymbols extends Command
{  
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replace:symbols';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace specific symbols in PHP and TXT files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Устанавливаем путь к папке, где находятся файлы.
        $directory = 'C:/OSPanel/domains/1';

        // Массив замен
        $replacements = [
            'Ç' => 'Ҫ',
            'ç' => 'ҫ',
            'Ă' => 'Ӑ',
            'ă' => 'ӑ',
            'Ĕ' => 'Ӗ',
            'ĕ' => 'ӗ',     
            'ÿ' => 'ӳ',
            'Ÿ' => 'Ӳ',
        ];
           //  ӗӖӐҪ   ҫӑӗ
        // Используем рекурсивный итератор для обхода всех файлов в папке
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($files as $file) {
            // Обрабатываем только файлы .php и .txt
            if ($file->isFile() && in_array($file->getExtension(), ['php', 'txt', 'cs'])) {
                // Читаем содержимое файла
                $content = file_get_contents($file->getRealPath());

                // Выполняем замены
                $updatedContent = strtr($content, $replacements);

                // Записываем изменения обратно в файл
                file_put_contents($file->getRealPath(), $updatedContent);

                // Сообщаем об изменении файла
                $this->info('Processed: ' . $file->getRealPath());
            }
        }

        // Завершаем команду
        $this->info('Symbol replacement completed.');
        return 0;
    }
}
// php artisan replace:symbols

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
