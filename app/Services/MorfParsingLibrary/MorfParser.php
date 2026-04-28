<?php

namespace App\Services\MorfParsingLibrary;

/**
 * Wowowow...This class is awesome!
 */
use Exception;

class MorfParser
{
    public  $iah; // разбиение слова на корень и аффиксы
    public $fd;  // определение характеристик

    // Значения по умолчанию
    #region Default values
    public $dictionary_path;
    public $affixes_path;
    public $rules_path;
    public $exceptions_path;
    public $affinfo_path;

    public $InputText_Path;
    public $OutputText_Path;

    public static  $Dictionary = [];
    public static  $Affixes = [];
    public static  $Rules = [];
    public static  $Exceptions = [];
    public static  $AffInfo = [];
    #endregion

    // Характеристики слова
    #region WordFeatures
    public $Word;
    public $ChastRechi = [];
    public $Root = [];
    public $Affix = [];
    public $Vremya = [];
    public $Padezh = [];
    public $PluralOrNot = [];
    public $Face = [];
    public $Negativ = [];
    public $Infinitiv = [];
    public $AffixInfo = [];

    public $RootAffixesCollection = [];
    public $COUNT;
    #endregion

    public function __construct()
    {
        // Получаем базовый путь до текущего файла (текущей директории, где находится класс)
        $baseDir = dirname(__FILE__);
        $this->dictionary_path = $baseDir . '/DICT.txt';
        $this->affixes_path = $baseDir . '/Affixes.txt';
        $this->rules_path = $baseDir . '/Rules.txt';
        $this->exceptions_path = $baseDir . '/Exceptions.txt';
        $this->affinfo_path = $baseDir . '/AffInfo.txt';
        $this->InputText_Path = $baseDir . '/Input.txt';
        $this->OutputText_Path = $baseDir . '/Output.txt';

        $this->InitializeComponents();
    }

    // Начальная инициализация компонентов
    public function InitializeComponents()
    {
        $this->RootAffixesCollection = [];
        $this->ChastRechi = [];
        $this->Root = [];
        $this->Affix = [];
        $this->Vremya = [];
        $this->Padezh = [];
        $this->PluralOrNot = [];
        $this->Face = [];
        $this->Negativ = [];
        $this->Infinitiv = [];
        $this->AffixInfo = [];

        $this->COUNT = 110;
    }





    // Функция для работы с текстом из файла
    public function dealWithTextFromFile($inputPath)
    {
        echo "НАЧАЛО. " . date("Y-m-d H:i:s") . "\n";
        $readText = file_get_contents($inputPath);

        // Разбиваем текст на слова и фильтруем только уникальные слова
        $wordArray = array_unique(preg_split('/[\s,.\-:;!?]+/', mb_strtolower($readText), -1, PREG_SPLIT_NO_EMPTY));

        foreach ($wordArray as $word) {
            echo "На вход поступило слово: " . $word . "\n";
            $this->searchInDictionaries($word);
            $this->determineOnYourOwn($word);

            // Обработка всех характеристик слова
            for ($i = 0; $i < $this->COUNT; $i++) {
                $this->putInFile(
                    $this->OutputText_Path,
                    $this->Word,
                    $this->ChastRechi[$i],
                    $this->PluralOrNot[$i],
                    $this->Vremya[$i],
                    $this->Padezh[$i],
                    $this->Root[$i],
                    $this->Affix[$i],
                    $this->Face[$i],
                    $this->Negativ[$i],
                    $this->Infinitiv[$i]
                );
            }

            $this->initializeComponents();
        }

        echo "КОНЕЦ. " . date("Y-m-d H:i:s") . "\n";
    }

    // Анализ слов, введенных вручную
    public function dealWithManualText($inputText)
    {
     //   echo "НАЧАЛО. " . date("Y-m-d H:i:s") . "\n";
        $lmp = [];

        $wordArray = array_unique(preg_split('/[\s,]+/', mb_strtolower($inputText), -1, PREG_SPLIT_NO_EMPTY));

        foreach ($wordArray as $word) {
            echo "На вход поступило слово: " . $word . "\n";
            $this->searchInDictionaries($word);
            $this->determineOnYourOwn($word);

           
            dump($this->Word);
            dump($this->ChastRechi);
            dump($this->PluralOrNot);
            dump($this->Vremya);
            dump($this->Padezh);
            dump($this->Root);
            dump($this->Affix);
            dump($this->Face);
            dump($this->Negativ);
            dump($this->Infinitiv);

            if (count($this->ChastRechi) == 0) {
                $lmp[] = new MorfParser($this->Word);
            } else {
                $lmp[] = new MorfParser($this);
            }

            $this->initializeComponents();
        }

      //  echo "КОНЕЦ. " . date("Y-m-d H:i:s") . "\n";
        return $lmp;
    }


    public function DictionaryPath($dictionary_path = null)
    {
        // Если аргумент $dictionary_path не передан, возвращаем текущее значение
        if ($dictionary_path === null) {
            return $this->dictionary_path;
        } else {
            // Устанавливаем новый путь
            $this->dictionary_path = $dictionary_path;

            // Загружаем содержимое файла в статическое свойство $Dictionary
            self::$Dictionary = file($dictionary_path, FILE_IGNORE_NEW_LINES);

            // Вызываем статический метод Helper::GetNumbersOfLines для работы с $Dictionary
            Helper::GetNumbersOfLines(self::$Dictionary);
        }
    }



    // Путь к аффиксам
    // public function getAffixesPath()
    // {
    //     return $this->affixes_path;
    // }

    // public function setAffixesPath($affixes_path)
    // {
    //     $this->affixes_path = $affixes_path;
    //     $this->Affixes = file($affixes_path, FILE_IGNORE_NEW_LINES);
    // }

    public function AffixesPath($path = null)
    {
        if ($path !== null) {
            // Устанавливаем новый путь и загружаем данные из файла
            $this->affixes_path = $path;

            if (file_exists($this->affixes_path)) {
                $this->Affixes = file($this->affixes_path, FILE_IGNORE_NEW_LINES);
            } else {
                error_log("Файл не найден: $this->affixes_path");
            }
        }

        // Возвращаем текущий путь
        return $this->affixes_path;
    }

    // Функция для пути к правилам
    public function RulesPath($path = null)
    {
        if ($path !== null) {
            $this->rules_path = $path;

            if (file_exists($this->rules_path)) {
                $this->Rules = file($this->rules_path, FILE_IGNORE_NEW_LINES);
            } else {
                error_log("Файл не найден: $this->rules_path");
            }
        }

        return $this->rules_path;
    }

    // Функция для пути к исключениям
    public function ExceptionsPath($path = null)
    {
        if ($path !== null) {
            $this->exceptions_path = $path;

            if (file_exists($this->exceptions_path)) {
                $this->Exceptions = file($this->exceptions_path, FILE_IGNORE_NEW_LINES);
            } else {
                error_log("Файл не найден: $this->exceptions_path");
            }
        }

        return $this->exceptions_path;
    }

    // Функция для пути к AffInfo
    public function AffInfoPath($path = null)
    {
        if ($path !== null) {
            $this->affinfo_path = $path;

            if (file_exists($this->affinfo_path)) {
                $this->AffInfo = file($this->affinfo_path, FILE_IGNORE_NEW_LINES);
            } else {
                error_log("Файл не найден: $this->affinfo_path");
            }
        }

        return $this->affinfo_path;
    }

    // Функция для пути к исходному файлу
    public function InputPath($path = null)
    {
        if ($path !== null) {
            $this->InputText_Path = $path;
        }

        return $this->InputText_Path;
    }

    // Функция для пути к выходному файлу
    public function OutputPath($path = null)
    {
        if ($path !== null) {
            $this->OutputText_Path = $path;
        }

        return $this->OutputText_Path;
    }


    // Корни и аффиксы (коллекция)
    public function addToRootAffixCollection($root, $affix)
    {
        $this->RootAffixesCollection[] = ['root' => $root, 'affix' => $affix];
    }

    //   // Часть речи
    
    // public function chastRechiDeterminer($word)
    // {
    //     // Инициализируем переменные для begin и end
    //     $begin = 0;
    //     $end = 0;

    //     // Вызов статического метода с передачей begin и end по ссылке
    //     Helper::getCurrentNumbers($word, $begin, $end);
    //     echo "Begin: $begin, End: $end\n";
    //     $results = [];
    //     $iah = new ImprovedAffixHandler($results, $this, $begin, $end);
    //     echo "Calling mainWordDivider for word: $word\n";
    //     $iah->mainWordDivider($word, "");


    //     echo 'Results after affix division: ' . json_encode($results) . "\n";

    //     return $results;
    // }

    public function chastRechiDeterminer($word)
    {
        // Инициализируем переменные для begin и end
        $begin = 0;
        $end = 0;
    
        // Вызов статического метода с передачей begin и end по ссылке
        Helper::getCurrentNumbers($word, $begin, $end);
        echo "Begin: $begin, End: $end\n";
    
        // Инициализация массива для хранения результатов
        $results = [];
    
        try {
            echo "Initializing ImprovedAffixHandler...\n";
            $iah = new ImprovedAffixHandler($results, $this, $begin, $end);
            echo "ImprovedAffixHandler initialized successfully.\n";
            $iah->mainWordDivider($word, "ат");
        } catch (Exception $e) {
            echo "Error initializing ImprovedAffixHandler: " . $e->getMessage() . "\n";
        }
    
        echo 'Results after affix division: ' . json_encode($results) . "\n";
    
      //  dump($iah);

        return $results;
    }



    // Число
    private function pluralOrNotDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $PLURAL = Constants::UNKNOWN;

        switch ($chastrechi) {
            case Constants::NOUN:
            case Constants::ADJECTIVE:
            case Constants::NUMERIC:
                $PLURAL = $this->fd->determinePluralOfSusch($word, $aff_massiv);
                break;
            case Constants::PRICHASTIE:
                $PLURAL = $this->fd->determinePluralOfPrichastie($word, $aff_massiv);
                break;
            case Constants::PRONOUN:
                $PLURAL = $this->fd->determinePluralOfPron($word, $aff_massiv);
                break;
            case Constants::VERB:
                $PLURAL = $this->fd->determinePluralOfVerb($word, $aff_massiv);
                break;
        }

        return $PLURAL;
    }

    // Время
    private function vremyaDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $VREMYA = Constants::UNKNOWN;

        switch ($chastrechi) {
            case Constants::VERB:
                $VREMYA = $this->fd->determineVremyaOfGlagol($word, $aff_massiv);
                break;
            case Constants::PRICHASTIE:
                $VREMYA = $this->fd->determineVremyaOfPrichastie($word, $aff_massiv);
                break;
            default:
                $VREMYA = $this->fd->determineVremyaOfOstalnoe($word, $aff_massiv);
                break;
        }

        return $VREMYA;
    }

    // Падеж
    private function padezhDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $PADEZH = Constants::UNKNOWN;

        switch ($chastrechi) {
            case Constants::NOUN:
            case Constants::ADJECTIVE:
            case Constants::NUMERIC:
            case Constants::PRONOUN:
                $PADEZH = $this->fd->determinePadezhOfSusch($word, $aff_massiv);
                break;
            case Constants::PRICHASTIE:
                $PADEZH = $this->fd->determinePadezhOfPrichastie($word, $aff_massiv);
                break;
            default:
                break;
        }

        return $PADEZH;
    }

    // Лицо
    private function faceDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $FACE = Constants::UNKNOWN;

        switch ($chastrechi) {
            case Constants::NOUN:
                $FACE = $this->fd->determineFaceOfSusch($word, $aff_massiv);
                break;
            case Constants::PRICHASTIE:
                $FACE = $this->fd->determineFaceOfPrichastie($word, $aff_massiv);
                break;
            case Constants::VERB:
                $FACE = $this->fd->determineFaceOfGlagol($word, $aff_massiv);
                break;
            case Constants::NUMERIC:
                $FACE = $this->fd->determineFaceOfChislitelnoe($word, $aff_massiv);
                break;
            case Constants::PRONOUN:
                $FACE = $this->fd->determineFaceOfPronoun($word, $aff_massiv);
                break;
        }

        return $FACE;
    }

    // Негатив
    private function negativDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $res = Constants::POSITIVE;

        if ($chastrechi == Constants::VERB) {
            $res = $this->fd->determineNegativeOfGlagol($word, $aff_massiv);
        }

        return $res;
    }

    // Инфинитив
    private function infinitivDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $res = Constants::NULL;

        if ($chastrechi == Constants::VERB) {
            $res = $this->fd->determineInfinitivOfGlagol($word, $aff_massiv);
        }

        return $res;
    }

    // Информация об аффиксах
    private function affixInfoDeterminer($word, $chastrechi, $affixes)
    {
        $aff_massiv = explode('|', $affixes);
        $res = Constants::NULL;

        $res = $this->fd->determineAffixInfo($word, $chastrechi, $aff_massiv);

        return $res;
    }
    public function determineOnYourOwn($word)
    {
        // Инициализируем переменную для хранения аффикса
        $aff = "";

        // Убираем окончания (аффиксы) из слова и получаем основную форму с помощью метода Helper::loseEnds.
        // Метод возвращает корневую часть слова и его аффиксы.
        $temp_w = Helper::loseEnds($word, $aff);
     //   dump( 'temp_w:',$temp_w);
        // echo  $temp_w;
        // Трансформируем некоторые слова (например, заменяем некоторые формы на стандартные) с помощью метода transformSomeWords.
        $temp_w = Helper::transformSomeWords($temp_w);
      //  dump( 'temp_w_transformSomeWords:',$temp_w);
        // Определяем часть речи слова с помощью метода chastRechiDeterminer, который возвращает массив частей речи.
        $temp_ChastRechi = $this->chastRechiDeterminer($temp_w);
        dump( 'temp_ChastRechi:',$temp_ChastRechi);
        // Увеличиваем счётчик найденных частей речи на количество элементов в массиве $temp_ChastRechi.
        $this->COUNT += count($temp_ChastRechi);

        // Цикл проходит по каждой части речи, найденной для слова.
        for ($i = 0; $i < count($temp_ChastRechi); $i++) {
            // Сохраняем оригинальное слово в переменную класса для дальнейшего использования.
            $this->Word = $word;

            // Добавляем текущую часть речи в массив ChastRechi.
            $this->ChastRechi[] = $temp_ChastRechi[$i];

            // Добавляем корневую часть слова, извлечённую из массива RootAffixesCollection (заполняется на основе анализа слова).
            // Здесь $this->RootAffixesCollection[$i]['root'] — это корень слова, связанный с частью речи.
            $this->Root[] = $this->RootAffixesCollection[$i]['root'];

            // Добавляем аффикс, соединяя аффиксы из RootAffixesCollection и аффикс, который был извлечён из оригинального слова.
            $this->Affix[] = $this->RootAffixesCollection[$i]['affix'] . $aff;

            // Определяем единственное или множественное число с помощью метода pluralOrNotDeterminer,
            // используя корень слова, часть речи и аффикс, и добавляем результат в массив PluralOrNot.
            $this->PluralOrNot[] = $this->pluralOrNotDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем время глагола (прошедшее, настоящее, будущее) с помощью метода vremyaDeterminer
            // и добавляем результат в массив Vremya.
            $this->Vremya[] = $this->vremyaDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем падеж (именительный, родительный и т.д.) с помощью метода padezhDeterminer
            // и добавляем результат в массив Padezh.
            $this->Padezh[] = $this->padezhDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем лицо (1, 2 или 3 лицо) с помощью метода faceDeterminer
            // и добавляем результат в массив Face.
            $this->Face[] = $this->faceDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем, является ли глагол утвердительным или отрицательным, с помощью метода negativDeterminer
            // и добавляем результат в массив Negativ.
            $this->Negativ[] = $this->negativDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем, является ли слово инфинитивом, с помощью метода infinitivDeterminer
            // и добавляем результат в массив Infinitiv.
            $this->Infinitiv[] = $this->infinitivDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));

            // Определяем дополнительную информацию о аффиксе с помощью метода affixInfoDeterminer
            // и добавляем результат в массив AffixInfo.
            $this->AffixInfo[] = $this->affixInfoDeterminer(end($this->Root), $temp_ChastRechi[$i], end($this->Affix));
        }
    }

    private function searchInDictionaries($word)
    {
        // Инициализируем переменную для хранения аффикса
        $aff = "";

        // Временное слово, получаем его базовую форму без аффиксов с помощью метода Helper::loseEnds
        // Метод loseEnds убирает аффиксы из слова и возвращает основное слово (корень) и аффикс
        $temp_w = Helper::loseEnds($word, $aff);

        // Сохраняем оригинальное слово в переменную класса для дальнейшего использования
        $this->Word = $word;

        // Инициализация переменных $begin и $end, которые будут использоваться для задания диапазона поиска в словаре
        $begin = 0;
        $end = 0;

        // Метод getCurrentNumbers вычисляет границы диапазона строк в словаре, 
        // где могут находиться подходящие слова. Передаем $begin и $end по ссылке, чтобы они были изменены внутри метода.
      
        Helper::getCurrentNumbers($temp_w, $begin, $end);

        // Основной цикл, который проходит по строкам словаря от $begin до $end
        for ($i = $begin; $i < $end; $i++) {
            // Разбиваем текущую строку словаря на части (элементы) с помощью explode, используя запятую в качестве разделителя
            $seek_arr = explode(',', self::$Dictionary[$i]);
          //  dump( '$seek_arr:', $seek_arr) ;
            // Проверяем, совпадает ли временное слово $temp_w с первым элементом текущей строки словаря
            if ($temp_w == $seek_arr[0]) {
                dump('  if ($temp_w == $seek_arr[0]) ');
                // Переменная $found определяет, было ли слово найдено в одном из шаблонов
                $found = false;

                // Проходим по массиву $defaults, который содержит шаблоны по умолчанию для частей речи
                foreach (Helper::$defaults as $line) {
                    // Разбиваем строку с шаблоном на массив элементов
                    $mas = explode(',', $line);

                    // Сравниваем второй элемент словаря (часть речи) с первым элементом шаблона
                    if ($mas[0] == $seek_arr[1]) {
                        dump(' if ($mas[0] == $seek_arr[1]) ');
                        // Если часть речи - это местоимение (pron), обрабатываем его по специальным правилам
                        if ($seek_arr[1] == "pron") {
                            dump(' if ($seek_arr[1] == "pron") ');
                            // Проходим по массиву $pron_defaults, который содержит шаблоны для местоимений
                            for ($j = 0; $j < count(Helper::$pron_defaults); $j++) {
                                // Разбиваем строку с шаблоном для местоимений на массив
                                $mas_pron = explode(',', Helper::$pron_defaults[$j]);

                                // Если слово и шаблонное местоимение совпадают, заполняем массивы данными
                                if ($seek_arr[0] == $mas_pron[0]) { 
                                    dump(' if ($seek_arr[0] == $mas_pron[0]) ');
                                    // Добавляем часть речи (местоимение)
                                    $this->ChastRechi[] = Constants::PRONOUN;
                                    // Определяем единственное или множественное число
                                    $this->PluralOrNot[] = $mas_pron[1];
                                    // Время по умолчанию (отсутствует)
                                    $this->Vremya[] = Constants::NULL;
                                    // Падеж
                                    $this->Padezh[] = $mas_pron[2];
                                    // Корень слова
                                    $this->Root[] = $temp_w;
                                    // Аффикс, связанный с этим словом
                                    $this->Affix[] = $aff;
                                    // Лицо (1, 2 или 3 лицо)
                                    $this->Face[] = $mas_pron[3];
                                    // Утвердительная форма
                                    $this->Negativ[] = Constants::POSITIVE;
                                    // Инфинитив по умолчанию (отсутствует)
                                    $this->Infinitiv[] = Constants::NULL;
                                    // Информация о аффиксе по умолчанию (отсутствует)
                                    $this->AffixInfo[] = Constants::NULL;

                                    // Помечаем, что слово было найдено
                                    $found = true;
                                    break;
                                }
                            }
                        } else {

                            dump('else');
                            // Если часть речи не местоимение, заполняем массивы на основе шаблона
                            $this->ChastRechi[] = $mas[1];    // Часть речи
                            $this->PluralOrNot[] = $mas[2];   // Единственное/множественное число
                            $this->Vremya[] = $mas[3];        // Время
                            $this->Padezh[] = $mas[4];        // Падеж
                            $this->Root[] = $temp_w;          // Корень слова
                            $this->Affix[] = $aff;            // Аффикс
                            $this->Face[] = $mas[5];          // Лицо
                            $this->Negativ[] = $mas[6];       // Утвердительная или отрицательная форма
                            $this->Infinitiv[] = $mas[7];     // Инфинитив
                            $this->AffixInfo[] = Constants::NULL; // Информация о аффиксе

                            // Помечаем, что слово было найдено
                            $found = true;
                            break;
                        }
                    }
                }

                // Если слово не найдено в шаблонах, заполняем массивы значениями по умолчанию
                if (!$found) {
                    dump('!$found');
                    // Преобразуем часть речи через метод convertPartOfSpeech
                    $this->ChastRechi[] = Helper::convertPartOfSpeech($seek_arr[1]);
                    // Заполняем остальные поля значениями по умолчанию
                    $this->PluralOrNot[] = Constants::NULL;
                    $this->Vremya[] = Constants::NULL;
                    $this->Padezh[] = Constants::NULL;
                    $this->Root[] = $temp_w;
                    $this->Affix[] = $aff;
                    $this->Face[] = Constants::NULL;
                    $this->Negativ[] = Constants::POSITIVE;
                    $this->Infinitiv[] = Constants::NULL;
                    $this->AffixInfo[] = Constants::NULL;

                    dump();


                }

                // Увеличиваем счётчик найденных слов
                $this->COUNT++;
            }
        }

        // dump($this->ChastRechi);
        // dump($this->PluralOrNot);
        // dump($this->Vremya );
        // dump($this->Padezh );
        // dump($this->Root);
        // dump($this->Affix );
        // dump($this->Face);
        // dump($this->Negativ );
        // dump($this->Infinitiv );
        // dump($this->AffixInfo );
    }


    // Запись в файл проанализированных слов
    private function putInFile($destination_path, $word, $chastrechi, $pluralornot, $vremya, $padezh, $root, $affix, $face, $neg, $infinitiv)
    {
        if (!file_exists($destination_path)) {
            file_put_contents($destination_path, '');
        }

        $new_slovar_line = $word . " " . $chastrechi . " " . $pluralornot . " " . $vremya . " " . $padezh . " " . $root . " " . $affix . " " . $face . " " . $neg . " " . $infinitiv;

        $lines = file($destination_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!in_array($new_slovar_line, $lines)) {
            file_put_contents($destination_path, $new_slovar_line . PHP_EOL, FILE_APPEND);
        }
    }

    // Отделение повторяющихся слов
    private function onlyDistinct($word_array)
    {
        $hs = array_unique($word_array);
        return array_values($hs);
    }
}
