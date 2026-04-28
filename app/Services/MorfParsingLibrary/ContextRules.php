<?php
namespace App\Services\MorfParsingLibrary;


class ContextRules {
    // C-согласная
    // F-мягкая гласная
    // B-твердая гласная
    public static $B = "аӑуы";
    public static $C = "бвгджзйклмнпрсҫтфхцчшщ";
    public static $F = "еӗÿя";
    public static $WORD;
    public static $AFFUNIT;

    // поиск правила обработки контекстов
    public static function FindTemplate($word, $AffUnit, $rules, &$syllables, &$chastrechi) {
        self::$WORD = $word;
        self::$AFFUNIT = $AffUnit;

        $lcon = mb_strlen($word) > 1 ? mb_substr($word, mb_strlen($word) - 2) : $word; // получение лев контекста
        $affunit_parts = explode('|', $AffUnit);
        $rcon = $affunit_parts[0]; // получение пр контекста

        foreach ($rules as $line) {
            if (strpos($line, "//") === 0) continue; // пропуск комментариев
            $rule = explode(';', $line); // читаем строку с правилом
            if (self::CheckLeftContext($lcon, $rule[0]) && self::CheckRightContext($rcon, $rule[1])) {
                // согласно правилу
                self::WordModify($word, $rule[2]); // слово
                $syllables = self::ExtractSyllables($rule[3]); // символы восстановления
                $chastrechi = $rule[4]; //

                echo "Правило контекста: [" . self::$WORD . " " . self::$AFFUNIT . " | " . $line . "]<br>";
                return;
            }
        }
        echo "No Context Rule<br>";
    }

    // символы по умолчанию
    public static function DefaultSyllables($AffUnit) {
        switch (AffixInfoProvider::TypeOfRecovery($AffUnit)) {
            case AffixInfoProvider::RecoveryMode['With']:  // Обращение к элементу массива-константы
                return ["а", "е", "ӗ", "я"];
            case AffixInfoProvider::RecoveryMode['Without']:
                return [""];
            case AffixInfoProvider::RecoveryMode['Both']:
                return ["а", "е", "ӗ", "я", ""];
        }
        return [];
    }

    // проверка подходимости части речи
    public static function CheckChastRechi($chastrechi, $context_chastrechi)
     {
        if ($context_chastrechi === null) return false;
        switch ($context_chastrechi) {
            case "":
                return true;
            default:
                $m = explode(',', $context_chastrechi);
                foreach ($m as $item) {
                    $turned = Helper::convertPartOfSpeech($item);
                    if ($turned == $chastrechi) {
                        return true;
                    }
                }
                break;
        }
        return false;
    }

    // формирование массива символов
    private static function ExtractSyllables($p) {
        if ($p == "") return [""];
        return explode(',', $p);
    }

    // редактирование аффикса
    private static function AffixModify($AffUnit, $p) {
        throw new \LogicException("Not implemented");
    }

    // редактирование слова согласно правилу
    private static function WordModify(&$word, $p) {
        if ($p == "") return;
        switch ($p[0]) {
            case '-':
                $length = (int)$p[1];
                $word = mb_substr($word, 0, mb_strlen($word) - $length);
                break;
            default:
                break;
        }
    }

    // проверка лев контекста
    private static function CheckLeftContext($lcon_input, $lcon_rule) {
        if ($lcon_rule == "") return true;
        if ($lcon_input != $lcon_rule) {
            if (!self::CompatibleLeft($lcon_input, $lcon_rule)) {
                return false;
            }
        }
        return true;
    }

    // проверка пр контекста
    private static function CheckRightContext($rcon_input, $rcon_rule) {
        if ($rcon_rule == "") return true;
        if ($rcon_input != $rcon_rule) {
            if (!self::CompatibleRight($rcon_input, $rcon_rule)) {
                return false;
            }
        }
        return true;
    }

    // проверка совместимости лев
    private static function CompatibleLeft($input, $rule) {
        $mas = explode(',', $rule);
        foreach ($mas as $item) {
            if ($input == $item) {
                return true;
            }
        }

        $context = "";
        if (mb_strlen($input) > 1) {
            if (strpos(self::$C, $input[0]) !== false) {
                if ($input[0] == $input[1]) {
                    $context = "2C";
                } else {
                    $context = "C" . $input[1];
                }
            }
        }

        return $context == $rule;
    }

    // проверка совместимости прав
    private static function CompatibleRight($input, $rule) {
        $mas = explode(',', $rule);
        foreach ($mas as $item) {
            if ($input == $item) {
                return true;
            }
        }

        $context = "";
        if (strpos(self::$F, $input[0]) !== false) {
            $context = "F";
        } elseif (strpos(self::$B, $input[0]) !== false) {
            $context = "B";
        }

        return $context == $rule;
    }

    // проверка тверд/мягкость слова& 
    // true - тверд
    public static function Consistency($word) {
        $x = $word[mb_strlen($word) - 1];

        $Back = "аӑуыо";
        $Front = "еӗиÿэюя";
        $special = "ьъ";

        if (strpos($special, $x) !== false) return false;

        for ($i = mb_strlen($word) - 1; $i >= 0; $i--) {
            if (strpos($Back, $word[$i]) !== false) {
                return true; // твердыня
            }

            if (strpos($Front, $word[$i]) !== false) {
                return false; // мягкота
            }
        }
     
        throw new \LogicException("WTF man?");
    }

    // оканчив. на согл. или нет
    public static function Soglasnaya($word) {
        $sogl = false;
        $S = "бвгджзйклмнпрсҫтфхцчшщ";
        $special = "ьъ";
        $x = $word[mb_strlen($word) - 1];

        $sogl = (strpos($S, $x) !== false || strpos($special, $x) !== false); // оканчивается ли слово на согл
        return $sogl;
    }

    // проверка звонкости согласной буквы
    public static function Zvonko($character) {
        $z = "гджзйлмнр";
        // $nez = "бвкптфхцчшщ";

        return strpos($z, $character) !== false;
    }
}