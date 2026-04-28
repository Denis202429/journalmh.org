<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrintUnicodeChars extends Command
{
    protected $signature = 'unicode:chars {word : Слово для анализа}';
    protected $description = 'Выводит Unicode-коды символов слова';

    public function handle()
    {
        $word = $this->argument('word');
        $this->info("Анализ слова: {$word}");
        $this->newLine();

        $headers = ['Символ', 'Unicode', 'Название', 'UTF-8', 'HTML'];
        $rows = [];

        for ($i = 0; $i < mb_strlen($word); $i++) {
            $char = mb_substr($word, $i, 1);
            $code = mb_ord($char);
            $rows[] = [
                $char,
                'U+' . strtoupper(dechex($code)),
                $this->getCharName($char),
                bin2hex(mb_substr($word, $i, 1, '8bit')),
                '&#' . $code . ';'
            ];
        }

        $this->table($headers, $rows);
    }

    protected function getCharName(string $char): string
    {
        $names = [
            'ӑ' => 'CYRILLIC SMALL LETTER A WITH BREVE',
            'ӗ' => 'CYRILLIC SMALL LETTER IE WITH BREVE',
            // Добавьте другие специфичные символы при необходимости
        ];

        return $names[$char] ?? 
               (preg_match('/[\p{Cyrillic}]/u', $char) 
                ? 'CYRILLIC SMALL LETTER ' . mb_strtoupper($char)
                : 'UNKNOWN CHARACTER');
    }
}

//php artisan unicode:chars "кӗнеке"

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
