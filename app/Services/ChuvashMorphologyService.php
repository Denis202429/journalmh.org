<?php

namespace App\Services;

use App\Libraries\ChuvashAffixLevels;
use App\Services\FeaturesDeterminer; // Добавить эту строку
use App\Models\ChuvAlph; // Добавьте этот импорт

class ChuvashMorphologyService
{
    protected array $affixLevels;
    protected array $affixLevels_noun;
    protected array $affixLevels_verb;
    protected array $affixLevels_num;
    protected array $affixLevels_adj;
    protected array $affixLevels_adv;
    protected array $affixLevels_part;

    protected array $dictionary = [];
    protected array $dictionaryByWord = []; // Для быстрого поиска по слову

    protected FeaturesDeterminer $featuresDeterminer;

    public function __construct()
    {
        $this->loadDictionaryFromDB();
        $this->affixLevels = ChuvashAffixLevels::LEVELS;
        $this->affixLevels_noun = ChuvashAffixLevels::LEVELS_noun; // Прямое обращение к константе
        $this->affixLevels_verb = ChuvashAffixLevels::LEVELS_verb;
        $this->affixLevels_num = ChuvashAffixLevels::LEVELS_num;
        $this->affixLevels_adj = ChuvashAffixLevels::LEVELS_adj;
        $this->affixLevels_adv = ChuvashAffixLevels::LEVELS_adv;
        // $this->affixLevels_part = ChuvashAffixLevels::LEVELS_part;


        // $this->featuresDeterminer = new FeaturesDeterminer();

        $this->featuresDeterminer = new \App\Services\FeaturesDeterminer(); // Добавить \
    }


    // protected function loadDictionary(): void
    // {

    //     $path = __DIR__ . '/DICT.txt';
    //     if (!file_exists($path)) {
    //         throw new \RuntimeException("Файл словаря не найден: $path");
    //     }
    //     foreach (file($path) as $line) {
    //         $line = trim($line);
    //         if (empty($line)) continue;
    //         [$word, $pos] = explode(',', $line);
    //         $this->dictionary[] = [
    //             'word' => trim($word),
    //             'pos' => trim($pos)
    //         ];
    //     }
    // } 

    protected function loadDictionaryFromDB(): void
    {
        try {
            // Загружаем все записи из базы данных
            $records = ChuvAlph::all();

            foreach ($records as $record) {
                $entry = [
                    'word' => trim($record->Word),
                    'pos' => trim($record->Pos),
                    'info' => trim($record->Info ?? '')
                ];

                $this->dictionary[] = $entry;

                // Для быстрого поиска сохраняем в отдельный массив
                $this->dictionaryByWord[$entry['word']] = $entry;
            }

            // Сортируем по длине слова (от самых длинных к коротким)
            // Это важно для правильного поиска основ
            usort($this->dictionary, fn($a, $b) => mb_strlen($b['word']) <=> mb_strlen($a['word']));
        } catch (\Exception $e) {
            throw new \RuntimeException("Не удалось загрузить словарь из базы данных: " . $e->getMessage());
        }
    }




    // А можно сделать загрузку словаря не из файла а из базы данных. 
    // Модель таблицы в которой уже загружены слова из словаря DICT выглядит следующим образом - 
    // <?php

    // namespace App\Models;

    // use Illuminate\Database\Eloquent\Factories\HasFactory;
    // use Illuminate\Database\Eloquent\Model;

    // class ChuvAlph extends Model
    // {
    //     use HasFactory;

    //     protected $table = 'chuv_alph';

    //     // Укажите заполняемые поля
    //     protected $fillable = ['Word', 'Pos', 'Info'];
    // }


    // public function analyze(string $inputWord): array
    // {
    //     $inputWord = mb_strtolower(trim($inputWord));

    //     if (empty($inputWord)) {
    //         throw new \InvalidArgumentException('Слово не может быть пустым');
    //     }

    //     $matches = [];
    //     if (!is_array($this->dictionary)) {
    //         throw new \RuntimeException('Dictionary must be an array');
    //     }

    //     // Поиск ВСЕХ совпадений в словаре
    //     foreach ($this->dictionary as $entry) {
    //         if (str_starts_with($inputWord, $entry['word'])) {
    //             $matches[] = $entry;
    //         }
    //     }

    //     $allAnalyses = [];

    //     // Если нашли совпадения в словаре
    //     if (!empty($matches)) {
    //         // Сортируем по длине основы (от самых длинных к коротким)
    //         usort($matches, fn($a, $b) => mb_strlen($b['word']) <=> mb_strlen($a['word']));

    //         // Анализируем КАЖДУЮ найденную основу
    //         foreach ($matches as $match) {
    //             $affixString = mb_substr($inputWord, mb_strlen($match['word']));
    //             $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($match['word']), $match['pos']);

    //             $allAnalyses[] = [
    //                 'analysis_number' => count($allAnalyses) + 1,
    //                 'word' => $inputWord,
    //                 'root' => $match['word'],
    //                 'pos' => $match['pos'],
    //                 'affixes' => $parsedAffixes,
    //                 'affixes_string' => $affixString,
    //                 'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
    //                 'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $match['pos']),
    //                 'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $match['pos']),
    //                 'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $match['pos'], $match['word']),
    //                 'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $match['pos']),
    //                 'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $match['pos'])
    //             ];
    //         }
    //     } else {
    //         // Для слов не из словаря
    //         $guessedRoot = $this->guessRoot($inputWord);
    //         $guessedPos = $this->guessPartOfSpeech($inputWord);
    //         $affixString = mb_substr($inputWord, mb_strlen($guessedRoot));
    //         $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($guessedRoot), $guessedPos);

    //         $allAnalyses[] = [
    //             'analysis_number' => 1,
    //             'word' => $inputWord,
    //             'root' => $guessedRoot,
    //             'pos' => $guessedPos,
    //             'affixes' => $parsedAffixes,
    //             'affixes_string' => $affixString,
    //             'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
    //             'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $guessedPos),
    //             'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $guessedPos),
    //             'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $guessedPos, $guessedRoot),
    //             'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $guessedPos),
    //             'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $guessedPos)
    //         ];
    //     }

    //     // ВСЕГДА возвращаем единый формат
    //     return [
    //         'word' => $inputWord,
    //         'total_analyses' => count($allAnalyses),
    //         'analyses' => $allAnalyses
    //     ];
    // }


    // Оптимизированный метод поиска основ
    public function findPossibleRoots(string $inputWord): array
    {
        $matches = [];

        // Вариант 1: Поиск по префиксам (самые длинные сначала)
        foreach ($this->dictionary as $entry) {
            if (str_starts_with($inputWord, $entry['word'])) {
                $matches[] = [
                    'word' => $entry['word'],
                    'pos' => $entry['pos'],
                    'info' => $entry['info'],
                    'match_type' => 'exact_prefix'
                ];
            }
        }

        // Вариант 2: Быстрый поиск по хеш-таблице (если нужно точное совпадение)
        if (isset($this->dictionaryByWord[$inputWord])) {
            $exactMatch = $this->dictionaryByWord[$inputWord];
            // Проверяем, нет ли уже такой записи
            $alreadyExists = false;
            foreach ($matches as $match) {
                if ($match['word'] === $exactMatch['word']) {
                    $alreadyExists = true;
                    break;
                }
            }
            if (!$alreadyExists) {
                $matches[] = [
                    'word' => $exactMatch['word'],
                    'pos' => $exactMatch['pos'],
                    'info' => $exactMatch['info'],
                    'match_type' => 'exact_word'
                ];
            }
        }

        return $matches;
    }

    // public function analyze(string $inputWord): array
    // {
    //     $inputWord = mb_strtolower(trim($inputWord));

    //     if (empty($inputWord)) {
    //         throw new \InvalidArgumentException('Слово не может быть пустым');
    //     }

    //     // Ищем все возможные основы
    //     $matches = $this->findPossibleRoots($inputWord);

    //     $allAnalyses = [];

    //     // Если нашли совпадения в словаре
    //     if (!empty($matches)) {
    //         // Сортируем по длине основы (от самых длинных к коротким)
    //         usort($matches, fn($a, $b) => mb_strlen($b['word']) <=> mb_strlen($a['word']));

    //         // Анализируем КАЖДУЮ найденную основу
    //         foreach ($matches as $index => $match) {
    //             $affixString = mb_substr($inputWord, mb_strlen($match['word']));
    //             $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($match['word']), $match['pos']);

    //             $allAnalyses[] = [
    //                 'analysis_number' => $index + 1,
    //                 'word' => $inputWord,
    //                 'root' => $match['word'],
    //                 'pos' => $match['pos'],
    //                 'info' => $match['info'],
    //                 'match_type' => $match['match_type'],
    //                 'affixes' => $parsedAffixes,
    //                 'affixes_string' => $affixString,
    //                 'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
    //                 'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $match['pos']),
    //                 'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $match['pos']),
    //                 'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $match['pos'], $match['word']),
    //                 'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $match['pos']),
    //                 'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $match['pos'])
    //             ];
    //         }
    //     } else {
    //         // Для слов не из словаря
    //         $guessedRoot = $this->guessRoot($inputWord);
    //         $guessedPos = $this->guessPartOfSpeech($inputWord);
    //         $affixString = mb_substr($inputWord, mb_strlen($guessedRoot));
    //         $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($guessedRoot), $guessedPos);

    //         $allAnalyses[] = [
    //             'analysis_number' => 1,
    //             'word' => $inputWord,
    //             'root' => $guessedRoot,
    //             'pos' => $guessedPos,
    //             'info' => 'не найдено в словаре',
    //             'match_type' => 'guessed',
    //             'affixes' => $parsedAffixes,
    //             'affixes_string' => $affixString,
    //             'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
    //             'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $guessedPos),
    //             'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $guessedPos),
    //             'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $guessedPos, $guessedRoot),
    //             'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $guessedPos),
    //             'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $guessedPos)
    //         ];
    //     }

    //     // ВСЕГДА возвращаем единый формат
    //     return [
    //         'word' => $inputWord,
    //         'total_analyses' => count($allAnalyses),
    //         'dictionary_size' => count($this->dictionary), // Информация о размере словаря
    //         'analyses' => $allAnalyses
    //     ];
    // }

    public function analyze(string $inputWord): array
    {
        $inputWord = mb_strtolower(trim($inputWord));

        if (empty($inputWord)) {
            throw new \InvalidArgumentException('Слово не может быть пустым');
        }

        $matches = [];
        if (!is_array($this->dictionary)) {
            throw new \RuntimeException('Dictionary must be an array');
        }

        // Поиск ВСЕХ совпадений в словаре
        foreach ($this->dictionary as $entry) {
            if (str_starts_with($inputWord, $entry['word'])) {
                $matches[] = $entry;
            }
        }

        $allAnalyses = [];

        // Если нашли совпадения в словаре
        if (!empty($matches)) {
            // Сортируем по длине основы (от самых длинных к коротким)
            usort($matches, fn($a, $b) => mb_strlen($b['word']) <=> mb_strlen($a['word']));

            // Анализируем КАЖДУЮ найденную основу
            foreach ($matches as $match) {
                $affixString = mb_substr($inputWord, mb_strlen($match['word']));
                $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($match['word']), $match['pos']);

                // ФИЛЬТР: Проверяем, есть ли неизвестные аффиксы
                $hasUnknownAffix = false;
                foreach ($parsedAffixes as $affix) {
                    if (isset($affix['name']) && $affix['name'] === 'неизвестный аффикс') {
                        $hasUnknownAffix = true;
                        break;
                    }
                }
                // Пропускаем вариант, если есть неизвестный аффикс
                if ($hasUnknownAffix) {
                    continue;
                }

                $analysis = [
                    'analysis_number' => count($allAnalyses) + 1,
                    'word' => $inputWord,
                    'root' => $match['word'],
                    'pos' => $match['pos'],
                    'affixes' => $parsedAffixes,
                    'affixes_string' => $affixString,
                    'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
                    'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $match['pos']),
                    'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $match['pos']),
                    'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $match['pos'], $match['word']),
                    'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $match['pos']),
                    'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $match['pos'])
                ];

                $allAnalyses[] = $analysis;
            }
        } else {
            // Для слов не из словаря
            $guessedRoot = $this->guessRoot($inputWord);
            $guessedPos = $this->guessPartOfSpeech($inputWord);
            $affixString = mb_substr($inputWord, mb_strlen($guessedRoot));
            $parsedAffixes = $this->parseAffixes($affixString, mb_strlen($guessedRoot), $guessedPos);

            // ФИЛЬТР: Проверяем, есть ли неизвестные аффиксы
            $hasUnknownAffix = false;
            foreach ($parsedAffixes as $affix) {
                if (isset($affix['name']) && $affix['name'] === 'неизвестный аффикс') {
                    $hasUnknownAffix = true;
                    break;
                }
            }

            // Добавляем только если нет неизвестных аффиксов
            if (!$hasUnknownAffix) {
                $allAnalyses[] = [
                    'analysis_number' => 1,
                    'word' => $inputWord,
                    'root' => $guessedRoot,
                    'pos' => $guessedPos,
                    'affixes' => $parsedAffixes,
                    'affixes_string' => $affixString,
                    'case' => $this->featuresDeterminer->determineCase($parsedAffixes),
                    'plural' => $this->featuresDeterminer->determinePlural($parsedAffixes, $guessedPos),
                    'time' => $this->featuresDeterminer->determineTime($parsedAffixes, $guessedPos),
                    'face' => $this->featuresDeterminer->determineFace($parsedAffixes, $guessedPos, $guessedRoot),
                    'negative' => $this->featuresDeterminer->determineNegative($parsedAffixes, $guessedPos),
                    'infinitiv' => $this->featuresDeterminer->determineInfinitiv($parsedAffixes, $guessedPos)
                ];
            }
        }

        // ВСЕГДА возвращаем единый формат
        return [
            'word' => $inputWord,
            'total_analyses' => count($allAnalyses),
            'analyses' => $allAnalyses
        ];
    }




    protected function guessRoot(string $word): string
    {
        // Эвристика по умолчанию
        return $word;
    }

    protected function guessPartOfSpeech(string $word): string
    {
        return 'unknown';
    }

    /**
     * Анализирует аффиксы в слове после основы
     * 
     * @param string $affixes - часть слова после корня (например, "нӑн" для "лашанӑн")
     * @param int $rootLength - длина корня (например, 4 для "лаша")
     * @param string $rootPos - часть речи корня ("noun", "verb" и т.д.)
     * @return array - массив с найденными аффиксами
     */



    // public function parseAffixes(string $affixes, int $rootLength, string $rootPos): array
    // {
    //     // Инициализация массива для хранения результатов разбора
    //     $result = [];
    //     // Оставшаяся часть слова для анализа (изначально все переданные аффиксы)
    //     $remaining = $affixes;
    //     // Позиция начала аффиксов в исходном слове (после корня)
    //     $positionInWord = $rootLength;

    //     // Выбираем массив аффиксов в зависимости от части речи
    //     $affixLevels = $this->getAffixLevelsByPos($rootPos);

    //     // Основной цикл: пока есть неразобранные символы
    //     while ($remaining !== '') {
    //         $found = false; // Флаг для отслеживания найденных аффиксов

    //         // Перебираем уровни аффиксов от высшего (26) к низшему (1)
    //         for ($level = 26; $level >= 1; $level--) {
    //             // Пропускаем несуществующие уровни в выбранном массиве
    //             if (!isset($affixLevels[$level])) {
    //                 continue;
    //             }

    //             // Пропускаем уровни, не подходящие для текущей части речи
    //             // if (!$this->isAffixLevelAllowed($level, $rootPos)) {
    //             //     continue;
    //             // }

    //             // Получаем все аффиксы текущего уровня из выбранного массива
    //             $levelAffixes = $affixLevels[$level];

    //             // Сортируем по длине (от самых длинных к коротким)
    //             usort($levelAffixes, fn($a, $b) => mb_strlen($b['affix']) <=> mb_strlen($a['affix']));

    //             foreach ($levelAffixes as $item) {
    //                 if (str_ends_with($remaining, $item['affix'])) {
    //                     $result[] = [
    //                         'affix' => $item['affix'],
    //                         'name' => $item['name'],
    //                         'type' => 'level_' . $level,
    //                         'position' => $positionInWord + mb_strlen($remaining) - mb_strlen($item['affix']),
    //                         'level' => $level
    //                     ];
    //                     $remaining = mb_substr($remaining, 0, -mb_strlen($item['affix']));
    //                     $found = true;
    //                 }
    //             }
    //         }

    //         // Если не нашли подходящий аффикс
    //         if (!$found) {
    //             // Добавляем остаток как неизвестный аффикс
    //             $result[] = [
    //                 'affix' => $remaining,
    //                 'name' => 'неизвестный аффикс',
    //                 'type' => 'unknown',
    //                 'position' => $positionInWord,
    //                 'level' => 0
    //             ];
    //             break; // Завершаем цикл while
    //         }
    //     }

    //     // Сортируем результаты по позиции в слове (от начала к концу)
    //     usort($result, fn($a, $b) => $a['position'] <=> $b['position']);
    //     return $result;
    // }



    /**
     * Анализирует аффиксы в слове после основы
     * 
     * Алгоритм работает "с конца слова к началу", отщепляя самые длинные
     * возможные суффиксы из известных списков. Это важно для чувашской 
     * агглютинативной морфологии, где аффиксы присоединяются в строгом порядке.
     * 
     * Примеры работы:
     * 1. Слово "килте" (в доме) с основой "кил" (дом):
     *    - "те" → местный падеж (уровень 7)
     *    - Результат: [['affix' => 'те', 'name' => 'местный падеж', ...]]
     * 
     * 2. Слово "кӗнекисем" (книги, мн.ч.) с основой "кӗнеке" (книга):
     *    - "сем" → показатель множественного числа (уровень 7)
     *    - Результат: [['affix' => 'сем', 'name' => 'множественное число', ...]]
     * 
     * 3. Слово "вулама" (читать, инфинитив) с основой "вул" (читать):
     *    - "а" → гласная основы глагола
     *    - "ма" → показатель инфинитива (уровень 25)
     *    - Результат: [['affix' => 'а', ...], ['affix' => 'ма', ...]]
     * 
     * 4. Слово "калаҫатпӑр" (мы поговорили) с основой "калаҫ" (разговаривать):
     *    - "ат" → показатель настоящего времени (уровень 15)
     *    - "п" → показатель 1 лица ед.ч. (уровень 16)
     *    - "ӑр" → показатель множественного числа (уровень 17)
     *    - Результат: три аффикса в правильном порядке
     * 
     * @param string $affixes - часть слова после корня (например, "нӑн" для "лашанӑн")
     * @param int $rootLength - длина корня (например, 4 для "лаша")
     * @param string $rootPos - часть речи корня ("noun", "verb" и т.д.)
     * @return array - массив с найденными аффиксами, каждый элемент содержит:
     *                'affix' => найденный аффикс (например, "нӑн"),
     *                'name' => название/значение аффикса,
     *                'type' => тип/уровень аффикса ('level_1', 'level_2', etc.),
     *                'position' => позиция начала аффикса в исходном слове,
     *                'level' => уровень аффикса (1-26, где 1 - ближайший к корню)
     */
    public function parseAffixes(string $affixes, int $rootLength, string $rootPos): array
    {
        // Инициализация массива для хранения результатов разбора
        // Пример: для слова "килте" (корень "кил" длиной 3, аффикс "те")
        $result = [];

        // Оставшаяся часть слова для анализа (изначально все переданные аффиксы)
        // Пример: для "лашанӑн" при корне "лаша" (4 символа) останется "нӑн"
        $remaining = $affixes;

        // Позиция начала аффиксов в исходном слове (после корня)
        // Пример: если корень "кил" длиной 3, то аффиксы начинаются с позиции 3
        $positionInWord = $rootLength;

        // Выбираем массив аффиксов в зависимости от части речи
        // Для существительных - один набор, для глаголов - другой и т.д.
        // Пример: для существительных доступны падежные аффиксы, 
        // для глаголов - временные и личные показатели
        $affixLevels = $this->getAffixLevelsByPos($rootPos);

        // Основной цикл: пока есть неразобранные символы
        // Работаем от конца слова к началу, как в реальном морфологическом анализе
        // Пример: "лашанӑн" → сначала находим "н", затем "ӑн"
        while ($remaining !== '') {
            $found = false; // Флаг для отслеживания найденных аффиксов

            // Перебираем уровни аффиксов от высшего (26) к низшему (1)
            // Это соответствует порядку присоединения аффиксов в слове:
            // Ближайшие к корню аффиксы (уровень 1-3) обрабатываются последними,
            // Внешние аффиксы (уровень 23-26) обрабатываются первыми.
            // 
            // Пример для глагола "калаҫатпӑр" (мы поговорили):
            // 1. Уровень 17: "ӑр" (множественное число) - найден первым
            // 2. Уровень 16: "п" (1 лицо) - найден вторым  
            // 3. Уровень 15: "ат" (настоящее время) - найден третьим
            for ($level = 26; $level >= 1; $level--) {
                // Пропускаем несуществующие уровни в выбранном массиве
                if (!isset($affixLevels[$level])) {
                    continue;
                }

                // Пропускаем уровни, не подходящие для текущей части речи
                // Пример: падежные аффиксы (уровни 1-8) не присоединяются к глаголам
                if (!$this->isAffixLevelAllowed($level, $rootPos)) {
                    continue;
                }

                // Получаем все аффиксы текущего уровня из выбранного массива
                // Пример: уровень 7 содержит ['те', 'та', 'че'] - местный падеж
                $levelAffixes = $affixLevels[$level];

                // Сортируем по длине (от самых длинных к коротким)
                // Важно: сначала проверяем длинные варианты, чтобы не разбить их на части
                // Пример: аффикс "машкӑн" (6 символов) нужно найти целиком,
                // а не как "ма" + "шкӑн"
                usort($levelAffixes, fn($a, $b) => mb_strlen($b['affix']) <=> mb_strlen($a['affix']));

                // Перебираем все аффиксы текущего уровня
                foreach ($levelAffixes as $item) {
                    // Проверяем, оканчивается ли остаток слова на текущий аффикс
                    // Пример: remaining = "нӑн", проверяем окончание на "нӑн", "н", "ӑн"
                    if (str_ends_with($remaining, $item['affix'])) {
                        // Найденный аффикс добавляем в результат
                        // Пример для "лашанӑн": найден "н" → записываем его
                        $result[] = [
                            'affix' => $item['affix'],
                            'name' => $item['name'],
                            'type' => 'level_' . $level,
                            'position' => $positionInWord + mb_strlen($remaining) - mb_strlen($item['affix']),
                            'level' => $level
                        ];

                        // Укорачиваем оставшуюся строку, отрезая найденный аффикс
                        // Пример: было "нӑн", нашли "н" → остается "ӑн"
                        $remaining = mb_substr($remaining, 0, -mb_strlen($item['affix']));

                        $found = true;
                        break 2; // Выходим из обоих циклов (foreach и for), переходим к следующей итерации while
                    }
                }
            }

            // Если не нашли подходящий аффикс после перебора всех уровней
            // Это означает, что остаток содержит неизвестную последовательность
            // Пример: для заимствованных слов или ошибок в слове
            if (!$found) {
                // Добавляем остаток как неизвестный аффикс
                // Пример: слово "компьютерла" (компьютерный) с корнем "компьютер"
                // остаток "ла" может быть неизвестным для системы
                $result[] = [
                    'affix' => $remaining,
                    'name' => 'неизвестный аффикс',
                    'type' => 'unknown',
                    'position' => $positionInWord,
                    'level' => 0
                ];
                break; // Завершаем цикл while, так как дальше разобрать не можем
            }
        }

        // Сортируем результаты по позиции в слове (от начала к концу)
        // Это нужно, чтобы аффиксы шли в порядке их следования в слове
        // Пример: для "калаҫатпӑр" получим: "ат" (поз. 5), "п" (поз. 7), "ӑр" (поз. 8)
        usort($result, fn($a, $b) => $a['position'] <=> $b['position']);

        return $result;
    }



    /**
     * Возвращает массив аффиксов в зависимости от части речи
     */
    protected function getAffixLevelsByPos(string $rootPos): array
    {
        // Определяем, какой массив использовать
        switch ($rootPos) {
            case 'noun':
                return $this->affixLevels_noun ?? $this->affixLevels;
            case 'verb':
                return $this->affixLevels_verb ?? $this->affixLevels;
            case 'num':
                return $this->affixLevels_num ?? $this->affixLevels;
            case 'adj':
                return $this->affixLevels_adj ?? $this->affixLevels;
            case 'adv':
                return $this->affixLevels_adv ?? $this->affixLevels;
            case 'part':
                return $this->affixLevels_verb ?? $this->affixLevels;
                // для причастий последовательность следования аффиксов как для глаголов
            default:
                return $this->affixLevels; // По умолчанию общий массив
        }
    }

    /**
     * Проверяет, разрешены ли аффиксы указанного уровня для данной части речи
     * 
     * @param int $level - уровень аффикса (1-26)
     * @param string $rootPos - часть речи (noun, verb и т.д.)
     * @return bool - true если аффиксы уровня разрешены для этой части речи
     */
    protected function isAffixLevelAllowed(int $level, string $rootPos): bool
    {
        // Правила разрешения аффиксов по уровням и частям речи
        $levelRules = [
            // Уровень => разрешенные части речи
            1 => ['noun'],
            2 => ['noun'],
            3 => ['noun'],
            4 => ['noun', 'adj', 'adv', 'part'],
            5 => ['num'],
            6 => ['num'],
            7 => ['*'],
            8 => ['*'],
            9 => ['verb'],
            10 => ['verb'],
            11 => ['verb'],
            12 => ['verb'],
            13 => ['verb'],
            14 => ['verb'],
            15 => ['verb'],
            16 => ['verb'],
            17 => ['verb'],
            18 => ['verb'],
            19 => ['verb'],
            20 => ['verb'],
            21 => ['verb'],
            22 => ['verb'],
            23 => ['*'],
            24 => ['*'],
            25 => ['*'],
            26 => ['*'],
        ];

        // Если уровень не существует в правилах - запрещаем
        if (!isset($levelRules[$level])) {
            return false;
        }

        // Специальный случай: символ '*' означает все части речи
        if (in_array('*', $levelRules[$level])) {
            return true;
        }

        // Проверяем, есть ли часть речи в разрешенных для этого уровня
        return in_array($rootPos, $levelRules[$level]);
    }
}

// у меня программа анализирует самую длинную основу которую найдет в словаре DICT.txt. Мне надо сделать так 
// чтобы программа анализировала все основы которые найдет в словаре. И чтобы потом выводила это все в консоль. Как это сделать? 


// Предложи решение следующей проблемы. Допустим нам надо проанализировать слово вулӑп. 
//     у нас в словаре DICT.txt находятся два слова - вулӑ - существительное , вула - глагол.
//     Программа найдет наибольшую основу это вулӑ , но для существительных программа не найдет аффикса в массиве  $affixLevels_noun.
//     То есть вариант с тем что это существительное отпадает если есть какие то неизвезтные аффиксы после анализа.
//     Правильным будет вариант следущий - основа 'вула', а аффикс 'ӑп'.  
//     Чаще все выпадают на конце слова гласные ӗ,ӑ,у,ӳ,а ,о,я (ы,и не выпадают),  и к основне присоединяются вместо них аффиксы.  
//     Вобщем две глобальные проблемы:
//     1. Как выявить основы в словаре если во многих словах у них конечные гласные выпадают.
//     2. Как сделать анализ по всем вариантам основы в словаре для анализируемого слова.

// кажется ты не совсем верно понял . У нас в словаре содержится слово вула - глагол и у него выпадает последняя
//  гласная - 'а' и в итоге к основе 'вул' присоединяется аффикс 'ӑп'. То есть в словаре все слова которые оканчиваются
//   на  ['ӑ', 'ӗ', 'у', 'ÿ', 'а', 'о', 'я', 'е'] могут терять свою последнюю гласную и к основе присоединяется аффикс.
//  в заимствованиях у,о не выдадает 

// выпадение гласных на конце актуально для  -  verb,noun,adv,adj,part,pronoun
// в некоторых словах заимствовваниях конечный о оставется без изменения во всех формах - какао - какаошӑн, Борнео - Борнеон
//  Конго - конгон, радио - радион,
// 
// в заимствованиях из русского на у - к ним падежные аффиксы присоединяются сразу. 
// НАпример - Баку - Бакун, Бакуна, кенгуру = кенгурун, 

// noun (n) - существительное
// verb (v) - глагол
// adv  - наречие
// part - причастие

// adj (adjective) - прилагательное
// descr (descriptive) - имитативы 
// int (interjection) - междометие
// pron (pronoun) - местоимение
// num - числительное
//  conj - союз 


// кукӑльсем   - мякгий знак не выпадает

// каятчӗ   - а здесь мягкий знак выпадает.  



// 1) ҫӑм, ҫӗм, шар, шер, ӑшӗ, ӗшӗ, серен, ашкал, ешкел, шкал, 
// 2)тарах, терех, рах, рех, 
// 3)кал, кел, кала, келе, машкӑн, мешкӗн, 
// 4)ай, ей, OnlyGlagol
// 5)са, се, ӑтт, ӗтт, ӑттӑм, ӗттӗм, ӑттӑн, ӗттӗн, атт, етт, аттӑм, еттӗм, ттӑм, ттӗм, ттӑн, ттӗн, атна, етне, ӗчч, 
// 6)а, е, 
// 7)ас, ес, асси, есси, 
// 8)ма, ме, маҫ, меҫ, масӑр, месӗр, малла, мелле, сассӑн, сессӗн, 
// 9)алла, елле, лла, лле, ла, ле, 
// 10)ни, 
// 11)хи, 
// 12)ман, мен, 
// 13)акан, екен, аканни, екенни, манни, менни 
// 14)ӑм, ӗм, мӗш, 
// 15)у, ӳ, и, 
// 16)ллӑ, ллӗ, лӑ, лӗ, лли, ли, 
// 17)мелли, малли, 
// 18)ри, ти, 
// 19)масть, маст, мест, мас, мес, 
// 20)м, 
// 21)атчӗ, етчӗ, тӑп, тӗп, тӑм, тӗм, рӑм, рӗм, тӑр, тӗр, рӑр, рӗр, рӑн, рӗн, тӑн, тӗн, мӑн, мӗн, ать, ет, ап, еп, рӗҫ, чӗҫ, ӗҫ, иччен, мап, меп, сан, ан, ен, мӗ, нӑ, нӗ, нӑҫем, нӗҫем, аҫ, еҫ, ччӑр, ччӗр, 
// 22)скер, 
// 23)ҫ, рӗ, 
// 24)ат, ет, 
// 25)ӑп, ӗп, пӑр, пӗр, 
// 26)ам, ем, асшӑн, есшӗн, 
// 27)ӑр, ӗр, ӑн, ӗн, сен, 
// 28)ар, ер, 
// 29)ӗ, 
// 30)сем, сам, 
// 31)н, ра, ре, та, те, тан, тен, ран, рен, пала, пеле, палан, пелен, на, не, шӑн, шӗн, 
// 32)па, пе, 
// 33)сӑр, сӗр, сӑмӑр, сӗмӗр, 
// 34)че, чи, чен, ччен, 
// 36)ах, ех, х, 
// 37)ччӗ, чӗ, 
//  php artisan analyze:chuvash "ывӑлӑмсене"
