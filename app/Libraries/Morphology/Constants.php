<?php

namespace App\Libraries\Morphology;

/**
 * Класс констант для морфологического анализатора
 */
class Constants
{
    // Общие константы
    const NULL = "null";
    const UNKNOWN = "unknown";
    
    // Лица
    const FACE1 = "1е";
    const FACE2 = "2е";
    const FACE3 = "3е";
    
    // Падежи
    const OSN_P = "основной";
    const ROD_P = "родительный";
    const DAT_P = "дательный";
    const MEST_P = "местный";
    const ISCH_P = "исходный";
    const TVOR_P = "творительный";
    const LISH_P = "лишительный";
    const PR_CEL_P = "причинно-целевой";
    
    // Числа
    const ED_CHISLO = "единственное";
    const MN_CHISLO = "множественное";
    
    // Время
    const NAST_V = "настоящее";
    const PROSH_V = "прошлое";
    const BUD_V = "будущее";
    
    // Отрицательность
    const NEGATIVE = "отрицание";
    const POSITIVE = "неотрицание";
    
    // Инфинитив
    const INF = "инфинитив";
    const NOTINF = "неинфинитив";
    
    // Части речи
    const NOUN = "имя существительное";
    const PRONOUN = "местоимение";
    const VERB = "глагол";
    const ADJECTIVE = "прилагательное";
    const ADVERB = "наречие";
    const NUMERIC = "имя числительное";
    const PART = "частица";
    const CONJ = "союз";
    const DEEPRICHASTIE = "деепричастие";
    const PRICHASTIE = "причастие";
    const DEENOUN = "Dee/noun";
    
    // Тип поиска
    const ANY = "Any";
    const ONLYGLAGOL = "OnlyGlagol";
    const NOTGLAGOL = "NotGlagol";
    
    // Типы аффиксов
    const ENDING = 'ENDING';
    const SUFFIX = 'SUFFIX';
    const PREFIX = 'PREFIX';
} 