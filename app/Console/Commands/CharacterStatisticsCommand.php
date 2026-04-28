<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainTable;
use App\Models\MainTable1;

class CharacterStatisticsCommand extends Command
{
    protected $signature = 'analyze:characters';
    protected $description = 'Analyze character statistics in content fields of both main tables';

    public function handle()
    {
        $this->info('Starting character analysis...');

        // Инициализируем массив для хранения статистики
        $charStats = [];
        $totalCharacters = 0;

        // Анализируем MainTable
        $this->info('Processing MainTable...');
        $records = MainTable::cursor();
        foreach ($records as $record) {
            $this->processContent($record->content, $charStats, $totalCharacters);
        }

        // Анализируем MainTable1
        $this->info('Processing MainTable1...');
        $records = MainTable1::cursor();
        foreach ($records as $record) {
            $this->processContent($record->content, $charStats, $totalCharacters);
        }

        // Сортируем статистику по частоте
        arsort($charStats);

        // Выводим результаты
        $this->info("\nCharacter statistics:");
        $this->info("Total characters analyzed: " . number_format($totalCharacters));

        $results = [];
        foreach ($charStats as $char => $count) {
            $percentage = ($count / $totalCharacters) * 100;
            $results[] = [
                'Character' => $this->formatChar($char),
                'Count' => $count,
                'Percentage' => number_format($percentage, 4) . '%'
            ];
        }

        $this->table(['Character', 'Count', 'Percentage'], $results);

        $this->info('Analysis completed!');
    }

    protected function processContent($content, &$charStats, &$totalCharacters)
    {
        if (empty($content)) {
            return;
        }

        $content = mb_strtolower($content);
        $length = mb_strlen($content);

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($content, $i, 1);
            
            if (!isset($charStats[$char])) {
                $charStats[$char] = 0;
            }
            
            $charStats[$char]++;
            $totalCharacters++;
        }
    }

    protected function formatChar($char)
    {
        if ($char === " ") {
            return "[space]";
        } elseif ($char === "\n") {
            return "[newline]";
        } elseif ($char === "\t") {
            return "[tab]";
        } elseif (ord($char) < 32) {
            return "[control:" . ord($char) . "]";
        }
        return $char;
    }
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
