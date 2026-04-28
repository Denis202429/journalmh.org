<?php
namespace App\Services\MorfParsingLibrary;

class ImprovedAffixHandler
{
    // Коллекция для хранения частей речи, определяемых для слова, и аффиксов
    public $ChastRechiCollection;
    // Объект для работы с морфологическим парсером
    public $morfParser;

    // Переменные для определения диапазона строк в словаре
    public $begin, $end;

    // Конструктор класса для начальной инициализации объектов
    public function __construct($l, $mp, $begin, $end)
    {
        error_log("ImprovedAffixHandler initialized with begin: $begin, end: $end");
        // Инициализация начала и конца диапазона строк словаря
        $this->begin = $begin;
        $this->end = $end;
        // Инициализация коллекции частей речи
        $this->ChastRechiCollection = $l;
        // Инициализация объекта морфологического парсера
        $this->morfParser = $mp;
    }

    //***********************************************************************************//
    /// ГЛАВНАЯ РАБОТА
    // Основной метод для разделения слова на корни и аффиксы, а также для определения части речи
    // Метод рекурсивный: он ищет соответствующие аффиксы и продолжает деление слова
    // public function mainWordDivider($word, $AffUnit)
    // {
    //     error_log("Checking affix: ");
    //     // Проходим по каждому символу слова с конца (начиная с длины 1)
    //     for ($i = 1; $i < strlen($word); $i++) {
    //         // Получаем подстроку, которая является предполагаемым аффиксом
    //         $aff = substr($word, -$i);

    //         error_log("Checking affix: " . $aff);

    //         // Проверяем, соответствует ли аффикс критериям с помощью статического метода
    //         if (AffixInfoProvider::affixesSuitable($aff, $AffUnit)) {
    //             // Пытаемся найти в словаре корень слова с добавлением этого аффикса
    //             $this->findInDictionary(substr($word, 0, strlen($word) - strlen($aff)), AffixInfoProvider::addAffixToUnit($aff, $AffUnit));

    //             // Логгируем разделение для отладки
    //             error_log("==================================================");

    //             // Рекурсивный вызов для оставшейся части слова
    //             $this->mainWordDivider(substr($word, 0, strlen($word) - strlen($aff)), AffixInfoProvider::addAffixToUnit($aff, $AffUnit));
    //         }
    //     }
    // }
// я внес изменения и у меня пишет сообщение об ошибке -  Expected type 'mixed'. Found 'void'.
    // public function mainWordDivider($word, $AffUnit)
    // {
    //     error_log("Starting mainWordDivider with word: $word");
    
    //     // Проходим по каждому символу слова с конца (начиная с длины 1)
    //     for ($i = 1; $i < strlen($word); $i++) {
    //         // Получаем подстроку, которая является предполагаемым аффиксом
    //         $aff = substr($word, -$i);
    //         error_log("Checking affix: " . $aff);
    
    //         // Проверяем, соответствует ли аффикс критериям
    //             if (AffixInfoProvider::affixesSuitable($aff, $AffUnit)) {
    //                 error_log("Affix suitable: " . $aff);
                
    //                 $updatedAffUnit = AffixInfoProvider::addAffixToUnit($aff, $AffUnit);
                    
    //                 // Вызов функции без присвоения результата
    //                 $this->findInDictionary(substr($word, 0, strlen($word) - strlen($aff)), $updatedAffUnit);
                
    //                 // Рекурсивный вызов для оставшейся части слова
    //                 $this->mainWordDivider(substr($word, 0, strlen($word) - strlen($aff)), $updatedAffUnit);
                

    //         } else {
    //             error_log("Affix not suitable: " . $aff);
    //         }
    //     }
    
    //     error_log("Finished mainWordDivider with word: $word");
    // }

    public function mainWordDivider($word, $AffUnit)
    {
        error_log("Starting mainWordDivider with word: $word, AffUnit: $AffUnit");
    
        // Проходим по каждому символу слова с конца (начиная с длины 1)
        for ($i = 1; $i < strlen($word); $i++) {
            // Получаем подстроку, которая является предполагаемым аффиксом
            $aff = substr($word, -$i);
            error_log("Checking affix: " . $aff);
    
            // Проверяем, соответствует ли аффикс критериям
            if (AffixInfoProvider::affixesSuitable($aff, $AffUnit)) {
                error_log("Affix suitable: " . $aff);
    
                // Обновляем аффиксный блок и выполняем поиск в словаре
                $updatedAffUnit = AffixInfoProvider::addAffixToUnit($aff, $AffUnit);
                $this->findInDictionary(substr($word, 0, strlen($word) - strlen($aff)), $updatedAffUnit);
    
                // Логгируем разделение для отладки
                error_log("==================================================");
    
                // Рекурсивный вызов для оставшейся части слова
                $this->mainWordDivider(substr($word, 0, strlen($word) - strlen($aff)), $updatedAffUnit);
            } else {
                error_log("Affix not suitable: " . $aff);
            }
        }
    
        error_log("Finished mainWordDivider with word: $word, AffUnit: $AffUnit");
    }
    




    //***********************************************************************************//
    // Поиск слова в словаре и работа с аффиксами
    private function findInDictionary($word, $AffUnit)
    {
        // Логгируем анализируемое слово и аффикс
        error_log("Анализируем: " . $word . " " . $AffUnit);

        // Получаем шаблон для части речи на основе переданного аффикса
        $pattern = AffixInfoProvider::patternPicker($AffUnit);

        // Определяем слоги по умолчанию для данного аффикса
        $default_syllables = ContextRules::defaultSyllables($AffUnit);

        // Объявляем переменные для контекста
        $context_syllables = null; 
        $context_chastrechi = null;
        $syll = [];
        $t_word = ""; // Переменная для временного хранения слова
        $t_AffUnit = ""; // Переменная для временного хранения аффикса

        // Ищем шаблон для слова в контексте
        ContextRules::findTemplate($word, $AffUnit, MorfParser::$Rules, $context_syllables, $context_chastrechi);

        // Проходим по строкам словаря, определяем диапазон строк
        for ($i = $this->begin; $i < $this->end; $i++) {
            // Разделяем строку словаря на части
            $seek_line = explode(',', MorfParser::$Dictionary[$i]);
            // Конвертируем часть речи
            $chastrechi = Helper::convertPartOfSpeech($seek_line[1]);
            // Проверяем соответствие части речи с шаблоном
            $match = preg_match($pattern, $chastrechi);

            if ($match) {
                // Если часть речи соответствует, проверяем её в контексте
                if (ContextRules::checkChastRechi($chastrechi, $context_chastrechi)) {
                    $syll = $context_syllables;
                    $t_word = ContextRules::$WORD;
                    $t_AffUnit = ContextRules::$AFFUNIT;
                } else {
                    // Используем стандартные слоги, если контекст не найден
                    $syll = $default_syllables;
                    $t_word = Helper::convertRoot($word, $chastrechi);
                    $t_AffUnit = $AffUnit;
                }

                // Обрабатываем слоги и проверяем каждое слово в словаре
                for ($j = 0; $j < count($syll); $j++) {
                    $fin_word = $t_word . $syll[$j];

                    // Если слово соответствует словарю и проверка пройдена
                    if ($fin_word == $seek_line[0] && Helper::checkCompatibility($fin_word, $t_AffUnit, $chastrechi)) {
                        // Определяем часть речи и режем аффикс
                        $chast = Helper::chastRechiSolver($chastrechi, $pattern);
                        $fin_aff = AffixInfoProvider::cutItOut($t_AffUnit, $chast);

                        // Добавляем результаты в коллекцию частей речи и аффиксов
                        $this->ChastRechiCollection[] = $chast;
                        $this->morfParser->addToRootAffixCollection($fin_word, $fin_aff);

                        // Логгируем успешное добавление
                        error_log("Добавляем: [" . $chast . ": " . $fin_word . " " . $fin_aff . "]");
                    }
                }
            }
        }
        // Проверяем исключения для данного слова и аффикса
        $this->checkExceptions($word, $AffUnit);

        // Логгируем переход к следующей паре слова и аффикса
        error_log("Переход к следующей паре.");
    }

    // Обрабатывает слова-исключения, которые могут не соответствовать стандартным правилам
    private function checkExceptions($word, $AffUnit)
    {
        // Извлекаем первый аффикс из комбинации
        $aff_first = explode('|', $AffUnit)[0];
        $chastrechi = ""; // Переменная для части речи
        $exception_word = $word; 
        $exception_AffUnit = $AffUnit;

        // Проходим по списку исключений
        foreach (MorfParser::$Exceptions as $exception_row) {
            // Разбиваем строку исключения на части
            $exception_row_array = explode(' ', $exception_row);

            // Если слово и первый аффикс совпадают с исключением
            if ($exception_row_array[0] == $word && $exception_row_array[1] == $aff_first) {
                // Обрабатываем исключение
                $exception_word = $exception_row_array[2];
                $chastrechi = $exception_row_array[3];

                // Добавляем найденную часть речи и аффикс в коллекцию
                $this->ChastRechiCollection[] = Helper::convertPartOfSpeech($chastrechi);
                $this->morfParser->addToRootAffixCollection($exception_word, $exception_AffUnit);

                // Логгируем найденное исключение
                error_log("Найдено исключение: [" . $exception_word . " " . $exception_AffUnit . " " . $chastrechi . "]");
            }
        }
    }
}
