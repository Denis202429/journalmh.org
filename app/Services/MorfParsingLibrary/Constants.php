<?php

namespace App\Services\MorfParsingLibrary;

class Constants
{
    
    public static $AFFIXES_ARRAY = 
    [
        [
            "level" => 1,
            "affixes" => ['ҫӑм', 'ҫӗм', 'шар', 'шер', 'ӑшӗ', 'ӗшӗ', 'серен', 'ашкал', 'ешкел', 'шкал'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 2,
            "affixes" =>  ['тарах', 'терех', 'рах', 'рех'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 3,
            "affixes" => ['кал', 'кел', 'кала', 'келе', 'машкӑн', 'мешкӗн'],
           "type" => "OnlyGlagol"
        ],
        [
            "level" => 4,
            "affixes" => ['ай', 'ей'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 5,
            "affixes" =>  ['са', 'се', 'ӑтт', 'ӗтт', 'ӑттӑм', 'ӗттӗм', 'ӑттӑн', 'ӗттӗн', 'атт', 'етт', 'аттӑм', 'еттӗм', 'ттӑм', 'ттӗм', 'ттӑн', 'ттӗн', 'атна', 'етне', 'ӗчч'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 6,
            "affixes" =>  ['а', 'е'],
            "type" => "Any"
        ],
        [
            "level" => 7,
            "affixes" =>  ['ас', 'ес', 'асси', 'есси'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 8,
            "affixes" => ['ма', 'ме', 'маҫ', 'меҫ', 'масӑр', 'месӗр', 'малла', 'мелле', 'сассӑн', 'сессӗн'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 9,
            "affixes" =>  ['алла', 'елле', 'лла', 'лле', 'ла', 'ле'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 10,
            "affixes" => ['ни', 'Any'],
            "type" => "Any"
        ],
        [
            "level" => 11,
            "affixes" => ['хи'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 12,
            "affixes" => ['ман', 'мен'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 13,
            "affixes" => ['акан', 'екен', 'аканни', 'екенни', 'манни', 'менни'],
            "type" => "Any"
        ],
        [
            "level" => 14,
            "affixes" => ['ӑм', 'ӗм', 'мӗш'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 15,
            "affixes" => ['у', 'ÿ', 'и'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 16,
            "affixes" => ['ллӑ', 'ллӗ', 'лӑ', 'лӗ', 'лли', 'ли'],
            "type" => "NotGlagol"   // лӑx
        ],
        [
            "level" => 17,
            "affixes" =>  ['мелли', 'малли'],
            "type" => "Any"
        ],
        [
            "level" => 18,
            "affixes" => ['ри', 'ти'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 19,
            "affixes" =>  ['масть', 'маст', 'мест', 'мас', 'мес'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 20,
            "affixes" =>  ['м'],
           "type" => "Any"
        ],
        [
            "level" => 21,
            "affixes" =>  ['атчӗ', 'етчӗ', 'тӑп', 'тӗп', 'тӑм', 'тӗм', 'рӑм', 'рӗм', 'тӑр', 'тӗр', 'рӑр', 'рӗр', 'рӑн', 'рӗн', 'тӑн', 'тӗн', 'мӑн', 'мӗн', 'ать', 'ет', 'ап', 'еп', 'рӗҫ', 'чӗҫ', 'ӗҫ', 'иччен', 'мап', 'меп', 'сан', 'ан', 'ен', 'мӗ', 'нӑ', 'нӗ', 'нӑҫем', 'нӗҫем', 'аҫ', 'еҫ', 'ччӑр', 'ччӗр'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 22,
            "affixes" =>  ['скер'],
            "type" => "Any"
        ],
        [
            "level" => 23,
            "affixes" =>  ['ҫ', 'рӗ'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 24,
            "affixes" => ['ат', 'ет'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 25,
            "affixes" =>  ['ӑп', 'ӗп', 'пӑр', 'пӗр'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 26,
            "affixes" => ['ам', 'ем', 'асшӑн', 'есшӗн'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 27,
            "affixes" =>  ['ӑр', 'ӗр', 'ӑн', 'ӗн', 'сен'],
            "type" => "Any"
        ],
        [
            "level" => 28,
            "affixes" => ['ар', 'ер'],
            "type" => "OnlyGlagol"
        ],
        [
            "level" => 29,
            "affixes" => ['ӗ'],
            "type" => "Any"
        ],
        [
            "level" => 30,
            "affixes" =>  ['сем', 'сам'],
            "type" => "Any"
        ],
        [
            "level" => 31,
            "affixes" =>   ['н', 'сене', 'ра', 'ре', 'та', 'те', 'тан', 'тен', 'ран', 'рен', 'пала', 'пеле', 'палан', 'пелен', 'на', 'не', 'шӑн', 'шӗн'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 32,
            "affixes" =>  ['па', 'пе'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 33,
            "affixes" =>  ['сӑр', 'сӗр', 'сӑмӑр', 'сӗмӗр'],
            "type" => "NotGlagol"
        ],
        [
            "level" => 34,
            "affixes" => ['че', 'чи', 'чен', 'ччен'],
             "type" => "NotGlagol"
        ],
        [
            "level" => 35,
            "affixes" => ['ах', 'ех', 'х'], 
            "type" => "Any"
        ],
        [
             "level" => 36,
             "affixes" => ['ччӗ', 'чӗ'],
             "type" =>  "Any"
        ],
    ];               
    
    
    
    public const NULL = "null";
    public const UNKNOWN = "unknown";
    
    // face'ы
    public const FACE1 = "1е";
    public const FACE2 = "2е";
    public const FACE3 = "3е";
    
    // падежи
    public const OSN_P = "основной";
    public const ROD_P = "родительный";
    public const DAT_P = "дательный";
    public const MEST_P = "местный";
    public const ISCH_P = "исходный";
    public const TVOR_P = "творительный";
    public const LISH_P = "лишительный";
    public const PR_CEL_P = "причинно-целевой";
    
    // числа
    public const ED_CHISLO = "единственное";
    public const MN_CHISLO = "множественное";
    
    // время
    public const NAST_V = "настоящее";
    public const PROSH_V = "прошлое";
    public const BUD_V = "будущее";
    
    // отрицательность
    public const NEGATIVE = "отрицание";
    public const POSITIVE = "неотрицание";
    
    // инфинитив
    public const INF = "инфинитив";
    public const NOTINF = "неинфинитив";
    
    // части речи
    public const NOUN = "имя существительное";
    public const PRONOUN = "местоимение";
    public const VERB = "глагол";
    public const ADJECTIVE = "прилагательное";
    public const ADVERB = "наречие";
    public const NUMERIC = "имя числительное";
    public const PART = "частица";
    public const CONJ = "союз";
    public const DEEPRICHASTIE = "деепричастие";
    public const PRICHASTIE = "причастие";
    public const DEENOUN = "Dee/noun";
    
    // тип поиска
    public const ANY = "Any";
    public const ONLYGLAGOL = "OnlyGlagol";
    public const NOTGLAGOL = "NotGlagol";
}
