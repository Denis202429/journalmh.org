<?php

namespace App\Services;

/**
 * Класс для работы с контекстными правилами чувашского языка
 * Правила встроены в код (не загружаются из файла)
 */
class ContextRules
{
    // Статические переменные
    public static string $WORD;
    public static string $AFFUNIT;
    
    // Константы для типов символов
    public const B = 'аӑуы';      // твердые гласные
    public const C = 'бвгджзйклмнпрсҫтфхцчшщ'; // согласные
    public const F = 'еӗÿя';      // мягкие гласные
    
    // Встроенные правила (массив вместо чтения из файла)
    private const RULES = [
        // ЛК;ПК;ЛК~;символы;часть_речи
        'нк;а;;ă;сущ-е',
        'ăл,ад,ал,ăн;ĕ,ĕн,е,ех,ÿ,ешкел;;ь;сущ-е',
        'еч,ач,уч,яч,рч;ĕ,ĕн;-1;т,д,дь;сущ-е',
        'ăв;;-2;у;сущ-е',
        'ĕв;;-2;ÿ;сущ-е',
        'мм,кк;и,ăн,ĕн;-1;;прил-е',
        '2C;ăн,ĕн;;;сущ-е,числ-е',
        '2C;и;-1;ă,ĕ;сущ-е,прил-е',
        '2C;B;-1;ă;сущ-е',
        '2C;F;-1;ĕ;сущ-е',
        ';ин;;а,е;',
        ';ăн;;;мест-е,сущ-е',
        'кĕ,пы,шă,тă,па,я,пе,йĕ,ху,кÿ;;;р;глагол',
        'ка,су,пу,ту;F;;й;глагол',
        'ий;ĕм,ÿ,ÿн,ĕ,ĕн;-1;;сущ-е,глагол',
        ';ĕ;;;сущ-е,мест-е,числ-е',
        'ей;ĕттĕм,ĕттĕн,ĕн;-1;;глагол',
    ];
    
// у меня вопрос по правилу 'ăв;;-2;у;сущ-е', то есть мы ищем основу на 'ăв' и аффикс может быть любым и 2 символа удаляем с основы и
// прибавляем к основе у верно? Но чего я не пойму как мы узнаем что это надо искать в существительных? Как и где это определяется. 
// я могу скинуть коды программы на С# если надо. 

    /**
     * Поиск правила обработки контекстов
     */
    public static function findTemplate(string $word, string $affUnit, array &$syllables, ?string &$chastrechi): void
    {
        self::$WORD = $word;
        self::$AFFUNIT = $affUnit;
        
        // Получение левого контекста (последние 2 символа)
        $lcon = mb_strlen($word) > 1 ? mb_substr($word, -2) : $word;
        
        // Получение правого контекста (первый символ аффикса)
        $affParts = explode('|', $affUnit);
        $rcon = !empty($affParts) && $affParts[0] !== '' ? mb_substr($affParts[0], 0, 1) : '';
        
        foreach (self::RULES as $line) {
            $rule = explode(';', $line);
            
            if (self::checkLeftContext($lcon, $rule[0] ?? '') && 
                self::checkRightContext($rcon, $rule[1] ?? '')) {
                
                // Согласно правилу
                self::wordModify($word, $rule[2] ?? ''); // слово
                
                $syllables = self::extractSyllables($rule[3] ?? ''); // символы восстановления
                $chastrechi = $rule[4] ?? ''; // часть речи
                
                return;
            }
        }
        
        // Если правило не найдено
        $syllables = [];
        $chastrechi = null;
    }
    /**
     * Символы по умолчанию для восстановления
     */
    public static function defaultSyllables(string $affUnit): array
    {
        // Простая логика определения типа восстановления
        $affParts = explode('|', $affUnit);
        $firstAffix = $affParts[0] ?? '';
        
        // Эвристика: если аффикс начинается с гласной, вероятно нужно восстановление
        if (mb_strlen($firstAffix) > 0) {
            $firstChar = mb_substr($firstAffix, 0, 1);
            if (mb_strpos(self::B . self::F, $firstChar) !== false) {
                return ['а', 'е', 'ӗ', 'я', '']; // both
            }
        }
        
        return ['']; // without
    }
    
    /**
     * Проверка подходимости части речи
     */
    public static function checkChastRechi(string $chastrechi, ?string $contextChastrechi): bool
    {
        if ($contextChastrechi === null || $contextChastrechi === '') {
            return false;
        }
        
        // Конвертируем часть речи в английский формат
        $currentPos = self::convertPosToEnglish($chastrechi);
        $rulePositions = explode(',', $contextChastrechi);
        
        foreach ($rulePositions as $rulePos) {
            $convertedRulePos = self::convertPosToEnglish(trim($rulePos));
            if ($convertedRulePos === $currentPos) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Конвертация части речи (русский/чувашский → английский)
     */
    private static function convertPosToEnglish(string $pos): string
    {
        $map = [
            'сущ-е' => 'noun',
            'сущ' => 'noun',
            'прил-е' => 'adj',
            'прил' => 'adj',
            'глагол' => 'verb',
            'глаг' => 'verb',
            'числ-е' => 'num',
            'числ' => 'num',
            'мест-е' => 'pronoun',
            'мест' => 'pronoun',
            'нар-е' => 'adv',
            'нар' => 'adv',
            'част-ца' => 'part',
            'част' => 'part',
            'предл' => 'prep',
            'союз' => 'conj'
        ];
        
        return $map[$pos] ?? $pos;
    }
    
    /**
     * Формирование массива символов
     */
    private static function extractSyllables(string $pattern): array
    {
        if ($pattern === '') {
            return [''];
        }
        
        return explode(',', $pattern);
    }
    
    /**
     * Редактирование слова согласно правилу
     */
    private static function wordModify(string $word, string $pattern): void
    {
        if ($pattern === '') {
            return;
        }
        
        if (str_starts_with($pattern, '-')) {
            $removeCount = (int)mb_substr($pattern, 1);
            if ($removeCount > 0 && $removeCount <= mb_strlen($word)) {
                self::$WORD = mb_substr($word, 0, -$removeCount);
            }
        }
        // Можно добавить другие типы изменений при необходимости
    }
    
    /**
     * Проверка левого контекста
     */
    private static function checkLeftContext(string $lconInput, string $lconRule): bool
    {
        if ($lconRule === '') {
            return true;
        }
        
        if ($lconInput === $lconRule) {
            return true;
        }
        
        return self::compatibleLeft($lconInput, $lconRule);
    }
    
    /**
     * Проверка правого контекста
     */
    private static function checkRightContext(string $rconInput, string $rconRule): bool
    {
        if ($rconRule === '') {
            return true;
        }
        
        if ($rconInput === $rconRule) {
            return true;
        }
        
        return self::compatibleRight($rconInput, $rconRule);
    }
    
    /**
     * Проверка совместимости левого контекста
     */
    private static function compatibleLeft(string $input, string $rule): bool
    {
        // Проверка списка вариантов
        $options = explode(',', $rule);
        if (in_array($input, $options, true)) {
            return true;
        }
        
        // Проверка специальных шаблонов
        if ($rule === '2C') {
            // Две согласные подряд
            if (mb_strlen($input) === 2) {
                $first = mb_substr($input, 0, 1);
                $second = mb_substr($input, 1, 1);
                return ($first === $second) && (mb_strpos(self::C, $first) !== false);
            }
        }
        
        // Шаблон C + гласная (например "Că")
        if (mb_strlen($rule) === 2 && $rule[0] === 'C') {
            $vowel = mb_substr($rule, 1, 1);
            if (mb_strlen($input) === 2) {
                $first = mb_substr($input, 0, 1);
                $second = mb_substr($input, 1, 1);
                return (mb_strpos(self::C, $first) !== false) && ($second === $vowel);
            }
        }
        
        return false;
    }
    
    /**
     * Проверка совместимости правого контекста
     */
    private static function compatibleRight(string $input, string $rule): bool
    {
        // Проверка списка вариантов
        $options = explode(',', $rule);
        if (in_array($input, $options, true)) {
            return true;
        }
        
        // Проверка специальных шаблонов
        if ($rule === 'B') {
            // Твердая гласная
            return mb_strpos(self::B, $input) !== false;
        }
        
        if ($rule === 'F') {
            // Мягкая гласная
            return mb_strpos(self::F, $input) !== false;
        }
        
        return false;
    }
    
    /**
     * Проверка твердости/мягкости слова
     * true - твердое, false - мягкое
     */
    public static function consistency(string $word): bool
    {
        $back = 'аӑуыо';
        $front = 'еӗиÿэюя';
        $special = 'ьъ';
        
        for ($i = mb_strlen($word) - 1; $i >= 0; $i--) {
            $char = mb_substr($word, $i, 1);
            
            // Пропускаем спецсимволы
            if (mb_strpos($special, $char) !== false) {
                continue;
            }
            
            if (mb_strpos($back, $char) !== false) {
                return true; // твердое
            }
            
            if (mb_strpos($front, $char) !== false) {
                return false; // мягкое
            }
        }
        
        // Если не нашли гласных, предполагаем твердое
        return true;
    }
    
    /**
     * Проверка, оканчивается ли слово на согласную
     */
    public static function soglasnaya(string $word): bool
    {
        if ($word === '') {
            return false;
        }
        
        $lastChar = mb_substr($word, -1);
        $special = 'ьъ';
        
        // Если последний символ - согласная или спецсимвол
        if (mb_strpos(self::C, $lastChar) !== false || 
            mb_strpos($special, $lastChar) !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Получить все правила (для отладки)
     */
    public static function getRules(): array
    {
        return self::RULES;
    }
}