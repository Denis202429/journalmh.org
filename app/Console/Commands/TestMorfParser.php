<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MorfParsingLibrary\TestClass;

class TestMorfParser extends Command
{
    protected $signature = 'test:morfparser {inputText}';
    protected $description = 'Test function in TestClass';

    public function handle()
    {
        $inputText = $this->argument('inputText');

        // Создаем экземпляр TestClass
        $testClass = new TestClass();

        // Вызываем SearchInDictionary для поиска записей
        $results = $testClass->SearchInDictionary($inputText);

        // Проверка, есть ли результаты
        if (empty($results)) {
            $this->info('No matching records found.');
        } else {
            // Выводим каждую найденную запись
            foreach ($results as $record) {
                $this->info('Root: ' . $record['Root']);
                $this->info('IznachCHRechi: ' . $record['IznachCHRechi']);
                $this->info('AffUnit: ' . $record['AffUnit']);
                $this->info('KonChastRechi: ' . ($record['KonChastRechi'] ?? 'N/A'));

                $this->info('Affix: ' . (is_array($record['Affix']) ? print_r($record['Affix'], true) : ($record['Affix'] ?? 'N/A')));

                $this->info('Vremya: ' . ($record['Vremya'] ?? 'N/A'));
                $this->info('Padezh: ' . ($record['Padezh'] ?? 'N/A'));
                $this->info('PluralOrNot: ' . ($record['PluralOrNot'] ?? 'N/A'));
                $this->info('Face: ' . ($record['Face'] ?? 'N/A'));
                $this->info('Negativ: ' . ($record['Negativ'] ?? 'N/A'));
                $this->info('Infinitiv: ' . ($record['Infinitiv'] ?? 'N/A'));
                $this->info('---'); // Разделитель между записями
            }
        }

        return 0;
    }
}

//php artisan test:morfparser аббатлӑхпа



//php artisan test:morfparser  çимӗç  çил_çунатçӑ çимелӗх çӗӑ


