<?php

namespace App\Libraries\Morphology;

/**
 * Интерфейс для морфологического анализатора
 */
interface MorphologyAnalyzerInterface
{
    /**
     * Анализирует слово
     *
     * @param string $word Слово для анализа
     * @return array Результаты анализа
     */
    public function analyze(string $word): array;

    /**
     * Проверяет префиксы слова
     *
     * @param string $word Слово для анализа
     * @return array Список найденных префиксов
     */
    public function checkPrefixes(string $word): array;
} 