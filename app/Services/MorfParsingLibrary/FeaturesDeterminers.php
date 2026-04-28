<?php

namespace App\Services\MorfParsingLibrary;
use App\Services\MorfParsingLibrary\MorfParser as A;
class FeaturesDeterminers
{
    #region Settings
    #region NOUN
    // FACE
    private $first_susch = ["ӑм", "ӗм", "ам", "ем", "м"];
    private $second_susch = ["ÿ", "у", "ÿн", "ун", "ӑр", "ӗр"];
    private $third_susch = ["и", "ин", "ӗ"];

    // PADEZHs
    private $rod = ["ӑн", "ӗн", "ин", "ÿн", "ун", "н", "сен"];
    private $dat = ["на", "не", "а", "е"];
    private $mest = ["ра", "ре", "та", "те", "че", "ти", "ри"];
    private $isch = ["тан", "тен", "рен", "ран", "чен"];
    private $tvor = ["па", "пе", "пала", "пеле", "палан", "пелен"];
    private $lish = ["сӑр", "сӗр"];
    private $prich_celevoy = ["шӑн", "шӗн"];

    // CHISLO
    public $pluralnoun = ["сем", "сен"];
    #endregion

    #region VERB
    // FACE
    private $first_verb = ["ӑп", "ӗп", "п", "ап", "еп", "ӑм", "ӗм"];
    private $second_verb = ["ӑн", "ӗн", "ӑр", "ӗр", "ан", "ен"];
    private $third_verb = ["ать", "ет", "аҫ", "еҫ", "ӗ", "ччӗр", "ччӑр", "ҫ", "нӑ", "нӗ", "ть", "т", "ччӗ", "тӑр", "тӗр"];

    // CHISLO
    private $pl = ["ӑр", "ӗр", "аҫ", "еҫ", "ҫ", "ччӗр"];

    // VREMYA
    private $nast_verb = ["ат", "ет", "ать", "аҫ", "еҫ", "ть", "п", "ап", "еп", "мас", "мес", "ан", "ен"];
    private $prosh_verb = ["р", "ч", "ӑм", "ӗм", "атт", "етт", "сатт", "сетт", "сачч", "сечч", "атч", "етч", "ччӗ", "чӗ"];
    private $bud_verb = ["ӗ"];

    private $ambi_vremya_verb = ["ӑп", "ӗп", "ӑн", "ӗн", "ӑр", "ӗр"];
    private $unknown_vremya = ["ма", "ме", "иччен"];

    // NEGATIVE
    private $negative = ["мас", "мес", "маҫ", "меҫ", "м", "сӑр", "сӗр"];

    // INFINITIV
    private $infinitiv = ["ма", "ме", "машкӑн", "мешкӗн"];
    #endregion

    #region PRONOUN
    // Pronoun logic could be added here
    #endregion

    #region CHISLITELNOE
    // FACE
    private $first_chisl = ["сӑмӑр", "сӗмӗр"];
    private $second_chisl = ["сӑр", "сӗр"];
    #endregion

    #region PRICHASTIE
    // 
    private $prich_mas = ["ас", "ес", "асшӑн", "есшӗн", "нӑ", "нӗ", "ан", "ен", "малла", "мелле"];

    // VREMYA
    private $nast_prich = ["акан", "екен", "аканн", "екенн", "ан", "ен"];
    private $bud_prich = ["ас", "ес", "асс", "есс", "асшӑн", "есшӗн"];
    private $prosh_prich = ["нӑ", "нӗ", "м", "н"];

    public $prosh_forall = ["ччӗ", "чӗ"];
    #endregion
    #endregion

    #region FACE
    // noun
    public function DetermineFaceOfSusch($word, $aff_massiv)
    {
        $sogl = ContextRules::Soglasnaya($word); // последняя буква согл или нет
        $consistent = ContextRules::Consistency($word); // твердость/мягкость слова
        $padezh = $this->DeterminePadezhOfSusch($word, $aff_massiv);

        if ($sogl) {
            switch ($padezh) {
                case Constants::ROD_P:
                    switch ($aff_massiv[0]) {
                        case "ӑн":
                            return Constants::FACE1;
                        case "ÿн":
                        case "ун":
                            return Constants::FACE2;
                        case "ӗн":
                            if ($consistent) return Constants::FACE3;
                            return "1,3е";
                    }
                    break;
                case Constants::DAT_P:
                    switch ($aff_massiv[0]) {
                        case "а":
                        case "е":
                            return Constants::FACE1;
                        case "на":
                            return Constants::FACE2;
                        case "не":
                            if ($consistent) return Constants::FACE3;
                            return "2,3е";
                    }
                    break;
                case Constants::MEST_P:
                case Constants::ISCH_P:
                    switch ($aff_massiv[0]) {
                        case "та":
                        case "те":
                        case "тан":
                        case "тен":
                        case "ра":
                        case "ре":
                        case "ран":
                        case "рен":
                            return Constants::FACE1;
                        case "ÿн":
                        case "ун":
                            return Constants::FACE2;
                        case "ӗн":
                            return Constants::FACE3;
                    }
                    break;
            }
        }

        if (array_intersect($aff_massiv, $this->first_susch)) return Constants::FACE1;
        if (array_intersect($aff_massiv, $this->second_susch)) return Constants::FACE2;
        if (array_intersect($aff_massiv, $this->third_susch)) return Constants::FACE3;

        return Constants::FACE1;
    }

    // chislit-e
    public function DetermineFaceOfChislitelnoe($word, $aff_massiv)
    {
        if (array_intersect($aff_massiv, $this->first_chisl)) return Constants::FACE1;
        if (array_intersect($aff_massiv, $this->second_chisl)) return Constants::FACE2;
        if (in_array("ӗ", $aff_massiv)) return Constants::FACE3;

        return Constants::FACE1;
    }

    // verb
    public function DetermineFaceOfGlagol($word, $aff_massiv)
    {
        if (in_array("м", $aff_massiv) && (in_array("ан", $aff_massiv) || in_array("ен", $aff_massiv))) return Constants::UNKNOWN; // нужно лучше
        if (array_intersect($aff_massiv, $this->first_verb)) return Constants::FACE1;
        if (array_intersect($aff_massiv, $this->second_verb)) return Constants::FACE2;
        if (array_intersect($aff_massiv, $this->third_verb)) return Constants::FACE3;
        return Constants::UNKNOWN;
    }

    // pronoun
    public function DetermineFaceOfPronoun($word, $aff_massiv)
    {
        for ($i = 0; $i < count(Helper::$pron_defaults); $i++) {
            $temp_mas = explode(',', Helper::$pron_defaults[$i]);
            if ($word == $temp_mas[0]) {
                return $temp_mas[3];
            }
        }
        return Constants::FACE3;
    }

    // prichastie
    public function DetermineFaceOfPrichastie($word, $aff_massiv)
    {
        if (array_intersect($aff_massiv, $this->prich_mas)) return Constants::UNKNOWN;

        return $this->DetermineFaceOfSusch($word, $aff_massiv);
    }
    #endregion

    #region PADEZH
    // noun

    #region PADEZH

    // noun
    public function DeterminePadezhOfSusch(string $word, array $aff_massiv): string
    {
        if (in_array("ӗн", $aff_massiv)) {
            if (in_array("че", $aff_massiv)) return Constants::MEST_P;
            if (in_array("чи", $aff_massiv)) return Constants::MEST_P;
            if (in_array("чен", $aff_massiv)) return Constants::ROD_P;
            return Constants::ROD_P;
        }

        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->rod);
        })) return Constants::ROD_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->dat);
        })) return Constants::DAT_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->mest);
        })) return Constants::MEST_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->isch);
        })) return Constants::ISCH_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->tvor);
        })) return Constants::TVOR_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->lish);
        })) return Constants::LISH_P;
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->prich_celevoy);
        })) return Constants::PR_CEL_P;

        return Constants::OSN_P;
    }

    // participle
    public function DeterminePadezhOfPrichastie(string $word, array $aff_massiv): string
    {
        if (array_filter($aff_massiv, function ($el) {
            return in_array($el, $this->prich_mas);
        })) return Constants::UNKNOWN;

        return $this->DeterminePadezhOfSusch($word, $aff_massiv);
    }
    #endregion


    #region CHISLO

    //noun
    function determinePluralOfSusch($word, $aff_massiv)
    {
        if (array_intersect($aff_massiv, self::$pluralnoun)) {
            return Constants::MN_CHISLO;
        } else {
            return Constants::ED_CHISLO;
        }
    }

    //мест
    function determinePluralOfPron($word, $aff_massiv)
    {
        foreach (Helper::$pron_defaults as $pron_default) {
            $temp_mas = array_filter(explode(',', $pron_default));
            if ($word === $temp_mas[0]) {
                return $temp_mas[1];
            }
        }
        return self::determinePluralOfSusch($word, $aff_massiv);
    }

    //verb
    function determinePluralOfVerb($word, $aff_massiv)
    {
        if (in_array('м', $aff_massiv) && (in_array('ан', $aff_massiv) || in_array('ен', $aff_massiv))) {
            return Constants::UNKNOWN; // нужно улучшить логику
        }
        if (in_array(end($aff_massiv), self::$unknown_vremya)) {
            return Constants::UNKNOWN;
        }
        if (array_intersect($aff_massiv, self::$pl)) {
            return Constants::MN_CHISLO;
        } else {
            return Constants::ED_CHISLO;
        }
    }

    //prichastie
    function determinePluralOfPrichastie($word, $aff_massiv)
    {
        if (array_intersect($aff_massiv, self::$prich_mas)) {
            return Constants::UNKNOWN;
        }

        return self::determinePluralOfSusch($word, $aff_massiv);
    }

    #endregion

    #region VREMYA

    //verb
    function determineVremyaOfGlagol($word, $aff_massiv)
    {
        $time = Constants::UNKNOWN;

        if (in_array("м", $aff_massiv) && (in_array("ан", $aff_massiv) || in_array("ен", $aff_massiv))) {
            return Constants::PROSH_V; // нужно улучшить логику
        }

        if (array_intersect($aff_massiv, self::$prosh_verb)) {
            return Constants::PROSH_V;
        }
        if (array_intersect($aff_massiv, self::$nast_verb)) {
            return Constants::NAST_V;
        }
        if (array_intersect($aff_massiv, self::$bud_verb)) {
            return Constants::BUD_V;
        }

        if (array_intersect($aff_massiv, self::$ambi_vremya_verb)) {
            if (in_array("т", $aff_massiv)) {
                $time = Constants::NAST_V;
            } else {
                $time = Constants::BUD_V;
            }
        }

        return $time;
    }

    //причастие
    function determineVremyaOfPrichastie($word, $aff_massiv)
    {
        $time = Constants::UNKNOWN;

        if (array_intersect($aff_massiv, self::$prosh_prich)) {
            return Constants::PROSH_V;
        }
        if (array_intersect($aff_massiv, self::$nast_prich)) {
            return Constants::NAST_V;
        }
        if (array_intersect($aff_massiv, self::$bud_prich)) {
            return Constants::BUD_V;
        }

        return $time;
    }

    //остальные
    function determineVremyaOfOstalnoe($word, $aff_massiv)
    {
        $time = Constants::UNKNOWN;

        if (array_intersect($aff_massiv, self::$prosh_forall)) {
            return Constants::PROSH_V;
        }

        return $time;
    }

    #endregion

    #region NEGATIVE
    //verb
    function determineNegativeOfGlagol($word, $aff_massiv)
    {
        $res = Constants::POSITIVE;
        if (array_intersect($aff_massiv, self::$negative)) {
            return Constants::NEGATIVE;
        }
        return $res;
    }
    #endregion

    #region INFINITIV
    function determineInfinitivOfGlagol($word, $aff_massiv)
    {
        if (in_array(end($aff_massiv), self::$infinitiv)) {
            return Constants::INF;
        }
        return Constants::NOTINF;
    }
    #endregion

    #region AFFINFO
    function determineAffixInfo($word, $chastrechi, $aff_massiv)
    {
        $glagol_start = $notglagol_start = $prichastie_start = 0;
        $glagol_end = $notglagol_end = $prichastie_end = 0;

        $x = "";

        // Определение начальных и конечных индексов
        for ($i = 0; $i < count(A::$AffInfo); $i++) {
            if (A::$AffInfo[$i] == "[NOTGLAGOL]") $notglagol_start = $i;
            if (A::$AffInfo[$i] == "[GLAGOL]") $glagol_start = $i;
            if (A::$AffInfo[$i] == "[PRICHASTIE]") $prichastie_start = $i;

            if (A::$AffInfo[$i] == "[/NOTGLAGOL]") $notglagol_end = $i;
            if (A::$AffInfo[$i] == "[/GLAGOL]") $glagol_end = $i;
            if (A::$AffInfo[$i] == "[/PRICHASTIE]") $prichastie_end = $i;
        }

        // Определение диапазона для работы с аффиксами
        switch ($chastrechi) {
            case Constants::VERB:
            case Constants::DEEPRICHASTIE:
                $start = $glagol_start;
                $end = $glagol_end;
                break;
            case Constants::PRICHASTIE:
                $start = $prichastie_start;
                $end = $prichastie_end;
                break;
            default:
                $start = $notglagol_start;
                $end = $notglagol_end;
                break;
        }

        // Поиск информации об аффиксах
        for ($j = count($aff_massiv) - 1; $j >= 0; $j--) {
            $temp_end = $end - 1;
            for ($i = $temp_end; $i >= $start; $i--) {
                $line = explode(';', A::$AffInfo[$i]);
                $affs = explode('/', $line[0]);

                if (in_array($aff_massiv[$j], $affs)) {
                    $x = $aff_massiv[$j] . "-" . $line[1] . "\r\n" . $x;
                    $temp_end = $i;
                    break;
                }
            }
        }

        return $x;
    }
    #endregion






    #endregion
}
