<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;
use Exception;

class ParallelCorpusImportService
{
    /**
     * Обрабатывает Excel файл и извлекает параллельные тексты в JSON формате
     * 
     * @param string $filePath Путь к файлу
     * @param int $maxRows Максимальное количество строк для обработки (0 = без ограничений)
     * @return array ['content' => string (JSON), 'stats' => array]
     * @throws Exception
     */
    public function processExcelFile(string $filePath, int $maxRows = 0): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Получаем данные с правильной обработкой пустых ячеек
            $allData = $sheet->toArray(null, true, true, false);
            
            // Валидация структуры файла
            $this->validateFileStructure($allData);
            
            // Обработка данных
            return $this->processData($allData, $maxRows);
            
        } catch (Exception $e) {
            Log::error('Ошибка при обработке Excel файла', [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Валидация структуры файла
     */
    protected function validateFileStructure(array $data): void
    {
        if (empty($data)) {
            throw new Exception('Файл пуст или не содержит данных.');
        }
        
        // Проверяем, что есть хотя бы одна строка с данными в обоих столбцах
        $hasData = false;
        foreach ($data as $row) {
            if (isset($row[0]) && isset($row[1]) && 
                trim((string)$row[0]) !== '' && trim((string)$row[1]) !== '') {
                $hasData = true;
                break;
            }
        }
        
        if (!$hasData) {
            throw new Exception('Файл не содержит параллельных текстов в двух столбцах.');
        }
    }
    
    /**
     * Обрабатывает данные из массива и возвращает JSON формат
     * Формат: [["оригинал1", "перевод1"], ["оригинал2", "перевод2"], ...]
     */
    protected function processData(array $allData, int $maxRows = 0): array
    {
        $pairs = [];
        $processedRows = 0;
        $skippedRows = 0;
        $emptyRows = 0;
        
        foreach ($allData as $row) {
            // Ограничение на количество строк
            if ($maxRows > 0 && $processedRows >= $maxRows) {
                break;
            }
            
            // Пропускаем полностью пустые строки
            if (empty($row) || (!isset($row[0]) && !isset($row[1]))) {
                $emptyRows++;
                continue;
            }
            
            // Получаем и очищаем значения
            $original = isset($row[0]) && $row[0] !== null && $row[0] !== '' 
                ? trim((string)$row[0]) : '';
            $translation = isset($row[1]) && $row[1] !== null && $row[1] !== '' 
                ? trim((string)$row[1]) : '';
            
            // Пропускаем строки, где оба столбца пустые
            if ($original === '' && $translation === '') {
                $skippedRows++;
                continue;
            }
            
            // Добавляем пару в массив (даже если один из столбцов пустой)
            $pairs[] = [$original, $translation];
            $processedRows++;
        }
        
        // Проверка результата
        if (empty($pairs)) {
            throw new Exception('Не удалось извлечь данные из файла.');
        }
        
        // Проверяем, что есть хотя бы одна пара с обоими значениями
        $hasFullPairs = false;
        foreach ($pairs as $pair) {
            if ($pair[0] !== '' && $pair[1] !== '') {
                $hasFullPairs = true;
                break;
            }
        }
        
        if (!$hasFullPairs) {
            throw new Exception('Файл не содержит полных параллельных текстов (оба столбца должны быть заполнены).');
        }
        
        // Преобразуем в JSON
        $jsonContent = json_encode($pairs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($jsonContent === false) {
            throw new Exception('Ошибка при преобразовании данных в JSON: ' . json_last_error_msg());
        }
        
        return [
            'content' => $jsonContent,
            'stats' => [
                'processed_rows' => $processedRows,
                'skipped_rows' => $skippedRows,
                'empty_rows' => $emptyRows,
                'total_rows' => count($allData),
                'pairs_count' => count($pairs)
            ]
        ];
    }
    
    /**
     * Валидация размера файла перед обработкой
     */
    public function validateFileSize(string $filePath, int $maxSizeMB = 10): void
    {
        $fileSize = filesize($filePath);
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        
        if ($fileSize > $maxSizeBytes) {
            throw new Exception("Размер файла превышает допустимый лимит ({$maxSizeMB} МБ).");
        }
    }
}
