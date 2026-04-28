<?php

namespace App\Libraries\Morphology;

/**
 * Вспомогательный класс для морфологического анализатора
 */
class Helper
{
    // Разделители слов
    public static $separator = [' ', ',', '.', ':', "\t", "\n", '?', '!', '—', '"', '«', '»', '…', ';'];
    
    // Окончания слов
    public static $separator_EndOfWord = ['-и', '-ши', '-ҫке', '-ха', '-им', '-шим', '-а', '-е', '-иҫ', '-мӗн', '-тӑк', '-тӗк', '-тӑр', '-тӗр', '-ах', '-ех'];
    
    // Буквы
    public static $letters = ['а', 'ӑ', 'б', 'в', 'г', 'д', 'е', 'ё', 'ӗ', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'ҫ', 'т', 'у', 'ÿ', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ы', 'э', 'ю', 'я'];
    
    // Значения по умолчанию
    public static $defaults = [
        'noun,' . Constants::NOUN . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::FACE1 . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'verb,' . Constants::VERB . ',' . Constants::ED_CHISLO . ',' . Constants::NAST_V . ',' . Constants::NULL . ',' . Constants::FACE2 . ',' . Constants::POSITIVE . ',' . Constants::NOTINF,
        'adj,' . Constants::ADJECTIVE . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::NULL . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'adv,' . Constants::ADVERB . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::NULL . ',' . Constants::NULL . ',' . Constants::POSITIVE . ',' . Constants::NULL,
        'pron,' . Constants::PRONOUN . ',' . Constants::ED_CHISLO . ',' . Constants::NULL . ',' . Constants::OSN_P . ',' . Constants::FACE3 . ',' . Constants::POSITIVE . ',' . Constants::NULL
    ];
    
    // Значения по умолчанию для местоимений
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
    
    // Специальные слова
    public static $onlyXI = ['ӗмӗр', 'паян', 'ӗнер', 'ыран', 'хӗлле', 'ҫулла', 'кӗркунне', 'ҫуркунне', 'хупах'];
    public static $onlyRI = ['л', 'н', 'д', 'т', 'ь'];
    public static $GLAS = ['а', 'е', 'ӑ', 'ӗ', 'и'];
    public static $SayMyName = ['кил', 'тул'];
    
    /**
     * Конвертирует корень слова
     *
     * @param string $root Корень слова
     * @param string $chastrechi Часть речи
     * @return string Конвертированный корень
     */
    public static function convertRoot(string $root, string $chastrechi): string
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
    
    /**
     * Конвертирует часть речи
     *
     * @param string $p Часть речи
     * @return string Конвертированная часть речи
     */
    public static function convertPartOfSpeech(string $p): string
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
            case 'deeprichastie':
                return Constants::DEEPRICHASTIE;
            case 'prichastie':
                return Constants::PRICHASTIE;
            case 'deenoun':
                return Constants::DEENOUN;
            default:
                return Constants::UNKNOWN;
        }
    }
    
    /**
     * Удаляет окончания слова
     *
     * @param string $word Слово
     * @param string &$aff Аффикс (передается по ссылке)
     * @return string Слово без окончания
     */
    public static function loseEnds(string $word, &$aff): string
    {
        $aff = '';
        
        foreach (self::$separator_EndOfWord as $end) {
            if (mb_substr($word, -mb_strlen($end)) === $end) {
                $aff = $end;
                return mb_substr($word, 0, mb_strlen($word) - mb_strlen($end));
            }
        }
        
        return $word;
    }
    
    /**
     * Решает часть речи
     *
     * @param string $chastrechi Часть речи
     * @param string $pattern Шаблон
     * @return string Решенная часть речи
     */
    public static function ChastRechiSolver(string $chastrechi, string $pattern): string
    {
        if ($chastrechi == Constants::UNKNOWN) {
            return $pattern;
        }
        
        return $chastrechi;
    }
    
    /**
     * Получает количество строк в словаре
     *
     * @param array $dict Словарь
     * @return void
     */
    public static function GetNumbersOfLines(array $dict): void
    {
        // В PHP не нужно считать строки, так как мы работаем с массивами
    }
    
    /**
     * Получает текущие номера
     *
     * @param string $word Слово
     * @param int &$begin Начальный номер (передается по ссылке)
     * @param int &$end Конечный номер (передается по ссылке)
     * @return void
     */
    public static function getCurrentNumbers(string $word, &$begin, &$end): void
    {
        $begin = 0;
        $end = 0;
        
        // Здесь должна быть логика определения номеров
    }
    
    /**
     * Трансформирует некоторые слова
     *
     * @param string $word Слово
     * @return string Трансформированное слово
     */
    public static function TransformSomeWords(string $word): string
    {
        // Здесь должна быть логика трансформации слов
        return $word;
    }
    
    /**
     * Проверяет совместимость
     *
     * @param string $word Слово
     * @param string $AffUnit Единица аффикса
     * @param string $chastrechi Часть речи
     * @return bool Результат проверки
     */
    public static function CheckCompatibility(string $word, string $AffUnit, string $chastrechi): bool
    {
        // Здесь должна быть логика проверки совместимости
        return true;
    }
    
    /**
     * Настраивает
     *
     * @param string $laff Левый аффикс
     * @param string $raff Правый аффикс
     * @return bool Результат настройки
     */
    public static function Adjust(string $laff, string $raff): bool
    {
        // Здесь должна быть логика настройки
        return true;
    }
} 