<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; // Подключение базового класса для команд Artisan
use Goutte\Client; // Подключение клиента для парсинга веб-страниц
use Symfony\Component\HttpClient\HttpClient; // Подключение HTTP-клиента
use App\Models\ScrapedData; // Подключение модели для хранения данных парсинга (предположительно)
use Illuminate\Support\Facades\Log; // Подключение фасада для логирования

class ParseWebsite extends Command
{
    // Подпись команды, которую можно вызвать из Artisan
    protected $signature = 'parse:website {url}';

    // Описание команды
    protected $description = 'Parses a website and extracts text content from all pages';

    // Массив для хранения посещенных URL
    protected $visited = [];

    // Файл для сохранения посещенных URL
    protected $visitedFile = 'visited_urls.json';
    //protected $visitedFile = 'visited_urls.txt'; // Закомментировано, чтобы использовать JSON вместо TXT

    public function __construct()
    {
        parent::__construct();
        //$this->loadVisitedUrls();  // Закомментировано: загрузка посещенных URL при инициализации
    }

    /**
     * Метод для загрузки посещенных URL из файла
     */
    private function loadVisitedUrls()
    {
        $filePath = storage_path($this->visitedFile); // Определение пути к файлу

        if (file_exists($filePath)) { // Проверка, существует ли файл
            try {
                $content = file_get_contents($filePath); // Чтение содержимого файла
                $this->visited = json_decode($content, true); // Декодирование JSON в массив

                if (json_last_error() !== JSON_ERROR_NONE) { // Проверка на ошибки декодирования JSON
                    $this->error("JSON decode error: " . json_last_error_msg()); // Вывод сообщения об ошибке
                    $this->visited = []; // Инициализация пустого массива в случае ошибки
                }
            } catch (\Exception $e) {
                $this->error("Failed to read file: " . $e->getMessage()); // Обработка исключения при чтении файла
                $this->visited = []; // Инициализация пустого массива в случае исключения
            }
        } else {
            $this->error("File does not exist: $filePath"); // Сообщение, если файл не существует
            $this->visited = []; // Инициализация пустого массива
        }
    }

    /**
     * Метод для сохранения посещенных URL в файл
     */
    private function saveVisitedUrls()
    {
        file_put_contents(storage_path($this->visitedFile), json_encode($this->visited)); // Запись массива посещенных URL в файл в формате JSON
        $this->info("Visited URLs saved"); // Сообщение о сохранении URL
    }

    /**
     * Метод, выполняемый при запуске команды
     */
    public function handle()
    {
        $url = $this->argument('url'); // Получение URL из аргумента команды
        $this->info("Command started"); // Сообщение о старте команды
        $this->info("URL to parse: $url"); // Сообщение о URL для парсинга

        // Создание HTTP-клиента с определенными параметрами
        $httpClient = HttpClient::create([
            'timeout' => 380, // Тайм-аут 380 секунд
            'verify_peer' => false, // Отключение проверки сертификата
            'verify_host' => false, // Отключение проверки хоста
        ]);
        $client = new Client($httpClient); // Создание клиента для парсинга с использованием HTTP-клиента
        $this->info("Client initialized"); // Сообщение об инициализации клиента

        $this->crawl($client, $url); // Запуск обхода веб-сайта, начиная с заданного URL
    }

    /**
     * Метод для обхода веб-сайта и парсинга страниц
     */
 
     private function requestWithRetry(Client $client, $url, $retries = 3)
     {
         for ($i = 0; $i < $retries; $i++) { // Цикл для выполнения запроса с повторными попытками
             try {
                 return $client->request('GET', $url); // Выполнение запроса GET на указанный URL
             } catch (\Symfony\Component\HttpClient\Exception\TimeoutException $e) { // Обработка исключения тайм-аута
                 if ($i == $retries - 1) { // Если это последняя попытка
                     file_put_contents(storage_path('timeout_urls.txt'), $url . PHP_EOL, FILE_APPEND); // Запись URL в файл тайм-аутов
                     return null; // Возврат null, если запрос не удался после всех попыток
                 }
                 sleep(2); // Задержка перед повторной попыткой
             }
         }
         return null; // Возврат null, если запрос не удался
     }
     
     private function crawl(Client $client, $url)
     {
         $this->info("Crawling URL: $url"); // Логирование текущего URL для парсинга
         if (isset($this->visited[$url])) { // Проверка, был ли уже посещен этот URL
             $this->info("URL already visited: $url"); // Логирование сообщения о повторном посещении URL
             return; // Прекращение выполнения, если URL уже был посещен
         }
     
         $this->visited[$url] = true; // Добавление URL в массив посещенных
         $this->saveVisitedUrls(); // Сохранение обновленного списка посещенных URL
     
         // Массив строк, по которым URL будут исключены из парсинга
         $excludedSubstrings = ['#', '/stat/', '/s0/', '/s1/', '/s2/', '/s3/', '/s4/', '/s5/'];
         foreach ($excludedSubstrings as $substring) { // Проверка на наличие исключающих подстрок
             if (strpos($url, $substring) !== false) {
                 $this->logOrPrint("URL with excluded substring found: $url"); // Логирование исключенного URL
                 ScrapedData::create(['url' => $url]); // Сохранение URL в базу данных без парсинга
                 return; // Прекращение выполнения для исключенного URL
             }
         }
     
         $crawler = $this->requestWithRetry($client, $url); // Запрос страницы с повторными попытками
     
         if ($crawler === null) { // Проверка на случай тайм-аута
             $this->logOrPrint("Skipped URL due to timeout: $url"); // Логирование пропущенного URL
             return; // Прекращение выполнения, если запрос завершился тайм-аутом
         }
     
         $text = ''; // Инициализация переменной для хранения текста страницы
         $content = ''; // Инициализация переменной для хранения контента страницы
     
         // Извлечение номера страницы из URL, если он присутствует
         $page = null;
         if (preg_match('/\.([0-9]+)\.html$/', $url, $matches)) {
             $page = $matches[1]; // Сохранение номера страницы
         }
     
         // Инициализация селекторов для парсинга мета-информации
         $titleSelector = '';
         $authorSelector = '';
         $typeSelector = '';
         $yearSelector = '';
         $tagSelector = [];
     
         if (strpos($url, '/author/') !== false) {
             // Селекторы для страниц с /author/
             $cssSelectors = [
                 '#posts > div > div > div.author_teple'
             ];
         } elseif (strpos($url, '/haylav/') !== false) {
             // Селекторы для страниц с /haylav/
             $cssSelectors = [
                 '#posts > div:nth-child(1) > div.story'
             ];
     
             // Поиск значений в мета-информации
             $metaNode = $crawler->filter('#posts > div:nth-child(1) > div.meta');
             $titleNode = $crawler->filter('#posts > div.post > h2');
     
             if ($titleNode->count() > 0) {
                 $titleSelector = $titleNode->text(); // Сохранение заголовка
             }
     
             // Поиск значений в мета-информации и сохранение в соответствующие переменные
             $metaNode->filter('a')->each(function ($node) use (&$authorSelector, &$typeSelector, &$yearSelector, &$tagSelector) {
                 $href = $node->attr('href');
                 $text = $node->text();
     
                 if (strpos($href, '/author/') !== false) {
                     $authorSelector = $text;
                 } elseif (strpos($href, '/type/') !== false) {
                     $typeSelector = $text;
                 } elseif (strpos($href, '/year/') !== false) {
                     $yearSelector = $text;
                 } elseif (strpos($href, '/tag/') !== false) {
                     $tagSelector[] = $text;
                 }
             });
         } else {
             // Селекторы для всех остальных страниц
             $cssSelectors = [
                 '#posts > div > div > div',     // https://chuvash.org/lib/author/163.html
                 '#posts > div > div',
             ];
         }
     
         // Парсинг текста страницы по заданным селекторам
         foreach ($cssSelectors as $selector) {
             $node = $crawler->filter($selector);
             if ($node->count() > 0) {
                 $node->children()->each(function ($childNode) use (&$text) {
                     if (!$childNode->matches('ul')) { // Исключение текста из ul элементов
                         $text .= $childNode->text() . PHP_EOL;
                     }
                 });
             }
         }
     
         if (!empty($text)) { // Проверка, найден ли текст
             $this->logOrPrint("Scraped URL: $url"); // Логирование URL, с которого был извлечен текст
             $this->logOrPrint($text); // Логирование извлеченного текста
     
             $content .= $text . PHP_EOL; // Добавление текста в контент
     
             try {
                 // Сохранение данных в базу данных
                 ScrapedData::create([
                     'url' => $url,
                     'page' => $page,
                     'title' => $titleSelector,
                     'author' => $authorSelector,
                     'type' => $typeSelector,
                     'year' => $yearSelector,
                     'tags' => implode(', ', $tagSelector),
                     'content' => $content
                 ]);
             } catch (\Exception $e) {
                 // Логирование ошибок при сохранении данных в базу данных
                 Log::error('Database Insert Error', [
                     'url' => $url,
                     'page' => $page,
                     'title' => $titleSelector,
                     'author' => $authorSelector,
                     'type' => $typeSelector,
                     'year' => $yearSelector,
                     'tags' => implode(', ', $tagSelector),
                     'content' => $content,
                     'error' => $e->getMessage()
                 ]);
                 throw $e; // Повторное выбрасывание исключения
             }
         } else {
             $this->logOrPrint("No text found at URL: $url"); // Логирование сообщения об отсутствии текста
             ScrapedData::create(['url' => $url]); // Сохранение URL в базу данных без контента
         }
     
         // Обход всех ссылок на странице
         $crawler->filter('a')->each(function ($node) use ($client) {
             $link = $node->link();
             $url = $link->getUri();
     
             if ($this->isSameDomain($url)) { // Проверка, относится ли ссылка к тому же домену
                 $this->crawl($client, $url); // Рекурсивный вызов метода crawl для новой ссылки
             }
         });
     }
     
     private function logOrPrint($message)
     {
         $this->info($message); // Логирование сообщения в консоль
     }
     
     private function isSameDomain($url)
     {
         $parsedUrl = parse_url($url); // Разбор URL
         $parsedBaseUrl = parse_url($this->argument('url')); // Разбор базового URL
     
         // Проверка, относится ли URL к тому же домену и имеет ли он путь, начинающийся с /lib/
         return isset($parsedUrl['host'], $parsedUrl['path']) &&
             $parsedUrl['host'] === $parsedBaseUrl['host'] &&
             strpos($parsedUrl['path'], '/lib/') === 0;
     }
     
}









 // if (!empty($text)) {
    //     $this->info("Scraped URL: $url");
    //     $this->info($text);

    //     $content .= $text . PHP_EOL;
    //     file_put_contents(storage_path('parsed_text.txt'), $content, FILE_APPEND);
    // } else {
    //     $this->info("No text found at URL: $url");
    //     file_put_contents(storage_path('no_text_urls.txt'), $url . PHP_EOL, FILE_APPEND);
    // }

  //  php artisan parse:website https://chuvash.org/lib/