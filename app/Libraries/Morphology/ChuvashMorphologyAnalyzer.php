<?php

namespace App\Libraries\Morphology;

/**
 * Класс для морфологического анализа чувашского языка
 */
class ChuvashMorphologyAnalyzer implements MorphologyAnalyzerInterface
{
    /**
     * Парсер морфологии
     *
     * @var MorfParser
     */
    private $parser;
    
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->parser = new MorfParser();
    }
    
    /**
     * Анализирует слово
     *
     * @param string $word Слово для анализа
     * @return array Результаты анализа
     */
    public function analyze(string $word): array
    {
        return $this->parser->analyze($word);
    }
    
    /**
     * Проверяет префиксы слова
     *
     * @param string $word Слово для анализа
     * @return array Список найденных префиксов
     */
    public function checkPrefixes(string $word): array
    {
        return $this->parser->checkPrefixes($word);
    }
} 