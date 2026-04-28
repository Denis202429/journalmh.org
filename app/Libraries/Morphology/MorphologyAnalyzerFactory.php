<?php

namespace App\Libraries\Morphology;

/**
 * Фабрика для создания морфологических анализаторов
 */
class MorphologyAnalyzerFactory
{
    /**
     * Создает анализатор для указанного языка
     *
     * @param string $language Язык
     * @return MorphologyAnalyzerInterface Анализатор
     * @throws \Exception Если анализатор для указанного языка не найден
     */
    public static function create(string $language): MorphologyAnalyzerInterface
    {
        switch (strtolower($language)) {
            case 'chuvash':
                return new ChuvashMorphologyAnalyzer();
            default:
                throw new \Exception("Анализатор для языка '{$language}' не найден");
        }
    }
} 