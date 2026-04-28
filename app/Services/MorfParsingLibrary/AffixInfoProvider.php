<?php
namespace App\Services\MorfParsingLibrary;


class AffixInfoProvider
{
    // Перечисления для указания типа поиска и типа восстановления
    const SearchMode = [
        'OnlyGlagol' => 0,
        'NotGlagol' => 1,
        'Any' => 2,
        'Prichastie' => 3,
        'DeePrichastie' => 4,
        'Prilagatelnoe' => 5,
        'NotNarechie' => 6,
        'NotPril' => 7,
        'NotNarechieGlagol' => 8,
        'SuschGlagol' => 9,
        'Chislitelnoe' => 10,
        'deenoun' => 11,
        'AbsolutelyAny' => 12,
    ];

    const RecoveryMode = [
        'With' => 0,
        'Without' => 1,
        'Both' => 2,
    ];

    // Метод присоединения нового аффикса к юниту аффиксов
    public static function addAffixToUnit($aff, $affUnit)
    {
        if (strlen($affUnit) > 0) {
            if ($affUnit[strlen($affUnit) - 1] == '|') {
                $affUnit = substr($affUnit, 0, -1);
            }
        }

        if (strpos($aff, '|') === 0) {
            return $aff . $affUnit;
        }

        return '|' . $aff . $affUnit;
    }

    // Метод проверки "подходимости" аффикса к аффиксу
    public static function affixesSuitable($aff, $affUnit)
    {
        // Типы
        $affType = '';
        $unitType = '';
        $decision = false;

        // Уровень аффикса
        $affLevel = self::levelOfSingleAffix($aff, $affType);

        // Уровень юнита
        $unitLevel = self::levelOfAffUnit($affUnit, $unitType);

        if ($affLevel < $unitLevel) {
            if ($affType === $unitType || $affType === Constants::ANY || $unitType === Constants::ANY) {
                $decision = true;
            }
        }

        return $decision;
    }

    
    // Метод определения "уровня", типа единичного аффикса
    public static function levelOfSingleAffix($affixForCheck, &$singleAffType)
    {
        $singleAffType = Constants::ANY;
        $level = 888;

        $affMas = explode('|', $affixForCheck);
        switch ($affixForCheck) {
            case '':
                $level = 100;
                break;
            case '|':
                $level = 90;
                break;
            default:
                // Используйте правильное имя статической переменной с правильным регистром
                for ($i = count(MorfParser::$Affixes) - 1; $i >= 0; $i--) {
                    $affArr = preg_split('/[,\s]+/', MorfParser::$Affixes[$i]);
                    if (in_array($affMas[0], $affArr)) {
                        $singleAffType = end($affArr);
                        return $i;
                    }
                }
                break;
        }

        return $level;
    }

    // Метод определения "уровня", типа юнита аффиксов
    public static function levelOfAffUnit($affUnitForCheck, &$affUnitType)
    {
        $affUnitType = Constants::ANY;

        if ($affUnitForCheck === '') {
            return 100;
        }
        if ($affUnitForCheck === '|') {
            return 90;
        }

        $demo = [Constants::ONLYGLAGOL, Constants::NOTGLAGOL];
        $singleAffType = '';
        $affMas = explode('|', $affUnitForCheck);
        $level = self::levelOfSingleAffix($affMas[0], $singleAffType);

        foreach ($affMas as $aff) {
            self::levelOfSingleAffix($aff, $singleAffType);
            if (in_array($singleAffType, $demo)) {
                $affUnitType = $singleAffType;
                return $level;
            }
        }

        return $level;
    }

    // Метод определения типа поиска в словаре
    public static function typeOfSearch($affUnit)
    {
        $mode = self::SearchMode['Any'];
        $affType = '';
        $level = self::levelOfSingleAffix($affUnit, $affType);

        if ($level >= 50) {
            return $mode;
        }

        $aff = explode('|', $affUnit);
        foreach ($aff as $a) {
            self::levelOfSingleAffix($a, $affType);
            switch ($a) {
                case 'ни':
                case 'акан':
                case 'екен':
                    $mode = self::SearchMode['Prichastie'];
                    break;
                case 'са':
                case 'се':
                    $mode = self::SearchMode['DeePrichastie'];
                    break;
                case 'а':
                case 'е':
                    $mode = self::SearchMode['deenoun'];
                    break;
                case 'лӑ':
                case 'лӗ':
                    $mode = self::SearchMode['Prilagatelnoe'];
                    break;
                default:
                    break;
            }

            switch ($affType) {
                case Constants::ONLYGLAGOL:
                case Constants::NOTGLAGOL:
                    $mode = array_search($affType, self::SearchMode);
                    break;
                default:
                    break;
            }
        }

        return $mode;
    }

    // Метод определения типа восстановления корня
    public static function typeOfRecovery($affUnit)
    {
        $mode = self::RecoveryMode['Both'];
        $aff = explode('|', $affUnit)[0];

        switch ($aff) {
            case 'и':
            case 'у':
                $mode = self::RecoveryMode['With'];
                break;
            case 'м':
            case 'а':
            case 'е':
                $mode = self::RecoveryMode['Without'];
                break;
        }

        return $mode;
    }

    // Метод определения искомой части речи
    public static function patternPicker($affUnit)
    {
        $sm = self::typeOfSearch($affUnit);

        $pattern = '';
        switch ($sm) {
            case self::SearchMode['OnlyGlagol']:
                $pattern = Constants::VERB . '|Same';
                break;
            case self::SearchMode['NotGlagol']:
                $pattern = Constants::NOUN . ',' . Constants::NUMERIC . ',' . Constants::ADJECTIVE . '|Same';
                break;
            case self::SearchMode['Any']:
                $pattern = Constants::NOUN . ',' . Constants::NUMERIC . ',' . Constants::VERB . '|Same';
                break;
        }

        return $pattern;
    }


    //обрезка "полных" аффиксов
    public static function CutItOut($AffUnit, $chast)
    {
        $affunit = array_filter(explode('|', $AffUnit)); // Разбиваем строку на массив и удаляем пустые элементы
        $finAffUnit = "";
        $separ = "|";

        foreach ($affunit as $aff) {
            switch ($aff) {
                case "ҫӑм":
                case "ҫӗм":
                    $finAffUnit .= $aff[0] . $separ . substr($aff, 1, 2) . $separ;
                    break;
                case "ятна":
                case "атна":
                case "етне":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . substr($aff, 2, 2) . $separ;
                    break;
                case "масӑр":
                case "месӗр":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . substr($aff, 2, 3) . $separ;
                    break;
                case "рӑм":
                case "рӗм":
                case "рӑн":
                case "рӗн":
                case "тӑм":
                case "тӗм":
                case "ман":
                case "мен":
                case "мӑп":
                case "мӗп":
                case "мап":
                case "меп":
                case "маҫ":
                case "меҫ":
                case "тӑп":
                case "тӗп":
                case "тӑн":
                case "тӗн":
                case "рӑр":
                case "рӗр":
                    $finAffUnit .= $aff[0] . $separ . substr($aff, 1, 2) . $separ;
                    break;
                case "аттӑм":
                case "еттӗм":
                case "ӑттӑм":
                case "ӗттӗм":
                    $finAffUnit .= substr($aff, 0, 3) . $separ . substr($aff, 3, 2) . $separ;
                    break;
                case "ттӑм":
                case "ттӗм":
                case "ттӑн":
                case "ттӗн":
                case "атӑп":
                case "етӗп":
                case "атӑн":
                case "етӗн":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . substr($aff, 2, 2) . $separ;
                    break;
                case "атчӗҫ":
                case "етчӗҫ":
                    $finAffUnit .= substr($aff, 0, 3) . $separ . substr($aff, 3, 1) . $separ . substr($aff, 4, 1) . $separ;
                    break;
                case "тчӗҫ":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . substr($aff, 2, 1) . $separ . substr($aff, 3, 1) . $separ;
                    break;
                case "аканни":
                case "екенни":
                case "яканни":
                    $finAffUnit .= substr($aff, 0, 5) . $separ . substr($aff, 5, 1) . $separ;
                    break;
                case "манни":
                case "менни":
                    $finAffUnit .= $aff[0] . $separ . substr($aff, 1, 3) . $separ . $aff[4] . $separ;
                    break;
                case "асси":
                case "есси":
                case "ясси":
                    $finAffUnit .= substr($aff, 0, 3) . $separ . $aff[3] . $separ;
                    break;
                case "ни":
                    $finAffUnit .= "н" . $separ . "и" . $separ;
                    break;
                case "малли":
                case "мелли":
                    $finAffUnit .= substr($aff, 0, 4) . $separ . "и" . $separ;
                    break;
                case "нӑҫем":
                case "нӗҫем":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . substr($aff, 2, 3) . $separ;
                    break;
                case "пӑр":
                case "пӗр":
                    $finAffUnit .= $aff[0] . $separ . substr($aff, 1, 2) . $separ;
                    break;
                case "сене":
                    $finAffUnit .= "сем" . $separ . "е" . $separ;
                    break;
                case "аҫҫӗ":
                case "еҫҫӗ":
                    $finAffUnit .= substr($aff, 0, 2) . $separ . "ҫ" . $separ . "ӗ" . $separ;
                    break;
                case "мест":
                    $finAffUnit .= "мес" . $separ . "т" . $separ;
                    break;
                case "масть":
                    $finAffUnit .= "мас" . $separ . "ть" . $separ;
                    break;
                case "рӗ":
                    $finAffUnit .= $aff[0] . $separ . "ӗ" . $separ;
                    break;
                case "рӗҫ":
                case "чӗҫ":
                    $finAffUnit .= $aff[0] . $separ . "ӗ" . $separ . "ҫ" . $separ;
                    break;
                case "мӗ":
                    $finAffUnit .= "м" . $separ . "ӗ" . $separ;
                    break;
                case "ӗҫ":
                    $finAffUnit .= "ӗ" . $separ . "ҫ" . $separ;
                    break;
                case "атчӗ":
                case "етчӗ":
                    $finAffUnit .= $aff[0] . "тч" . $separ . "ӗ" . $separ;
                    break;
                case "чӗ":
                    if ($chast == "глагол" && $affunit[0] == "чӗ") {
                        $finAffUnit .= "ч" . $separ . "ӗ" . $separ;
                    } else {
                        $finAffUnit .= $aff . $separ;
                    }
                    break;
                default:
                    $finAffUnit .= $aff . $separ;
                    break;
            }
        }

        $finAffUnit = rtrim($finAffUnit, $separ); // Удаляем последний символ разделителя
        return $finAffUnit;
    }





}