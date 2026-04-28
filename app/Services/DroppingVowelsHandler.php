<?php

namespace App\Services;

/**
 * Обработка выпадающих конечных гласных
 */
class DroppingVowelsHandler
{
    // Гласные, которые могут выпадать
    private const DROPPING_VOWELS = ['ӑ', 'ӗ', 'у', 'ÿ', 'а', 'о', 'я', 'е'];
    
    // Гласные, которые НЕ выпадают
    private const NON_DROPPING_VOWELS = ['ы', 'и', 'э', 'ю'];
    
    /**
     * Получить все возможные усеченные основы для слова из словаря
     */
    public static function getTruncatedStems(string $dictionaryWord): array
    {
        $stems = [$dictionaryWord]; // Полная форма
        
        // Проверяем, оканчивается ли слово на выпадающую гласную
        $lastChar = mb_substr($dictionaryWord, -1);
        
        if (self::isDroppingVowel($lastChar)) {
            // Усекаем последнюю гласную
            $truncated = mb_substr($dictionaryWord, 0, -1);
            if (mb_strlen($truncated) >= 2) { // Минимум 2 буквы
                $stems[] = $truncated;
            }
            
            // Также можно усечь две гласные подряд (редко, но бывает)
            if (mb_strlen($dictionaryWord) > 2) {
                $lastTwo = mb_substr($dictionaryWord, -2);
                $secondLast = mb_substr($lastTwo, 0, 1);
                
                if (self::isDroppingVowel($secondLast)) {
                    $doubleTruncated = mb_substr($dictionaryWord, 0, -2);
                    if (mb_strlen($doubleTruncated) >= 2) {
                        $stems[] = $doubleTruncated;
                    }
                }
            }
        }
        
        return array_unique($stems);
    }
    
    /**
     * Получить все возможные основы для поиска в анализируемом слове
     */
    public static function getPossibleBases(string $inputWord): array
    {
        $bases = [$inputWord]; // Исходное слово
        
        // 1. Добавляем возможные выпавшие гласные к концу
        foreach (self::DROPPING_VOWELS as $vowel) {
            $bases[] = $inputWord . $vowel;
        }
        
        // 2. Заменяем конечную согласную на возможные выпавшие гласные
        $lastChar = mb_substr($inputWord, -1);
        if (!self::isVowel($lastChar)) {
            foreach (self::DROPPING_VOWELS as $vowel) {
                $bases[] = mb_substr($inputWord, 0, -1) . $vowel;
            }
        }
        
        // 3. Для особого случая: когда аффикс начинается с той же гласной, что выпала
        // Например: вула + ӑп → вулӑп
        if (mb_strlen($inputWord) > 2) {
            $lastTwo = mb_substr($inputWord, -2);
            $vowel = mb_substr($lastTwo, 0, 1);
            $consonant = mb_substr($lastTwo, 1, 1);
            
            if (self::isDroppingVowel($vowel) && !self::isVowel($consonant)) {
                // Возможно это усеченная основа + аффикс, начинающийся с выпавшей гласной
                $base = mb_substr($inputWord, 0, -2) . $vowel;
                $bases[] = $base;
            }
        }
        
        return array_unique(array_filter($bases, function($base) {
            return mb_strlen($base) >= 2;
        }));
    }
    
    /**
     * Проверяет, может ли гласная выпадать
     */
    public static function isDroppingVowel(string $char): bool
    {
        return in_array($char, self::DROPPING_VOWELS);
    }
    
    /**
     * Проверяет, является ли символ гласной
     */
    public static function isVowel(string $char): bool
    {
        return self::isDroppingVowel($char) || in_array($char, self::NON_DROPPING_VOWELS);
    }
    
    /**
     * Восстанавливает полную форму из усеченной основы + аффикса
     */
    public static function restoreFullForm(string $truncatedStem, string $affix): ?array
    {
        // Если аффикс начинается с выпадающей гласной
        $firstChar = mb_substr($affix, 0, 1);
        
        if (self::isDroppingVowel($firstChar)) {
            // Возможно, это выпавшая гласная основы + аффикс
            $possibleFullForm = $truncatedStem . $firstChar;
            $remainingAffix = mb_substr($affix, 1);
            
            return [
                'full_form' => $possibleFullForm,
                'actual_affix' => $remainingAffix
            ];
        }
        
        return null;
    }
}