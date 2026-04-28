<?php

namespace App\Services\MorfParsingLibrary;

use App\Models\ChuvAlph;
use Illuminate\Support\Collection;
use App\Services\MorfParsingLibrary\Constants;



class TestClass
{
    // public $affixes;
    public $results = [];
    public function SearchInDictionary($input_slovo)
    {
        $queryResults = ChuvAlph::where('Slovo', $input_slovo)
            ->orWhereRaw('? LIKE CONCAT(Slovo, "%")', [$input_slovo])
            ->get();

        // Обработка каждого результата из запроса
        foreach ($queryResults as $result) {
            // Вычисление оставшейся части слова для AffUnit
            $affUnit = '';
            if (strlen($result->Slovo) < strlen($input_slovo)) {
                $affUnit = substr($input_slovo, strlen($result->Slovo));
            }

            // Инициализация записи и добавление ее в массив results
            $this->results[] = $this->initializeResult($result->Slovo, $result->CHRechi, $affUnit);
        }

        return $this->results;
    }


    private function calculateAffix($root, $affUnit)
    {
        $affixesFound = [];
        $remaining = $affUnit;
        $currentLevel = 1; // Начальный уровень для поиска аффиксов

        // Пока есть часть слова, которую нужно обработать
        while (!empty($remaining)) {
            $affixFound = false;

            // Перебираем все уровни от текущего уровня и выше
            foreach (Constants::$AFFIXES_ARRAY as $affixGroup) {
                if ($affixGroup['level'] < $currentLevel) {
                    continue; // Пропускаем уровни ниже текущего
                }

                // Перебираем аффиксы внутри группы
                foreach ($affixGroup['affixes'] as $affix) {
                    // Проверяем, начинается ли оставшаяся строка с текущего аффикса
                    if (substr($remaining, 0, strlen($affix)) === $affix) {
                        // Добавляем найденный аффикс в массив результатов
                        $affixesFound[] = [
                            'affix' => $affix,
                            'type' => $affixGroup['type'],
                        ];

                        // Обрезаем найденный аффикс из оставшейся части
                        $remaining = substr($remaining, strlen($affix));

                        // Обновляем текущий уровень на следующий для дальнейшего поиска
                        $currentLevel = $affixGroup['level'] + 1;

                        $affixFound = true; // Указываем, что нашли аффикс
                        break 2; // Прерываем оба цикла, чтобы начать новый поиск с обновленным уровнем
                    }
                }
            }

            // Если на текущем уровне аффикс не найден, прекращаем поиск
            if (!$affixFound) {
                break;
            }
        }

        return $affixesFound;
    }



    private function initializeResult($root, $iznachCHRechi, $affUnit)
    {
        return [
            'Root' => $root,
            'IznachCHRechi' => $iznachCHRechi,
            'AffUnit' => $affUnit,
            'KonChastRechi' => null,
            'Affix' => $this->calculateAffix($root, $affUnit), // Вызов новой функции
            'Vremya' => null,
            'Padezh' => null,
            'PluralOrNot' => null,
            'Face' => null,
            'Negativ' => null,
            'Infinitiv' => null,
        ];
    }

    public function displayResults()
    {
        foreach ($this->results as $result) {
            echo "Root: {$result['Root']}\n";
            echo "IznachCHRechi: {$result['IznachCHRechi']}\n";
            echo "AffUnit: {$result['AffUnit']}\n";
            echo "KonChastRechi: {$result['KonChastRechi']}\n";
            echo "Affix: {$result['Affix']}\n";
            echo "Vremya: {$result['Vremya']}\n";
            echo "Padezh: {$result['Padezh']}\n";
            echo "PluralOrNot: {$result['PluralOrNot']}\n";
            echo "Face: {$result['Face']}\n";
            echo "Negativ: {$result['Negativ']}\n";
            echo "Infinitiv: {$result['Infinitiv']}\n";
            echo "---\n";
        }
    }
}


// çӗӑ

// пӗрре, иккӗ, виççӗ, тӑваттӑ, пиллӗк, улттӑ, çиччӗ, саккӑр, тӑххӑр, вуннӑ (1-10) - единицы (здесь полная форма)
// вун пӗр, вун иккӗ, вун виççӗ, вун тӑваттӑ, вун пиллек, вун улттӑ, вун çиччӗ, вун саккӑр, вун тӑххӑр, (11-19) до двадцати (здесь полная форма)
// çирӗм, вӑтӑр, хӗрех, аллӑ, утмӑл, çитмӗл, сакӑр вуннӑ, тӑхӑр вуннӑ, çӗр (20 - 100 ) - десятки
// ик çӗр, виç çӗр, тӑват çӗр, пилӗк çӗр, улт çӗр, çич çӗр, сакӑр çӗр, тӑхӑр çӗр, пин  (100 - 1000)

//  289 456  - ик çӗр сакӑр вун тӑхӑр(с одним х, если длинное числительное то всегда краткая форма) пин те(добавляется после тысяч - пин те) тӑват çӗр аллӑ улттӑ 
//  967 654  - тӑхӑр çӗр утмӑл çичӗ(с одним ч) пин те улт çӗр аллӑ тӑххӑр
//  123 456 789 - çӗр çирӗм виçӗ миллион та тӑват çӗр аллӑ ултӑ пин те çич çӗр сакӑр вун тӑххӑр (здесь стоит в конце поэтому полная форма тӑххӑр)
//                                    миллон та, миллиард та, триллион та - добавляется если далее есть еще числительные
// пӗрре, иккӗ, виççӗ, тӑваттӑ, пиллӗк, улттӑ, çиччӗ, саккӑр, тӑххӑр, вуннӑ (1-10) - единицы (здесь полная форма)
//                                    а если просто 2 000 000 то будет - ик(сокращенная форма 'иккӗ' так как после нее есть еще слова) миллион.

// Даты                                    
// 13.11.2024 
// Сегодня такое то число -
// икӗ пин те çирем таваттӑмеш сулхи( добавляется 'сулхи' после года) чӳк уйахӗн (затем название месяца и слово 'уйахӗн') вун виççӗмӗшӗ  - сначала идет год , потом месяц и день, в конце добавляется -мӗшӗ
// Такого то числа произошло то то -  
// икӗ пин те çирем таваттӑмеш султа чӳк уйахӗн (затем название месяца и слово 'уйахӗн') вун виççӗмӗшӗшенче  - здесь изменены формы 'султа' и 'виççӗмӗшӗшенче' (добавляется уже -мӗшӗшенче)

// январь — кăрлач уйăхĕ 
// февраль — нарăс уйăхĕ 
// март — пуш уйăхĕ 
// апрель — ака уйăхĕ
// май — çу уйăхĕ 
// июнь — çĕртме уйăхĕ 
// июль — утă уйăхĕ 
// август — çурла уйăхĕ 
// сентябрь — авăн уйăхĕ 
// октябрь — юпа уйăхĕ 
// ноябрь — чӳк уйăхĕ 
// декабрь — раштав уйăхĕ 

// Порядковые числительные -
//  к ним прибавляется  -мӗш к последнему слову - пӗрремӗш(первый),вун виççӗмӗш(тридцатый). 

// Разделительная форма числительного - 
//  пӗрер, икшер, виçшер, тӑватшар, пиллӗкшер, ултшар, çичшер, сакшар, тӑххӑршар, вуншар (1-10) - единицы
// Собирательная форма - пӗрре, иксемер, виççӗн, тӑваттӑн, пиллӗк, улттӑ, çиччӗ, саккӑр, тӑххӑр, вуннӑ (1-10) вдвоем втроем 
//  эпир иксемер , виççсемер , тӑваттсамар, пиллӗксемер, ултсамар , çичсемер, 

 
// çӗӑ