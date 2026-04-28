<?php

namespace App\Libraries\Morphology;

/**
 * Класс для работы с информацией об аффиксах
 */
class AffixInfoProvider
{
    /**
     * Режимы поиска
     */
    const SEARCH_MODE_ONLYGLAGOL = 'OnlyGlagol';
    const SEARCH_MODE_NOTGLAGOL = 'NotGlagol';
    const SEARCH_MODE_ANY = 'Any';
    const SEARCH_MODE_PRICHASTIE = 'Prichastie';
    const SEARCH_MODE_DEEPRICHASTIE = 'DeePrichastie';
    const SEARCH_MODE_PRILAGATELNOE = 'Prilagatelnoe';
    const SEARCH_MODE_NOTNARECHIE = 'NotNarechie';
    const SEARCH_MODE_NOTPRIL = 'NotPril';
    const SEARCH_MODE_NOTNARECHIEGLAGOL = 'NotNarechieGlagol';
    const SEARCH_MODE_SUSCHGLAGOL = 'SuschGlagol';
    const SEARCH_MODE_CHISLITELNOE = 'Chislitelnoe';
    const SEARCH_MODE_DEENOUN = 'deenoun';
    const SEARCH_MODE_ABSOLUTELYANY = 'AbsolutelyAny';
    
    /**
     * Режимы восстановления
     */
    const RECOVERY_MODE_WITH = 'With';
    const RECOVERY_MODE_WITHOUT = 'Without';
    const RECOVERY_MODE_BOTH = 'Both';
    
    /**
     * Добавляет аффикс к единице аффиксов
     *
     * @param string $aff Аффикс
     * @param string $AffUnit Единица аффиксов
     * @return string Обновленная единица аффиксов
     */
    public static function AddAffixToUnit(string $aff, string $AffUnit): string
    {
        if (!empty($AffUnit)) {
            if (mb_substr($AffUnit, -1) === '|') {
                $AffUnit = mb_substr($AffUnit, 0, -1);
            }
        }
        
        if (mb_substr($aff, 0, 1) === '|') {
            return $aff . $AffUnit;
        }
        
        return '|' . $aff . $AffUnit;
    }
    
    /**
     * Проверяет совместимость аффиксов
     *
     * @param string $aff Аффикс
     * @param string $AffUnit Единица аффиксов
     * @return bool Результат проверки
     */
    public static function AffixesSuitable(string $aff, string $AffUnit): bool
    {
        // Типы
        $afftype = '';
        $unittype = '';
        
        // Уровень аффикса
        $afflevel = self::LevelOfSingleAffix($aff, $afftype);
        
        // Уровень юнита
        $unitlevel = self::LevelOfAffUnit($AffUnit, $unittype);
        
        if ($afflevel < $unitlevel) {
            if (($afftype == $unittype) || ($afftype == Constants::ANY) || ($unittype == Constants::ANY)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Определяет уровень единичного аффикса
     *
     * @param string $AffixForCheck Аффикс для проверки
     * @param string &$singleafftype Тип аффикса (передается по ссылке)
     * @return int Уровень аффикса
     */
    public static function LevelOfSingleAffix(string $AffixForCheck, &$singleafftype): int
    {
        $singleafftype = Constants::ANY;
        $level = 888;
        
        $affmas = explode('|', $AffixForCheck);
        $affmas = array_filter($affmas);
        
        if (empty($AffixForCheck)) {
            $level = 100;
        } elseif ($AffixForCheck === '|') {
            $level = 90;
        } else {
            // Здесь должна быть логика определения уровня аффикса
            // В PHP мы не можем напрямую обращаться к статическим полям другого класса
            // Поэтому нужно будет передавать массив аффиксов в качестве параметра
        }
        
        return $level;
    }
    
    /**
     * Определяет уровень единицы аффиксов
     *
     * @param string $AffUnit Единица аффиксов
     * @param string &$unittype Тип единицы (передается по ссылке)
     * @return int Уровень единицы
     */
    public static function LevelOfAffUnit(string $AffUnit, &$unittype): int
    {
        $unittype = Constants::ANY;
        $level = 888;
        
        // Здесь должна быть логика определения уровня единицы аффиксов
        
        return $level;
    }
    
    /**
     * Определяет тип поиска
     *
     * @param string $AffUnit Единица аффиксов
     * @return string Тип поиска
     */
    public static function TypeOfSearch(string $AffUnit): string
    {
        // Здесь должна быть логика определения типа поиска
        return self::SEARCH_MODE_ANY;
    }
    
    /**
     * Определяет тип восстановления
     *
     * @param string $AffUnit Единица аффиксов
     * @return string Тип восстановления
     */
    public static function TypeOfRecovery(string $AffUnit): string
    {
        // Здесь должна быть логика определения типа восстановления
        return self::RECOVERY_MODE_BOTH;
    }
    
    /**
     * Выбирает шаблон
     *
     * @param string $AffUnit Единица аффиксов
     * @return string Шаблон
     */
    public static function PatternPicker(string $AffUnit): string
    {
        // Здесь должна быть логика выбора шаблона
        return '';
    }
    
    /**
     * Вырезает часть
     *
     * @param string $AffUnit Единица аффиксов
     * @param string $chast Часть
     * @return string Результат
     */
    public static function CutItOut(string $AffUnit, string $chast): string
    {
        // Здесь должна быть логика вырезания
        return '';
    }
} 