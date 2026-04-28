<?php

namespace App\Services;

class FeaturesDeterminer
{
    // Константы для частей речи
    const NOUN = 'noun';
    const VERB = 'verb';
    const ADJECTIVE = 'adj';
    const PRONOUN = 'pron';
    const NUMERIC = 'num';
    const PRICHASTIE = 'part';
    // const DEEPRICHASTIE = 'deeprichastie';

    // Константы для чисел
    const ED_CHISLO = 'единственное';
    const MN_CHISLO = 'множественное';

    // Константы для времени
    const PROSH_V = 'прошедшее';
    const NAST_V = 'настоящее';
    const BUD_V = 'будущее';

    //  наклонения 
    const IZ_NAKL = 'изъявительное наклонение';
    const POVEL_NAKL = 'повелительное наклонение';
    const USTUP_NAKL = 'уступительное наклонение';
    const SOSLAG_NAKL = 'сослагательное наклонение';

    // Константы для лица
    const FACE1 = '1 лицо';
    const FACE2 = '2 лицо';
    const FACE3 = '3 лицо';

    // Константы для падежей
    const OSN_P = 'основной';
    const ROD_P = 'притяжательный';
    const DAT_P = 'дательный';
    const MEST_P = 'местный';
    const ISCH_P = 'исходный';
    const TVOR_P = 'творительный';
    const LISH_P = 'лишительный';
    const PR_CEL_P = 'причинно-целевой';

    // Константы для других характеристик
    const POSITIVE = 'положительный';
    const NEGATIVE = 'отрицательный';
    const INF = 'инфинитив';
    const NOTINF = 'не инфинитив';
    const UNKNOWN = 'не определенно';
    const NULL = 'отсутствует';

    // Настройки для определения характеристик
    private array $first_susch = ['ӑм', 'ӑр', 'ӗм', 'ӗр', 'м']; //  'ӑр' и 'ӗр' добавляются только тогда когда есть 'ӑм' и 'ӗм',
    private array $second_susch = ['ÿ', 'у',  'ӑр', 'ӗр']; // 'ÿн', 'ун' - диалектная форма
    private array $third_susch = ['и', 'ӗ'];

    private array $rod = ['ӑн', 'ӗн', 'ин', 'ÿн', 'ун', 'н', 'сен'];
    private array $dat = ['на', 'не', 'а', 'е'];
    private array $mest = ['ра', 'ре', 'та', 'те', 'че', 'ти', 'ри'];
    private array $isch = ['тан', 'тен', 'рен', 'ран', 'чен'];
    private array $tvor = ['па', 'пе', 'пала', 'пеле', 'палан', 'пелен'];
    private array $lish = ['сӑр', 'сӗр'];
    private array $prich_celevoy = ['шӑн', 'шӗн'];
    private array $pluralnoun = ['сем', 'сен'];

    private array $first_verb = ['ӑп', 'ӗп', 'п',  'ӑм', 'ӗм', 'м']; // 'ап', 'еп' - диалектное(разговорный)
    private array $second_verb = ['ӑн', 'ӗн'];  // если сразу после показателя вреемени стоит 'ӑр', 'ӗр'
    private array $third_verb = ['ӗ', 'ччӗр', 'ччӑр', 'тӑр', 'тӗр'];

    private array $pl = ['ӑр', 'ӗр', 'аҫ', 'еҫ', 'ҫ', 'ччӗр'];

    private array $nast_verb = ['ат', 'ет', 'ать', 'аҫ', 'еҫ', 'ть', 'п', 'ап', 'еп', 'мас', 'мес', 'ан', 'ен'];
    private array $prosh_verb = ['р', 'ч', 'ӑм', 'ӗм', 'атт', 'етт', 'сатт', 'сетт', 'сачч', 'сечч', 'атч', 'етч', 'ччӗ', 'чӗ'];
    private array $bud_verb = ['ӗ'];
    private array $ambi_vremya_verb = ['ӑп', 'ӗп', 'ӑн', 'ӗн', 'ӑр', 'ӗр'];
    private array $unknown_vremya = ['ма', 'ме', 'иччен'];

    private array $negative = ['мас', 'мес', 'маҫ', 'меҫ', 'м', 'сӑр', 'сӗр'];
    private array $infinitiv = ['ма', 'ме', 'машкӑн', 'мешкӗн'];

    private array $first_chisl = ['сӑмӑр', 'сӗмӗр'];
    private array $second_chisl = ['сӑр', 'сӗр'];

    private array $prich_mas = ['ас', 'ес', 'асшӑн', 'есшӗн', 'нӑ', 'нӗ', 'ан', 'ен', 'малла', 'мелле'];
    private array $nast_prich = ['акан', 'екен', 'аканн', 'екенн', 'ан', 'ен'];

    private array $bud_prich = ['ас', 'ес', 'асс', 'есс', 'асшӑн', 'есшӗн'];
    private array $prosh_prich = ['нӑ', 'нӗ', 'м', 'н'];
    private array $prosh_forall = ['ччӗ', 'чӗ'];

    /**
     * Определение числа
     */
    public function determinePlural(array $affixes, string $partOfSpeech): string
    {
        // ИЗВЛЕКАЕМ строки аффиксов!
        $affArray = $this->extractAffixStrings($affixes);

        switch ($partOfSpeech) {
            case self::NOUN:
            case self::ADJECTIVE:
            case self::NUMERIC:
                return $this->determinePluralOfNoun($affArray);
            case self::PRICHASTIE:
                return $this->determinePluralOfPrichastie($affArray);
            case self::PRONOUN:
                return $this->determinePluralOfPronoun($affArray);
            case self::VERB:
                return $this->determinePluralOfVerb($affArray);
            default:
                return self::UNKNOWN;
        }
    }
    /**
     * Определение времени
     */
    public function determineTime(array $affixes, string $partOfSpeech): string
    {
        $affArray = $affixes;

        switch ($partOfSpeech) {
            case self::VERB:
                return $this->determineTimeOfVerb($affArray);
            case self::PRICHASTIE:
                return $this->determineTimeOfPrichastie($affArray);
            default:
                return $this->determineTimeOfOther($affArray);
        }
    }
    /**
     * Определение лица
     */
    public function determineFace(array $affixes, string $partOfSpeech, string $word = ''): string
    {
        $affArray = $affixes;

        switch ($partOfSpeech) {
            case self::NOUN: {
                    //    dump('determineFaceOfNoun');
                    // $this->info('determineFaceOfNoun');
                    return $this->determineFaceOfNoun($affArray, $word);
                }
            case self::PRICHASTIE:
                return $this->determineFaceOfPrichastie($affArray, $word);
            case self::VERB:
                return $this->determineFaceOfVerb($affArray);
            case self::NUMERIC:
                return $this->determineFaceOfNumeric($affArray);
            case self::PRONOUN:
                return $this->determineFaceOfPronoun($affArray);
            default:
                return self::UNKNOWN;
        }
    }
    /**
     * Определение отрицательности (для глаголов)
     */
    public function determineNegative(array $affixes, string $partOfSpeech): string
    {
        // негативная форма глагола 
        // private array $negative = ['мас', 'мес', 'маҫ', 'меҫ', 'м', 'сӑр', 'сӗр'];

        if ($partOfSpeech !== self::VERB) {
            return self::POSITIVE;
        }

        $affArray = $affixes;

        foreach ($affArray as $affix) {
            if (in_array($affix,  ['мас', 'мес', 'маҫ', 'меҫ', 'м', 'сӑр', 'сӗр'])) {
                return self::NEGATIVE;
            }
        }

        return self::POSITIVE;
    }
    /**
     * Определение инфинитива (для глаголов)
     */
    public function determineInfinitiv(array $affixes, string $partOfSpeech): string
    {
        //  инфинитив если оканчивается на  ['ма', 'ме', 'машкӑн', 'мешкӗн'];
        if ($partOfSpeech !== self::VERB) {
            return self::NOTINF;
        }

        $affArray = $affixes;

        if (empty($affArray)) {
            return self::NOTINF;
        }

        $lastAffix = end($affArray);
        if (in_array($lastAffix, ['ма', 'ме', 'машкӑн', 'мешкӗн'])) {
            return self::INF;
        }

        return self::NOTINF;
    }
    /**
     * Вспомогательный метод для извлечения строк аффиксов
     */
    private function extractAffixStrings(array $affixes): array
    {
        $result = [];
        foreach ($affixes as $affix) {
            if (isset($affix['affix']) && $affix['affix'] !== '-') {
                $result[] = $affix['affix'];
            }
        }
        return $result;
    }
    /**
     * Методы для определения конкретных характеристик
     */
    private function determinePluralOfNoun(array $affArray): string
    {

        // private array $prosh_forall = ['ччӗ', 'чӗ'];
        foreach ($affArray as $affix) {
            if (in_array($affix, ['сем', 'сен', 'се'])) {
                return self::MN_CHISLO;
            }
        }
        return self::ED_CHISLO;
    }


    private function determinePluralOfPronoun(array $affArray): string
    {

        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӑр', 'ӗр', 'р', 'сем', 'сен', 'се'])) {
                return self::MN_CHISLO;
            }
        }
        return self::ED_CHISLO;
        // ['ӑр', 'ӗр','р', 'сем', 'сен','се']


    }

    private function determinePluralOfVerb(array $affArray): string
    {
        // private array $unknown_vremya = ['ма', 'ме', 'иччен'];
        // private array $pl = ['ӑр', 'ӗр', 'аҫ', 'еҫ', 'ҫ', 'ччӗр'];
        // private array $pl = ['ӑр', 'ӗр', 'аҫ', 'еҫ', 'ҫ', 'ччӗр'];

        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӑр', 'ӗр', 'аҫ', 'еҫ', 'ҫ'])) {
                return self::MN_CHISLO;
            }
        }

        return self::ED_CHISLO;
    }

    private function determinePluralOfPrichastie(array $affArray): string
    {
        // Если есть специальные аффиксы причастия

        foreach ($affArray as $affix) {
            if (in_array($affix, ['сем', 'сен'])) {
                return self::MN_CHISLO;
            }
        }

        return self::UNKNOWN;
    }


    public function determineNakl(array $affixes, string $partOfSpeech): string
    {
        if ($partOfSpeech !== self::VERB) {
            return self::NULL;
        }

        $affArray = $this->extractAffixStrings($affixes);
        return $this->determineNaklOfVerb($affArray);
    }

    //  наклонения 
    //  изьявительное  [если есть времена то это изьявительнео наклонение]
    //  повелительное  ['']  // сам сем форма вежливости
    //  уступительное  ['ин','ӑн','ӗн']
    //  сослагательное  ['ӑтт','ӗтт','ӗчч'] 

    private function determineNaklOfVerb(array $affArray): string
    {

        // Проверка прошедшего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ин', 'ӑн', 'ӗн'])) {
                return self::USTUP_NAKL;
            }
        }

        // Проверка настоящего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӑтт', 'ӗтт', 'ӗчч'])) {
                return self::SOSLAG_NAKL;
            }
        }
        // private array $ambi_vremya_verb = ['ӑп', 'ӗп', 'ӑн', 'ӗн', 'ӑр', 'ӗр'];
        // Проверка неоднозначных случаев

        // const IZ_NAKL = 'изъявительное наклонение';
        // const POVEL_NAKL = 'повелительное наклонение';
        // const USTUP_NAKL = 'уступительное наклонение';
        // const SOSLAG_NAKL = 'сослагательное наклонение';
        return self::IZ_NAKL;
    }



    // кай,noun,c
    private function determineTimeOfVerb(array $affArray): string
    {
        // Сначала проверяем будущее время (оно должно иметь приоритет)
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӗ'])) {
                return self::BUD_V;
            }
        }

        // Затем настоящее время
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ат', 'ет', 'ать', 'аҫ', 'еҫ', 'ть', 'п', 'ап', 'еп', 'мас', 'мес', 'ан', 'ен'])) {
                return self::NAST_V;
            }
        }

        // // Проверка особого случая (должна быть БОЛЕЕ КОНКРЕТНОЙ)
        // if (in_array('м', $affArray) && (in_array('ан', $affArray) || in_array('ен', $affArray))) {
        //     return self::PROSH_V;
        // }

        // Проверка прошедшего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, ['р', 'ч', 'ӑм', 'ӗм', 'атт', 'етт', 'сатт', 'сетт', 'сачч', 'сечч', 'атч', 'етч', 'ччӗ', 'чӗ'])) {
                // Уточняем тип прошедшего времени
                if (in_array($affix, ['р', 'ч'])) {
                    return 'однократно прошедшее время';
                }
                if (in_array($affix, ['т', 'тч', 'атт', 'етт', 'атч'])) {
                    return 'многократное прошедшее время';
                }
                if (in_array($affix, ['сатт', 'сетт', 'сачч', 'сечч'])) {
                    return 'давно прошедшее время';
                }
                return self::PROSH_V;
            }
        }

        return self::UNKNOWN;
    }


    private function determineTimeOfPrichastie(array $affArray): string
    {
        // Проверка прошедшего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, $this->prosh_prich)) {
                return self::PROSH_V;
            }
        }

        // Проверка настоящего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, $this->nast_prich)) {
                return self::NAST_V;
            }
        }

        // Проверка будущего времени
        foreach ($affArray as $affix) {
            if (in_array($affix, $this->bud_prich)) {
                return self::BUD_V;
            }
        }

        return self::UNKNOWN;
    }

    private function determineTimeOfOther(array $affArray): string
    {
        foreach ($affArray as $affix) {
            if (in_array($affix, $this->prosh_forall)) {
                return self::PROSH_V;
            }
        }
        return self::UNKNOWN;
    }



    private function determineFaceOfNoun(array $affArray, string $word): string
    {
        // НЕ используем array_flip(), чтобы избежать ошибок
        // Вместо этого используем обычные проверки

        // Проверяем комбинации по вашим правилам:

        // Комбинация 1 лица: ӑм + ӑр или ӗм + ӗр
        if ((in_array('ӑм', $affArray) && in_array('ӑр', $affArray)) ||
            (in_array('ӗм', $affArray) && in_array('ӗр', $affArray))
        ) {
            return self::FACE1;
        }

        // Только ӑр или ӗр (без парного аффикса) - 2 лицо
        if ((in_array('ӑр', $affArray) && !in_array('ӑм', $affArray)) ||
            (in_array('ӗр', $affArray) && !in_array('ӗм', $affArray))
        ) {
            return self::FACE2;
        }

        // Одиночные аффиксы
        if (in_array('ӑм', $affArray) || in_array('ӗм', $affArray) || in_array('м', $affArray)) {
            return self::FACE1;
        }

        if (in_array('ÿ', $affArray) || in_array('у', $affArray)) {
            return self::FACE2;
        }

        if (in_array('и', $affArray) || in_array('ӗ', $affArray)) {
            return self::FACE3;
        }

        return self::UNKNOWN;
    }


    private function determineFaceOfVerb(array $affArray): string
    {
        // функцию разбирали с А.П.

        // Проверка аффиксов первого лица
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӑп', 'ӗп', 'п',  'ӑм', 'ӗм', 'м'])) {     // 'ап', 'еп' - разговорное аффиксы первого лица глаголов
                return self::FACE1;
            }
        }

        // Проверка аффиксов второго лица            
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӑн', 'ӗн',  'ӑс', 'ӗс'])) {
                return self::FACE2;
            }
        }
        //  'ӑс', 'ӗс'  - второе лицо сослагательного наклонения


        // Проверка аффиксов третьего лица
        foreach ($affArray as $affix) {
            if (in_array($affix, ['ӗ', 'ччӗр', 'ччӑр', 'тӑр', 'тӗр'])) {
                return self::FACE3;
            }
        }

        return self::UNKNOWN;
    }

    private function determineFaceOfNumeric(array $affArray): string
    {
        // у числительных 'ӑр', 'ӗр' - покзатели множественного числа
        // функцию разбирали с А.П. 
        // у меня в базе нет ['сӑм', 'сӗм'] 
        foreach ($affArray as $affix) {
            if (in_array($affix, ['сӑм', 'сӗм'])) {
                return self::FACE1;
            }
        }

        foreach ($affArray as $affix) {
            if (in_array($affix, ['с', 'с'])) {
                return self::FACE2;
            }
        }

        foreach ($affArray as $affix) {
            if (in_array($affix, ['шӗ', 'ш'])) {
                return self::FACE3;
            }
        }

        return self::UNKNOWN;
    }

    private function determineFaceOfPronoun(array $affArray): string
    {
        // В полной реализации здесь должна быть логика для местоимений из словаря
        // функцию разбирали с А.П.
        // Проверяем комбинации по вашим правилам:
        //  1-ое лицо  ['ӑм', 'ӑр', 'ӗм', 'ӗр', 'м'],
        //  2-ое лицо  ['ÿ', 'у',  'ӑр', 'ӗр']
        //  3- е лицо  ['и', 'ӗ']
        // $affixesSet = array_flip($affArray); // Создаем lookup-таблицу

        $affixesSet = [];
        foreach ($affArray as $affix) {
            if (is_string($affix) || is_int($affix)) {
                $affixesSet[$affix] = true;
            }
        }

        // Комбинация 1 лица: ӑм + ӑр или ӗм + ӗр
        if ((isset($affixesSet['ӑм']) && isset($affixesSet['ӑр'])) ||
            (isset($affixesSet['ӗм']) && isset($affixesSet['ӗр']))
        ) {
            return self::FACE1;
        }

        // Только ӑр или ӗр (без парного аффикса) - 2 лицо
        if ((isset($affixesSet['ӑр']) && !isset($affixesSet['ӑм'])) ||
            (isset($affixesSet['ӗр']) && !isset($affixesSet['ӗм']))
        ) {
            return self::FACE2;
        }

        // Одиночные аффиксы
        if (isset($affixesSet['ӑм']) || isset($affixesSet['ӗм']) || isset($affixesSet['м'])) {
            return self::FACE1;
        }

        if (isset($affixesSet['ÿ']) || isset($affixesSet['у'])) {
            return self::FACE2;
        }

        if (isset($affixesSet['и']) || isset($affixesSet['ӗ'])) {
            return self::FACE3;
        }

        return self::UNKNOWN;
    }

    private function determineFaceOfPrichastie(array $affArray, string $word): string
    {
        // Если есть специальные аффиксы причастия
        foreach ($affArray as $affix) {
            if (in_array($affix, $this->prich_mas)) {
                return self::UNKNOWN;
            }
        }

        return $this->determineFaceOfNoun($affArray, $word);
    }


    /**
     * Определяет падеж на основе уже найденных аффиксов
     * 
     * @param array $parsedAffixes Результат работы parseAffixes()
     * @return string Название падежа
     */
    public function determineCase(array $parsedAffixes): string
    {
        if (empty($parsedAffixes)) {
            return 'основной падеж';
        }

        $caseAffixes = [
            'притяжательный' => ['ăн', 'ĕн', 'н', 'ăнн', 'ĕнн', 'нн'],
            'дательный' => ['а', 'е', 'на', 'не'],
            'местный' => ['ра', 'ре', 'та', 'те', 'че', 'р', 'т', 'ч'],
            'исходный' => ['ран', 'рен', 'тан', 'тен', 'чен', 'ранн', 'ренн', 'танн', 'тенн', 'ченн'],
            'творительный' => ['па', 'пе', 'п', 'пала', 'пеле', 'пал', 'пел', 'палан', 'пелен', 'паланн', 'пеленн'],
            'лишительный' => ['сăр', 'сĕр', 'сăрр', 'сĕрр'],
            'причинно-целевой' => ['шăн', 'шĕн', 'шăнн', 'шĕнн']
        ];

        // Ищем ВСЕ аффиксы падежей в слове
        $foundCases = [];

        foreach ($parsedAffixes as $affixData) {
            $affix = $affixData['affix'];
            foreach ($caseAffixes as $case => $affixes) {
                if (in_array($affix, $affixes)) {
                    $foundCases[$case] = $affixData['level'];
                }
            }
        }

        if (!empty($foundCases)) {
            // Возвращаем падеж с НАИБОЛЕЕ ВЫСОКИМ УРОВНЕМ (самый "внешний" падеж)
            arsort($foundCases); // Сортируем по убыванию уровня
            $firstCase = array_key_first($foundCases);
            return $firstCase . ' падеж';
        }

        return 'неопределенный падеж';
    }



    /**
     * Вспомогательный метод для определения согласности
     */
    private function isConsonant(string $char): bool
    {
        $consonants = [
            'б',
            'в',
            'г',
            'д',
            'ж',
            'з',
            'й',
            'к',
            'л',
            'м',
            'н',
            'п',
            'р',
            'с',
            'т',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ӑ',
            'ӗ',
            'ÿ',
            'ҫ'
        ];

        return in_array(mb_strtolower($char), $consonants);
    }
}
