<?php

namespace App\Libraries\Morphology;

/**
 * Класс для морфологического анализа слов
 */
class MorfParser
{
    /**
     * Обработчик аффиксов
     *
     * @var ImprovedAffixHandler
     */
    private $iah;
    
    /**
     * Определитель характеристик
     *
     * @var FeaturesDeterminers
     */
    private $fd;
    
    /**
     * Пути к файлам
     */
    private $dictionary_path;
    private $affixes_path;
    private $rules_path;
    private $exceptions_path;
    private $affinfo_path;
    private $inputText_Path;
    private $outputText_Path;
    
    /**
     * Статические массивы
     */
    public static $Dictionary = [];
    public static $Affixes = [];
    public static $Rules = [];
    public static $Exceptions = [];
    public static $AffInfo = [];
    
    /**
     * Характеристики слова
     */
    private $word;
    private $variants = [];
    
    /**
     * Коллекция корней и аффиксов
     *
     * @var array
     */
    private $rootAffixesCollection = [];
    
    /**
     * Количество "значений" слова
     *
     * @var int
     */
    private $count = 0;
    
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->initializeComponents();
        
        // Пути к файлам
        $this->dictionary_path = storage_path('app/morphology/DICT.txt');
        $this->affixes_path = storage_path('app/morphology/Affixes.txt');
        $this->rules_path = storage_path('app/morphology/Rules.txt');
        $this->exceptions_path = storage_path('app/morphology/Exceptions.txt');
        $this->affinfo_path = storage_path('app/morphology/AffInfo.txt');
        $this->inputText_Path = storage_path('app/morphology/Input.txt');
        $this->outputText_Path = storage_path('app/morphology/Output.txt');
        
        echo "Пути к файлам:\n";
        echo "Словарь: {$this->dictionary_path}\n";
        echo "Аффиксы: {$this->affixes_path}\n";
        echo "Правила: {$this->rules_path}\n";
        echo "Исключения: {$this->exceptions_path}\n";
        echo "Информация об аффиксах: {$this->affinfo_path}\n";
        
        // Загрузка файлов
        if (file_exists($this->dictionary_path)) {
            self::$Dictionary = file($this->dictionary_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            echo "Словарь загружен, количество записей: " . count(self::$Dictionary) . "\n";
        } else {
            echo "Файл словаря не найден: {$this->dictionary_path}\n";
        }
        
        if (file_exists($this->affixes_path)) {
            $this->loadAffixes();
        } else {
            echo "Файл аффиксов не найден: {$this->affixes_path}\n";
        }
        
        if (file_exists($this->rules_path)) {
            self::$Rules = file($this->rules_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            echo "Правила загружены, количество записей: " . count(self::$Rules) . "\n";
        } else {
            echo "Файл правил не найден: {$this->rules_path}\n";
        }
        
        if (file_exists($this->exceptions_path)) {
            self::$Exceptions = file($this->exceptions_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            echo "Исключения загружены, количество записей: " . count(self::$Exceptions) . "\n";
        } else {
            echo "Файл исключений не найден: {$this->exceptions_path}\n";
        }
        
        if (file_exists($this->affinfo_path)) {
            self::$AffInfo = file($this->affinfo_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            echo "Информация об аффиксах загружена, количество записей: " . count(self::$AffInfo) . "\n";
        } else {
            echo "Файл информации об аффиксах не найден: {$this->affinfo_path}\n";
        }
        
        Helper::GetNumbersOfLines(self::$Dictionary);
        
        $this->fd = new FeaturesDeterminers();
    }
    
    /**
     * Инициализирует компоненты
     */
    private function initializeComponents(): void
    {
        $this->rootAffixesCollection = [];
        $this->variants = [];
        $this->count = 0;
    }
    
    /**
     * Обрабатывает текст из файла
     *
     * @param string $inputPath Путь к входному файлу
     * @return void
     */
    public function DealWithTextFromFile(string $inputPath): void
    {
        if (!file_exists($inputPath)) {
            throw new \Exception("Файл {$inputPath} не найден.");
        }
        
        $lines = file($inputPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if (empty($lines)) {
            throw new \Exception("Файл {$inputPath} пуст.");
        }
        
        foreach ($lines as $line) {
            $this->DealWithManualText($line);
        }
    }
    
    /**
     * Обрабатывает текст вручную
     *
     * @param string $inputText Входной текст
     * @return array Результаты анализа
     */
    public function DealWithManualText(string $inputText): array
    {
        $this->word = $inputText;
        $this->variants = [];
        $this->count = 0;
        
        $this->SearchInDictionaries($this->word);
        
        if ($this->count == 0) {
            $this->DetermineOnYourOwn($this->word);
        }
        
        $results = [];
        
        for ($i = 0; $i < $this->count; $i++) {
            $results[] = [
                'word' => $this->word,
                'chastRechi' => $this->variants[$i]['chastRechi'] ?? Constants::UNKNOWN,
                'pluralOrNot' => $this->variants[$i]['pluralOrNot'] ?? Constants::NULL,
                'vremya' => $this->variants[$i]['vremya'] ?? Constants::NULL,
                'padezh' => $this->variants[$i]['padezh'] ?? Constants::NULL,
                'root' => $this->variants[$i]['root'] ?? '',
                'affix' => $this->variants[$i]['affix'] ?? '',
                'face' => $this->variants[$i]['face'] ?? Constants::NULL,
                'negativ' => $this->variants[$i]['negativ'] ?? Constants::NULL,
                'infinitiv' => $this->variants[$i]['infinitiv'] ?? Constants::NULL,
                'affixInfo' => $this->variants[$i]['affixInfo'] ?? ''
            ];
        }
        
        return $results;
    }
    
    /**
     * Добавляет в коллекцию корней и аффиксов
     *
     * @param string $root Корень
     * @param string $affix Аффикс
     * @return void
     */
    public function AddtoRootAffixCollection(string $root, string $affix): void
    {
        $this->rootAffixesCollection[] = ['root' => $root, 'affix' => $affix];
    }
    
    /**
     * Определяет часть речи
     *
     * @param string $word Слово
     * @return array Части речи
     */
    private function ChastRechiDeterminer(string $word): array
    {
        // Проверяем, есть ли слово в словаре аффиксов
        foreach (self::$Affixes as $affix) {
            $parts = explode(',', $affix);
            if (count($parts) < 2) {
                continue;
            }
            
            $affixText = $parts[0];
            $affixType = $parts[1] ?? Constants::UNKNOWN;
            
            // Проверяем, заканчивается ли слово на этот аффикс
            if (mb_substr($word, -mb_strlen($affixText)) === $affixText) {
                return [$this->determinePartOfSpeechFromAffix($affixType)];
            }
        }
        
        // Если не нашли аффиксов, проверяем, есть ли слово в словаре
        foreach (self::$Dictionary as $entry) {
            $parts = explode(',', $entry);
            if (count($parts) >= 2 && $parts[0] === $word) {
                return [$parts[1] ?? Constants::UNKNOWN];
            }
        }
        
        // Если не нашли в словаре, возвращаем неизвестную часть речи
        return [Constants::UNKNOWN];
    }
    
    /**
     * Определяет число
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Число
     */
    private function PluralOrNotDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Если это глагол, проверяем индикаторы лица
        if ($chastrechi === Constants::VERB) {
            // Индикаторы множественного числа для глаголов
            $pluralIndicators = ['тпӑр', 'тпӗр', 'тӑр', 'тӗр'];
            
            foreach ($pluralIndicators as $indicator) {
                if (strpos($affixes, $indicator) !== false) {
                    return Constants::MN_CHISLO;
                }
            }
            
            // Если есть индикаторы единственного числа
            $singularIndicators = ['ӑп', 'ӗп', 'п', 'ап', 'еп', 'ӑм', 'ӗм', 'тӑп', 'тӗп'];
            foreach ($singularIndicators as $indicator) {
                if (strpos($affixes, $indicator) !== false) {
                    return Constants::ED_CHISLO;
                }
            }
        }
        
        // Для существительных проверяем суффиксы множественного числа
        if (strpos($affixes, '-сем') !== false || strpos($affixes, '-семӗ') !== false) {
            return Constants::MN_CHISLO;
        }
        
        // По умолчанию - единственное число
        return Constants::ED_CHISLO;
    }
    
    /**
     * Определяет время
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Время
     */
    private function VremyaDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Если это не глагол, время не определяется
        if ($chastrechi !== Constants::VERB) {
            return Constants::NULL;
        }

        echo "Determining time for word: " . $word . "\n";
        echo "Affixes: " . $affixes . "\n";

        // Разбиваем строку аффиксов на массив
        $affixArray = explode('|', $affixes);

        // Проверяем прошедшее время
        if (in_array('ат', $affixArray) || in_array('ет', $affixArray)) {
            echo "Found past time indicators\n";
            return Constants::PROSH_V;
        }

        // Проверяем будущее время
        if (in_array('аҫ', $affixArray) || in_array('еҫ', $affixArray)) {
            echo "Found future time indicators\n";
            return Constants::BUD_V;
        }

        // Проверяем настоящее время
        if (in_array('ать', $affixArray) || in_array('еть', $affixArray)) {
            echo "Found present time indicators\n";
            return Constants::NAST_V;
        }

        echo "No time indicators found\n";
        return Constants::NULL;
    }
    
    /**
     * Определяет падеж
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Падеж
     */
    private function PadezhDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Если это не существительное, падеж не определяется
        if ($chastrechi !== Constants::NOUN) {
            return Constants::NULL;
        }
        
        // Проверяем, есть ли в аффиксах показатель падежа
        if (strpos($affixes, '-ӑн') !== false || strpos($affixes, '-ӗн') !== false) {
            return Constants::ROD_P;
        } elseif (strpos($affixes, '-а') !== false || strpos($affixes, '-е') !== false) {
            return Constants::DAT_P;
        } elseif (strpos($affixes, '-ра') !== false || strpos($affixes, '-ре') !== false) {
            return Constants::MEST_P;
        } elseif (strpos($affixes, '-ран') !== false || strpos($affixes, '-рен') !== false) {
            return Constants::ISCH_P;
        } elseif (strpos($affixes, '-па') !== false || strpos($affixes, '-пе') !== false) {
            return Constants::TVOR_P;
        } elseif (strpos($affixes, '-сӑр') !== false || strpos($affixes, '-сӗр') !== false) {
            return Constants::LISH_P;
        } elseif (strpos($affixes, '-шӑн') !== false || strpos($affixes, '-шӗн') !== false) {
            return Constants::PR_CEL_P;
        } else {
            return Constants::OSN_P;
        }
    }
    
    /**
     * Определяет лицо
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param array $affixes Аффиксы
     * @return string Лицо
     */
    private function FaceDeterminer(string $word, string $chastrechi, array $affixes): string
    {
        // Если это не глагол, возвращаем null
        if ($chastrechi !== Constants::VERB) {
            return Constants::NULL;
        }

        echo "Determining face for word: " . $word . "\n";
        echo "Affixes: " . implode('|', $affixes) . "\n";

        // Проверяем первое лицо
        if (in_array('ӑр', $affixes) || in_array('ӗр', $affixes)) {
            if (in_array('п', $affixes) || in_array('т', $affixes)) {
                echo "Found first person plural indicators\n";
                return Constants::FACE1;
            }
        }

        // Проверяем первое лицо единственного числа
        if (in_array('ӑп', $affixes) || in_array('ӗп', $affixes) || 
            in_array('ап', $affixes) || in_array('еп', $affixes)) {
            echo "Found first person singular indicators\n";
            return Constants::FACE1;
        }

        // Проверяем второе лицо
        if (in_array('ӑн', $affixes) || in_array('ӗн', $affixes) ||
            in_array('ӑр', $affixes) || in_array('ӗр', $affixes)) {
            echo "Found second person indicators\n";
            return Constants::FACE2;
        }

        // Проверяем третье лицо
        if (in_array('ать', $affixes) || in_array('еть', $affixes) ||
            in_array('ӗ', $affixes) || in_array('ҫ', $affixes)) {
            echo "Found third person indicators\n";
            return Constants::FACE3;
        }

        echo "No face indicators found\n";
        return Constants::NULL;
    }
    
    /**
     * Определяет отрицательность
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Отрицательность
     */
    private function NegativDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Проверяем, есть ли в аффиксах показатель отрицательности
        if (strpos($affixes, '-мар') !== false || strpos($affixes, '-мер') !== false) {
            return Constants::NEGATIVE;
        }
        
        // По умолчанию - положительная форма
        return Constants::POSITIVE;
    }
    
    /**
     * Определяет инфинитив
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Инфинитив
     */
    private function InfinitivDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Если это не глагол, инфинитив не определяется
        if ($chastrechi !== Constants::VERB) {
            return Constants::NULL;
        }
        
        // Проверяем, есть ли в аффиксах показатель инфинитива
        if (strpos($affixes, '-ма') !== false || strpos($affixes, '-ме') !== false) {
            return Constants::INF;
        }
        
        // По умолчанию - не инфинитив
        return Constants::NOTINF;
    }
    
    /**
     * Определяет информацию об аффиксах
     *
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $affixes Аффиксы
     * @return string Информация об аффиксах
     */
    private function AffixInfoDeterminer(string $word, string $chastrechi, string $affixes): string
    {
        // Если аффиксов нет, возвращаем пустую строку
        if (empty($affixes)) {
            return '';
        }
        
        // Ищем информацию об аффиксах в словаре AffInfo
        foreach (self::$AffInfo as $info) {
            $parts = explode(',', $info);
            if (count($parts) >= 2 && $parts[0] === $affixes) {
                return $parts[1] ?? '';
            }
        }
        
        // Если не нашли информацию, возвращаем сами аффиксы
        return $affixes;
    }
    
    /**
     * Поиск слова в словарях
     * @param string $word Слово для поиска
     * @return bool Результат поиска
     */
    private function SearchInDictionaries(string $word): bool
    {
        echo "Поиск слова '{$word}' в словарях...\n";
        echo "Количество записей в словаре: " . count(self::$Dictionary) . "\n";
        echo "Количество записей в исключениях: " . count(self::$Exceptions) . "\n";
        
        // Поиск в исключениях
        foreach (self::$Exceptions as $exception) {
            if (strcasecmp($exception, $word) === 0) {
                echo "Слово '{$word}' найдено в исключениях\n";
                return true;
            }
        }
        
        // Поиск в словаре
        foreach (self::$Dictionary as $entry) {
            if (strcasecmp($entry, $word) === 0) {
                echo "Слово '{$word}' найдено в словаре\n";
                return true;
            }
        }
        
        echo "Слово '{$word}' не найдено в словарях\n";
        return false;
    }
    
    /**
     * Обрабатывает запись из словаря
     *
     * @param string $entry Запись из словаря
     * @return void
     */
    private function processDictionaryEntry(string $entry): void
    {
        $parts = explode(',', $entry);
        
        if (count($parts) < 2) {
            return;
        }
        
        $word = $parts[0];
        $chastrechi = $parts[1] ?? Constants::UNKNOWN;
        
        // Создаем новый вариант анализа
        $variant = [
            'chastRechi' => $chastrechi,
            'root' => $word,
            'affix' => '',
            'pluralOrNot' => $this->PluralOrNotDeterminer($word, $chastrechi, ''),
            'vremya' => $this->VremyaDeterminer($word, $chastrechi, ''),
            'padezh' => $this->PadezhDeterminer($word, $chastrechi, ''),
            'face' => $this->FaceDeterminer($word, $chastrechi, []),
            'negativ' => $this->NegativDeterminer($word, $chastrechi, ''),
            'infinitiv' => $this->InfinitivDeterminer($word, $chastrechi, ''),
            'affixInfo' => $this->AffixInfoDeterminer($word, $chastrechi, '')
        ];
        
        // Добавляем вариант в массив вариантов
        $this->variants[] = $variant;
        
        // Добавляем корень в коллекцию корней и аффиксов
        $this->AddtoRootAffixCollection($word, '');
        
        // Увеличиваем счетчик
        $this->count++;
    }
    
    /**
     * Определяет характеристики слова самостоятельно
     * @param string $word Слово для анализа
     * @return void
     */
    private function DetermineOnYourOwn(string $word): void
    {
        echo "Определение характеристик слова '{$word}' самостоятельно...\n";
        
        // Получаем длину слова
        $wordLength = mb_strlen($word);
        echo "Длина слова: {$wordLength}\n";
        
        // Массив для хранения всех возможных вариантов разбора
        $variants = [];
        
        // Сначала ищем самый длинный корень в словаре
        $longestRoot = '';
        $longestRootPartOfSpeech = Constants::UNKNOWN;
        
        foreach (self::$Dictionary as $entry) {
            $parts = explode(',', $entry);
            if (count($parts) < 2) {
                continue;
            }
            
            $dictionaryWord = $parts[0];
            $dictionaryPartOfSpeech = $parts[1] ?? Constants::UNKNOWN;
            
            // Проверяем, начинается ли слово с корня из словаря
            if (mb_strpos($word, $dictionaryWord) === 0 && mb_strlen($dictionaryWord) > mb_strlen($longestRoot)) {
                $longestRoot = $dictionaryWord;
                $longestRootPartOfSpeech = $dictionaryPartOfSpeech;
            }
        }
        
        // Если нашли корень в словаре
        if (!empty($longestRoot)) {
            // Получаем оставшуюся часть слова после корня
            $remainingWord = mb_substr($word, mb_strlen($longestRoot));
            
            // Проверяем суффиксы в оставшейся части
            $suffixes = $this->checkSuffixes($remainingWord);
            
            if (!empty($suffixes)) {
                $variants[] = [
                    'root' => $longestRoot,
                    'rootPartOfSpeech' => $longestRootPartOfSpeech,
                    'suffixes' => $suffixes
                ];
            }
        }
        
        // Если не нашли вариантов с корнем из словаря, пробуем найти корень, удаляя аффиксы
        if (empty($variants)) {
            $suffixes = $this->checkSuffixes($word);
            if (!empty($suffixes)) {
                $wordWithoutAffixes = $word;
                foreach ($suffixes as $suffix) {
                    if (mb_substr($wordWithoutAffixes, -mb_strlen($suffix)) === $suffix) {
                        $wordWithoutAffixes = mb_substr($wordWithoutAffixes, 0, -mb_strlen($suffix));
                    }
                }
                
                $variants[] = [
                    'root' => $wordWithoutAffixes,
                    'rootPartOfSpeech' => Constants::UNKNOWN,
                    'suffixes' => $suffixes
                ];
            }
        }
        
        // Если нашли варианты, сохраняем их
        if (!empty($variants)) {
            foreach ($variants as $variant) {
                $root = $variant['root'];
                $rootPartOfSpeech = $variant['rootPartOfSpeech'];
                $suffixes = $variant['suffixes'];
                
                echo "Найден корень: '$root' (часть речи: $rootPartOfSpeech)\n";
                echo "Найденные суффиксы: " . implode('|', $suffixes) . "\n";
                
                // Определяем часть речи на основе суффиксов
                $partOfSpeech = $this->determinePartOfSpeech($word, [], $suffixes, []);
                
                // Если часть речи не определена и найден корень, используем его часть речи
                if ($partOfSpeech === Constants::UNKNOWN && $rootPartOfSpeech !== Constants::UNKNOWN) {
                    $partOfSpeech = $rootPartOfSpeech;
                }
                
                echo "Определенная часть речи: {$partOfSpeech}\n";
                
                // Создаем новый вариант анализа
                $analysisVariant = [
                    'chastRechi' => $partOfSpeech,
                    'root' => $root,
                    'affix' => implode('|', $suffixes),
                    'pluralOrNot' => $this->PluralOrNotDeterminer($word, $partOfSpeech, implode('|', $suffixes)),
                    'vremya' => $this->VremyaDeterminer($word, $partOfSpeech, implode('|', $suffixes)),
                    'padezh' => $this->PadezhDeterminer($word, $partOfSpeech, implode('|', $suffixes)),
                    'face' => $this->FaceDeterminer($word, $partOfSpeech, $suffixes),
                    'negativ' => $this->NegativDeterminer($word, $partOfSpeech, implode('|', $suffixes)),
                    'infinitiv' => $this->InfinitivDeterminer($word, $partOfSpeech, implode('|', $suffixes)),
                    'affixInfo' => $this->AffixInfoDeterminer($word, $partOfSpeech, implode('|', $suffixes))
                ];
                
                // Добавляем вариант в массив вариантов
                $this->variants[] = $analysisVariant;
                
                // Добавляем корень в коллекцию корней и аффиксов
                $this->AddtoRootAffixCollection($root, implode('|', $suffixes));
                
                // Увеличиваем счетчик
                $this->count++;
            }
        }
    }
    
    /**
     * Проверка суффиксов слова
     * @param string $word Слово для проверки
     * @return array Найденные суффиксы
     */
    public function checkSuffixes(string $word): array
    {
        $suffixes = [];
        $wordLength = mb_strlen($word);
        
        echo "Checking suffixes for word: " . $word . "\n";
        
        // Сначала проверяем составные суффиксы, которые нужно разбить
        $compoundSuffixes = [
            'пӑр' => ['п', 'ӑр'],  // множественное число + первое лицо
            'пӗр' => ['п', 'ӗр'],  // множественное число + первое лицо
            'тӑр' => ['т', 'ӑр'],  // множественное число + первое лицо
            'тӗр' => ['т', 'ӗр'],  // множественное число + первое лицо
            'атӑп' => ['ат', 'ӑп'], // прошедшее время + первое лицо
            'етӗп' => ['ет', 'ӗп'], // прошедшее время + первое лицо
            'атпӑр' => ['ат', 'п', 'ӑр'], // прошедшее время + множественное число + первое лицо
            'етпӗр' => ['ет', 'п', 'ӗр']  // прошедшее время + множественное число + первое лицо
        ];
        
        // Проверяем окончание слова на составные суффиксы
        foreach ($compoundSuffixes as $compound => $parts) {
            if (mb_substr($word, -mb_strlen($compound)) === $compound) {
                echo "Found compound suffix: " . $compound . " -> " . implode('|', $parts) . "\n";
                $suffixes = array_merge($suffixes, $parts);
                $word = mb_substr($word, 0, -mb_strlen($compound));
                break;
            }
        }
        
        // Затем проверяем простые суффиксы, которые не нужно разбивать
        $simpleSuffixes = [
            'ат', 'ет',     // прошедшее время
            'ать', 'еть',   // настоящее время
            'аҫ', 'еҫ',     // будущее время
            'ӑп', 'ӗп',     // первое лицо
            'ӑн', 'ӗн',     // второе лицо
            'а', 'е'        // третье лицо
        ];
        
        foreach ($simpleSuffixes as $suffix) {
            if (mb_substr($word, -mb_strlen($suffix)) === $suffix) {
                echo "Found simple suffix: " . $suffix . "\n";
                array_unshift($suffixes, $suffix);
                $word = mb_substr($word, 0, -mb_strlen($suffix));
                break;
            }
        }
        // echo "suffixes " . $suffixes[0]. "\n";
        echo "Final suffixes array: " . implode('|', $suffixes) . "\n";
        return $suffixes;
    }
    
    /**
     * Определение части речи
     * @param string $word Слово
     * @param array $endings Окончания
     * @param array $suffixes Суффиксы
     * @param array $prefixes Приставки
     * @return string Определенная часть речи
     */
    private function determinePartOfSpeech(string $word, array $endings, array $suffixes, array $prefixes): string
    {
        // Проверяем, есть ли в суффиксах показатели глагола
        foreach ($suffixes as $suffix) {
            if (in_array($suffix, ['ет', 'ать', 'ить', 'еть', 'ять', 'ӑп', 'ӗп', 'тӑп', 'тӗп'])) {
                return Constants::VERB;
            }
        }
        
        // Если не нашли показателей глагола, проверяем другие части речи
        foreach ($suffixes as $suffix) {
            if (in_array($suffix, ['а', 'я', 'о', 'е', 'и', 'ы'])) {
                return Constants::NOUN;
            }
        }
        
        // По умолчанию возвращаем неизвестную часть речи
        return Constants::UNKNOWN;
    }
    
    /**
     * Сохранение результатов анализа
     * @param string $word Слово
     * @param string $partOfSpeech Часть речи
     * @param array $endings Окончания
     * @param array $suffixes Суффиксы
     * @param array $prefixes Приставки
     * @return void
     */
    private function saveAnalysisResults(string $word, string $partOfSpeech, array $endings, array $suffixes, array $prefixes): void
    {
        // Определяем корень (если не найден, используем само слово)
        $root = $word;
        
        // Удаляем суффиксы с конца слова
        foreach ($suffixes as $suffix) {
            if (mb_substr($root, -mb_strlen($suffix)) === $suffix) {
                $root = mb_substr($root, 0, -mb_strlen($suffix));
            }
        }
        
        // Разбиваем строку суффиксов на массив, если это строка
        $suffixArray = is_string($suffixes) ? explode('|', $suffixes) : $suffixes;
        
        echo "Saving analysis results for word: " . $word . "\n";
        echo "Suffixes array before face determination: " . implode('|', $suffixArray) . "\n";
        
        // Создаем новый вариант анализа
        $variant = [
            'chastRechi' => $partOfSpeech,
            'root' => $root,
            'affix' => implode('|', $suffixArray),
            'pluralOrNot' => $this->PluralOrNotDeterminer($word, $partOfSpeech, implode('|', $suffixArray)),
            'vremya' => $this->VremyaDeterminer($word, $partOfSpeech, implode('|', $suffixArray)),
            'padezh' => $this->PadezhDeterminer($word, $partOfSpeech, implode('|', $suffixArray)),
            'face' => $this->FaceDeterminer($word, $partOfSpeech, $suffixArray),
            'negativ' => $this->NegativDeterminer($word, $partOfSpeech, implode('|', $suffixArray)),
            'infinitiv' => $this->InfinitivDeterminer($word, $partOfSpeech, implode('|', $suffixArray)),
            'affixInfo' => $this->AffixInfoDeterminer($word, $partOfSpeech, implode('|', $suffixArray))
        ];
        
        echo "Face determined: " . $variant['face'] . "\n";
        
        // Добавляем вариант в массив вариантов
        $this->variants[] = $variant;
        
        // Добавляем корень в коллекцию корней и аффиксов
        $this->AddtoRootAffixCollection($root, implode('|', $suffixArray));
        
        // Увеличиваем счетчик
        $this->count++;
    }
    
    /**
     * Определяет часть речи на основе аффикса
     *
     * @param string $affixType Тип аффикса
     * @return string Часть речи
     */
    private function determinePartOfSpeechFromAffix(string $affixType): string
    {
        switch ($affixType) {
            case 'verb':
                return Constants::VERB;
            case 'noun':
                return Constants::NOUN;
            case 'adj':
                return Constants::ADJECTIVE;
            case 'adv':
                return Constants::ADVERB;
            case 'pron':
                return Constants::PRONOUN;
            case 'num':
                return Constants::NUMERIC;
            case 'part':
                return Constants::PART;
            case 'conj':
                return Constants::CONJ;
            case 'deeprichastie':
                return Constants::DEEPRICHASTIE;
            case 'prichastie':
                return Constants::PRICHASTIE;
            case 'deenoun':
                return Constants::DEENOUN;
            default:
                return Constants::UNKNOWN;
        }
    }
    
    /**
     * Записывает результаты в файл
     *
     * @param string $destination_path Путь к файлу назначения
     * @param string $word Слово
     * @param string $chastrechi Часть речи
     * @param string $pluralornot Число
     * @param string $vremya Время
     * @param string $padezh Падеж
     * @param string $root Корень
     * @param string $affix Аффикс
     * @param string $face Лицо
     * @param string $neg Отрицательность
     * @param string $infinitiv Инфинитив
     * @return void
     */
    private function PutInFile(string $destination_path, string $word, string $chastrechi, string $pluralornot, string $vremya, string $padezh, string $root, string $affix, string $face, string $neg, string $infinitiv): void
    {
        // Здесь должна быть логика записи результатов в файл
    }
    
    /**
     * Возвращает только уникальные элементы массива
     *
     * @param array $word_array Массив слов
     * @return array Уникальные элементы массива
     */
    private function OnlyDistinct(array $word_array): array
    {
        return array_unique($word_array);
    }
    
    /**
     * Анализирует слово
     *
     * @param string $word Слово для анализа
     * @return array Результаты анализа
     */
    public function analyze(string $word): array
    {
        $this->word = $word;
        $this->variants = [];
        $this->count = 0;
        
        $this->DealWithManualText($word);
        
        if (empty($this->variants)) {
            return [
                'word' => $word,
                'variants' => []
            ];
        }
        
        return [
            'word' => $word,
            'variants' => $this->variants
        ];
    }
    
    private function determineAffixType(string $word, string $affix): string
    {
        // Если аффикс находится в начале слова - это префикс
        if (mb_strpos($word, $affix) === 0) {
            return Constants::PREFIX;
        }
        
        // Если аффикс находится в конце слова - это суффикс
        if (mb_strpos($word, $affix) === mb_strlen($word) - mb_strlen($affix)) {
            return Constants::SUFFIX;
        }
        
        // Если аффикс находится в середине слова - это суффикс
        if (mb_strpos($word, $affix) !== false) {
            return Constants::SUFFIX;
        }
        
        // По умолчанию считаем суффиксом
        return Constants::SUFFIX;
    }

    private function loadAffixes()
    {
        $affixesFile = storage_path('app/morphology/Affixes.txt');
        if (file_exists($affixesFile)) {
            $lines = file($affixesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode(',', $line);
                // Берем все части кроме последней (тип части речи)
                $affixes = array_slice($parts, 0, -1);
                foreach ($affixes as $affix) {
                    $affix = trim($affix);
                    if (!empty($affix)) {
                        self::$Affixes[] = $affix;
                    }
                }
            }
            echo "Loaded " . count(self::$Affixes) . " individual affixes\n";
        }
    }
} 