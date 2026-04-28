<?php

namespace App\Libraries\Morphology;

class AffInfo
{
    public $AffixText;      // Текст аффикса
    public $AffixType;      // Тип аффикса (префикс/суффикс)
    public $AffixLevel;     // Уровень аффикса
    public $AffixMeaning;   // Значение аффикса
    public $AffixPartOfSpeech; // Часть речи
    public $AffixPartOfSpeechType; // Тип части речи (OnlyGlagol, NotGlagol, Any)

    public function __construct(
        string $affixText = '',
        string $affixType = '',
        int $affixLevel = 0,
        string $affixMeaning = '',
        string $affixPartOfSpeech = '',
        string $affixPartOfSpeechType = 'Any'
    ) {
        $this->AffixText = $affixText;
        $this->AffixType = $affixType;
        $this->AffixLevel = $affixLevel;
        $this->AffixMeaning = $affixMeaning;
        $this->AffixPartOfSpeech = $affixPartOfSpeech;
        $this->AffixPartOfSpeechType = $affixPartOfSpeechType;
    }
} 