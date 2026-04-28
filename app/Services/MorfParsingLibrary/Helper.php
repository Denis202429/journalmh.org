<?php
namespace App\Services\MorfParsingLibrary;

class Helper
{
    // Def values
    public static $separator = [' ', ',', '.', ':', '\t', '\n', '?', '!', '—', '"', '«', '»', '…', ';'];
    public static $l = [];
    public static $separator_EndOfWord = ['-и', '-ши', '-ҫке', '-ха', '-им', '-шим', '-а', '-е', '-иҫ', '-мӗн', '-тӑк', '-тӗк', '-тӑр', '-тӗр', '-ах', '-ех'];
    public static $letters = ['а', 'ӑ', 'б', 'в', 'г', 'д', 'е', 'ё', 'ӗ', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'ҫ', 'т', 'у', 'ÿ', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ы', 'э', 'ю', 'я'];

    public static $defaults = [
        'noun,' . Constants::NOUN . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::FACE1 . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'verb,' . Constants::VERB . ',' . Constants::ED_CHISLO . ',' . Constants::NAST_V . ',' . Constants::NULL . ',' . Constants::FACE2 . ',' . Constants::POSITIVE . ',' . Constants::NOTINF,
        'adj,' . Constants::ADJECTIVE . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::NULL . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'adv,' . Constants::ADVERB . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::NULL . ',' . Constants::NULL . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'pron,' . Constants::PRONOUN . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::FACE3 . ',' . Constants::POSITIVE . ',' . Constants::NULL
    ];

    public static $pron_defaults = [
        'эпӗ,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE1,
        'эп,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE1,
        'эсӗ,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'эс,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'вӑл,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE3,
        'эпир,' . Constants::MN_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE1,
        'эсир,' . Constants::MN_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'вӗсем,' . Constants::MN_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE3,
        'хам,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE1,
        'ху,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'хӑй,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE3,
        'хамӑр,' . Constants::MN_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE1,
        'хӑвӑр,' . Constants::MN_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'хӑв,' . Constants::ED_CHISLO . ',' . Constants::OSN_P . ',' . Constants::FACE2,
        'пирӗн,' . Constants::MN_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE1,
        'сирӗн,' . Constants::MN_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE2,
        'вӗсен,' . Constants::MN_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE3,
        'ман,' . Constants::ED_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE1,
        'сан,' . Constants::ED_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE2,
        'ун,' . Constants::ED_CHISLO . ',' . Constants::ROD_P . ',' . Constants::FACE3
    ];

    public static $onlyXI = ['ӗмӗр', 'паян', 'ӗнер', 'ыран', 'хӗлле', 'ҫулла', 'кӗркунне', 'ҫуркунне', 'хупах'];
    public static $onlyRI = ['л', 'н', 'д', 'т', 'ь'];
    public static $GLAS = ['а', 'е', 'ӑ', 'ӗ', 'и'];
    public static $SayMyName = ['кил', 'тул'];
    public static $AffUnit = [];

   //  ҫӗӑ
   
   
//     1. convertRoot($root, $chastrechi)
// Эта функция преобразует корень слова в зависимости от части речи.

// Для глаголов (Constants::VERB): если корень совпадает с одним из предустановленных значений (например, шӑв, тӑв, сӑв, ҫӑв),
// возвращается корень с изменённым окончанием — первая буква корня сохраняется, а оставшаяся часть заменяется на 'у'.
// Специально обрабатывается корень 'сӗв', который преобразуется в 'сÿ'.
// Для существительных (Constants::NOUN): также заменяются окончания, если корень — тӑв или ҫӑв.
// Особый случай: если корень равен 'вӗсен', он преобразуется в 'вӗсем'.
// Если ни одно из условий не выполнено, функция возвращает исходный корень.
    // Convert root method
    public static function convertRoot($root, $chastrechi)
    {
        if ($chastrechi == Constants::VERB) {
            switch ($root) {
                case 'шӑв':
                case 'тӑв':
                case 'сӑв':
                case 'ҫӑв':
                    return $root[0] . 'у';
                case 'сӗв':
                    return 'сÿ';
            }
        }

        if ($chastrechi == Constants::NOUN) {
            switch ($root) {
                case 'тӑв':
                case 'ҫӑв':
                    return $root[0] . 'у';
            }
        }

        if ($root == 'вӗсен') {
            return 'вӗсем';
        }

        return $root;
    }


    // convertPartOfSpeech($p)
    // Эта функция преобразует строковое представление части речи (например, 'noun', 'verb') в константу из класса Constants. Если строка 
    //не совпадает ни с одним из предопределённых значений, возвращается исходная строка.
    
    // Пример: 'noun' будет преобразован в Constants::NOUN, 'verb' — в Constants::VERB и так далее.
    // Также поддерживаются русскоязычные варианты частей речи, такие как 'сущ-е' для существительных или 'глагол' для глаголов.
    
    // Convert part of speech method
    public static function convertPartOfSpeech($p)
    {
        switch ($p) {
            case 'noun':
                return Constants::NOUN;
            case 'verb':
                return Constants::VERB;
            case 'adj':
                return Constants::ADJECTIVE;
            case 'pron':
                return Constants::PRONOUN;
            case 'num':
                return Constants::NUMERIC;
            case 'adv':
                return Constants::ADVERB;
            case 'part':
                return Constants::PART;
            case 'conj':
                return Constants::CONJ;
            case 'прил-е':
                return Constants::ADJECTIVE;
            case 'сущ-е':
                return Constants::NOUN;
            case 'числ-е':
                return Constants::NUMERIC;
            case 'мест-е':
                return Constants::PRONOUN;
            case 'глагол':
                return Constants::VERB;
            default:
                return $p;
        }
    }

    // 3. loseEnds($word, &$aff)
    // Эта функция удаляет окончания (аффиксы) из слова и сохраняет их в переменной $aff.
    
    // Алгоритм: цикл ищет в слове окончания, указанные в массиве self::$separator_EndOfWord. Если окончание найдено, оно удаляется из слова и добавляется к строке $aff.
    // Окончания сохраняются в формате |окончание|окончание..., где каждый удалённый аффикс добавляется в начало строки $aff.
    // Цикл продолжается, пока не будут удалены все возможные окончания.
    // В результате возвращается корень слова без окончаний.

    // Lose ends method
    public static function loseEnds($word, &$aff)
    {
        // Инициализируем переменную $aff как пустую строку.
        // $aff будет хранить удалённые окончания (аффиксы) слова.
        $aff = '';
    
        // Флаг для выхода из внешнего цикла, когда все окончания будут удалены.
        $flag1 = false;
    
        // Запускаем цикл, который будет работать, пока флаг $flag1 не станет истинным.
        // Этот цикл продолжает удалять окончания до тех пор, пока их находят.
        while (!$flag1) {
            // Внутренний флаг для отслеживания того, было ли окончание удалено в текущей итерации.
            $flag2 = false;
    
            // Проходим по каждому окончанию в массиве self::$separator_EndOfWord.
            foreach (self::$separator_EndOfWord as $ending) {
                // Проверяем, оканчивается ли слово на текущее окончание из массива.
                if (str_ends_with($word, $ending)) {
                    // Если да, то удаляем это окончание из слова.
                    // Для этого мы обрезаем слово на длину окончания (функция substr).
                    $word = substr($word, 0, -strlen($ending));
    
                    // Добавляем удалённое окончание к переменной $aff.
                    // Каждый аффикс добавляется в начало строки $aff и разделяется символом '|'.
                    $aff = '|' . $ending . $aff;
    
                    // Устанавливаем флаг $flag2 в true, чтобы показать, что окончание было удалено.
                    $flag2 = true;
    
                    // Прерываем цикл, чтобы перейти к следующей итерации внешнего цикла.
                    break;
                }
            }
    
            // Если ни одно окончание не было найдено и удалено (флаг $flag2 остался false),
            // это значит, что больше нет окончаний для удаления, и мы можем завершить цикл.
            if (!$flag2) {
                $flag1 = true;
            }
        }
    
        // Возвращаем результат — слово без окончаний (корневую часть).
        return $word;
    }
    
    // 4. ChastRechiSolver($chastrechi, $pattern)
    // Эта функция решает, какую часть речи выбрать, основываясь на переданном шаблоне ($pattern).
    
    // Алгоритм: шаблон разбивается по символу |, и рассматривается его второй элемент.
    // В зависимости от значения этого элемента функция возвращает исходную часть речи ($chastrechi), если она совпадает с 'Same', или модифицированную часть речи, например, Constants::DEEPRICHASTIE для деепричастий.
    // Если ни одно из условий не выполнено, возвращается элемент шаблона.
    // ChastRechiSolver method
    public static function ChastRechiSolver($chastrechi, $pattern)
    {
        $ex = explode('|', $pattern)[1];
        switch ($ex) {
            case 'Same':
                return $chastrechi;
            case Constants::DEENOUN:
                return $chastrechi == Constants::VERB ? Constants::DEEPRICHASTIE : $chastrechi;
            default:
                return $ex;
        }
    }

    // Get numbers of lines method
    // GetNumbersOfLines($dict)
    // Эта функция анализирует словарь ($dict) и сохраняет индексы строк, начинающихся с определённых букв.
    
    // Алгоритм: итерируется по словарю, сравнивая каждое слово с предопределённым массивом букв self::$letters.
    // Если слово начинается с определённой буквы из массива, индекс этого слова сохраняется в массиве self::$l.

   


    public static function getNumbersOfLines($dict)
    {
        // Инициализируем пустой массив для хранения позиций
        self::$l = [];
        $k = 0;

        // Проходим по каждому элементу словаря
        foreach ($dict as $i => $word) {
            // Проверяем, начинается ли слово с текущей буквы
            if ($k < count(self::$letters) && str_starts_with($word, self::$letters[$k])) {
                self::$l[] = $i;
                $k += 1;
            }
        }
    }


    // Get current numbers method

    // getCurrentNumbers($word, &$begin, &$end)
    // Эта функция получает начальный и конечный индексы строк, соответствующих букве, с которой начинается слово.
    
    // Алгоритм: итерируется по массиву букв, и если слово начинается с текущей буквы, устанавливаются значения переменных $begin и $end, соответствующие номерам строк.
    // Пример: если слово начинается на 'a', начальный и конечный индексы будут установлены согласно предопределённым значениям.
    // Получение нужной пары номеров строк в словаре
  
    public static function getCurrentNumbers($word, &$begin, &$end)
    {
        $begin = $end = 0;
         // Проверка, что $l не пустой
    if (empty(self::$l)) {
        dump("Ошибка: массив l не инициализирован.");
    }
        foreach (self::$letters as $i => $letter) {
            if (mb_substr($word, 0, 1) === $letter) {
                if ($i < count(self::$l) - 1) {
                    $begin = self::$l[$i];
//                    $begin = self::$l[$i];
                    $end = self::$l[$i + 1];
                } else {
                   // $begin = 1;
                    $begin = self::$l[$i];
                    $end = count(MorfParser::$Dictionary); // Предполагается, что MorfParser::$Dictionary — это массив
                }
                break;
            }
        }
    }




    public static function transformSomeWords($word)
    {
        if (mb_strlen($word) < 3) {
            return $word;
        }
    
        $excepts = ["паян", "чее", "япала"];
        foreach ($excepts as $except) {
            if (mb_strpos($word, $except) !== false) {
                return $word;
            }
        }
    
        // 1
        $a = ["ӑяя", "аяя", "уя", "ӑя", "ая", "ее", "яя", "яй"];
        $b = ["ӑйайа", "айайа", "уйа", "ӑйа", "айа", "ейе", "айа", "ай"];
        
        foreach ($a as $index => $substring) {
            // Проверка на наличие подстроки в пределах второго и предпоследнего символов
            $position = mb_strpos(mb_substr($word, 1, mb_strlen($word) - 2), $substring);
            if ($position !== false) {
                $word = str_replace($substring, $b[$index], $word);
                return $word;
            }
        }
    
        if (mb_strpos($word, "ятт") !== false) {
            return $word;
        }
    
        // 2
        $c = ["ят", "яп", "яҫ"];
        foreach ($c as $substring) {
            if (mb_strpos($word, $substring) !== false) {
                $word = str_replace("я", "яа", $word);
                break;
            }
        }
    
        // 3
        $d = ["кал", "кел"];
        foreach ($d as $substring) {
            $position = mb_strpos(mb_substr($word, 1, mb_strlen($word) - 3), $substring);
            if ($position !== false) {
                $word = str_replace($substring, $substring . mb_substr($substring, 1, 1), $word);
                break;
            }
        }
    
        return $word;
    }



// CheckCompatibility($word, $AffUnit, $chastrechi)
// Эта функция проверяет совместимость слова с аффиксами и частью речи.

// Алгоритм: разбивает аффиксную строку $AffUnit на массив и анализирует различные условия, такие как согласные в слове, звуковая структура, окончание слова и первая буква аффикса.
// В зависимости от части речи и аффиксов, проверяются разные комбинации условий для установления совместимости.
// В результате возвращается булевый результат — совместимо ли слово с аффиксами.
    // Проверка совместимости слова с аффиксом
    public static function CheckCompatibility($word, $AffUnit, $chastrechi)
    {
        $result = true;
        $aff_mas = array_filter(explode('|', $AffUnit));
        Helper::$AffUnit = $aff_mas;
        $word_lastsymbol = $word[strlen($word) - 1];
        $aff_firstsymbol = $aff_mas[0][0];
        $aff_first = $aff_mas[0];

        $sogl = ContextRules::Soglasnaya($word);
        $consistent = ContextRules::Consistency($word);
        $zvonk = ContextRules::Zvonko($word_lastsymbol);

        // Условия проверки
        $s1 = ($sogl && $word_lastsymbol == $aff_firstsymbol && $word_lastsymbol != 'н' && $word_lastsymbol != 'х');
        $s2 = (($aff_first == "а" || $aff_first == "е") && count($aff_mas) > 1);
        $s3 = (($aff_first == "а" || $aff_first == "е") && count($aff_mas) > 1 && $aff_mas[1] == "ҫ");
        $s4 = (($aff_first == "а" || $aff_first == "е") && count($aff_mas) > 1 && ($aff_mas[1] == "тпӑр" || $aff_mas[1] == "тпӗр"));
        $s5 = (count($aff_mas) > 1 && ($aff_first == "а" || $aff_first == "е") && ($aff_mas[1] == "ма" || $aff_mas[1] == "ме"));
        $s6 = ($chastrechi == Constants::NOUN || $chastrechi == Constants::ADJECTIVE || $chastrechi == Constants::NUMERIC) && ((!$sogl || $zvonk) && $aff_first == "чӗ" || $sogl && !$zvonk && $aff_first == "ччӗ");
        $s7 = ($chastrechi == Constants::NOUN || $chastrechi == Constants::ADJECTIVE || $chastrechi == Constants::NUMERIC) && ((!$sogl || $zvonk) && $aff_first == "чен" || $sogl && !$zvonk && $aff_first == "ччен");
        $s8 = ($sogl && ($aff_first == "ллӑ" || $aff_first == "ллӗ" || $aff_first == "лли")) || (!$sogl && ($aff_first == "лӑ" || $aff_first == "лӗ" || $aff_first == "ли"));
        $s9 = ($sogl && ($aff_first == "лла" || $aff_first == "лле")) || (!$sogl && ($aff_first == "ла" || $aff_first == "ле"));
        $s10 = ($aff_first == "хи" && !in_array($word, self::$onlyXI)) || (($aff_first == "ри" || $aff_first == "ти") && in_array($word, self::$onlyXI));
        $s11 = (($aff_first == "хи" || $aff_first == "ти") && in_array($word_lastsymbol,  self::$onlyRI) && !in_array($word, self::$SayMyName));
        $s12 = ($aff_first == "шкал" && $word_lastsymbol != 'о');
        $s13 = ($chastrechi == Constants::ADJECTIVE || $chastrechi == Constants::NUMERIC || $chastrechi == Constants::NOUN) && in_array($word_lastsymbol, self::$GLAS) && ($aff_first == "ӗн" || $aff_first == "ӑн");
        $s14 = ($chastrechi == Constants::ADJECTIVE || $chastrechi == Constants::NUMERIC || $chastrechi == Constants::PRONOUN) && $aff_first == "ӗ";
        $s15 = ($aff_first == "ах" || $aff_first == "ех") && ($word_lastsymbol == 'а' || $word_lastsymbol == 'е');
        $s17 = ($word_lastsymbol == 'т' && $aff_firstsymbol == 'ч' && $chastrechi == Constants::VERB);
        $s18 = ($chastrechi == Constants::ADVERB && in_array("ӗ", $aff_mas));

        $temp = $s1 || $s2 || $s3 || $s4 || $s5 || $s6 || $s7 || $s8 || $s9 || $s10 || $s11 || $s12 || $s13 || $s14 || $s15 || $s17 || $s18;

        // Условия проверки аффикс и аффикс
        $k01 = (in_array("мас", $aff_mas) || in_array("мес", $aff_mas)) && (in_array("тӑр", $aff_mas) || in_array("тӗр", $aff_mas));
        $k02 = (in_array("мас", $aff_mas) || in_array("мес", $aff_mas)) && (in_array("ӑр", $aff_mas) || in_array("ӗр", $aff_mas));
        $k03 = (in_array("ат", $aff_mas) || in_array("ет", $aff_mas)) && in_array("чӗ", $aff_mas);
        $k04 = in_array("н", $aff_mas) && in_array("ӗ", $aff_mas);
        $k05 = in_array("ӑн", $aff_mas) && in_array("чӗ", $aff_mas);
        $k07 = (in_array("ар", $aff_mas) || in_array("ер", $aff_mas)) && (in_array("ат", $aff_mas) || in_array("ет", $aff_mas));
        $k08 = (in_array("ас", $aff_mas) || in_array("ес", $aff_mas)) && (in_array("ан", $aff_mas) || in_array("ен", $aff_mas));
        $k09 = in_array("н", $aff_mas) && (in_array("чи", $aff_mas) || in_array("чӗ", $aff_mas));

        $temp1 = $k01 || $k02 || $k03 || $k04 || $k05 || $k07 || $k08 || $k09;

        if ($temp || $temp1) $result = false;

        return $result;
    }

//Функция adjust принимает два параметра $laff и $raff, которые, судя по названию, могут быть аффиксами.
// Цель функции — проверить, находятся ли эти два аффикса рядом в массиве Helper::$AffUnit.

    public static function adjust($laff, $raff)
    {
        $lpos = array_search($laff, Helper::$AffUnit);
        $rpos = array_search($raff, Helper::$AffUnit);

        if ($lpos === false || $rpos === false) {
            return false;
        } else if (abs($lpos - $rpos) == 1) {
            return true;
        } else {
            return false;
        }
    }
}
