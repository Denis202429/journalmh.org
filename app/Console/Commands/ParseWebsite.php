<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use App\Models\ScrapedData;
use Illuminate\Support\Facades\Log;

class ParseWebsite extends Command
{
    //protected $signature = 'parse:website {url}';
    protected $signature = 'parse:website {url} {siteType}';

    protected $description = 'Parses a website and extracts text content from all pages';
    protected $visited = [];
    protected $queue = [];
    protected $visitedFile = 'visited_urls.json';
    protected $stateFile = 'parsing_state.json';
    protected $currentUrl = null;

    public function __construct()
    {
        parent::__construct();
        $this->loadVisitedUrls();
        $this->loadState();
    }

    private function loadVisitedUrls()
    {
        $filePath = storage_path($this->visitedFile);
        if (file_exists($filePath)) {
            try {
                $content = file_get_contents($filePath);
                $this->visited = json_decode($content, true) ?: [];
            } catch (\Exception $e) {
                $this->error("Failed to read file: " . $e->getMessage());
            }
        }
    }

    private function saveVisitedUrls()
    {
        file_put_contents(storage_path($this->visitedFile), json_encode($this->visited));
    }

    private function loadState()
    {
        $filePath = storage_path($this->stateFile);
        if (file_exists($filePath)) {
            try {
                $content = file_get_contents($filePath);
                $state = json_decode($content, true);
                $this->queue = $state['queue'] ?? [];
                $this->currentUrl = $state['currentUrl'] ?? null;
            } catch (\Exception $e) {
                $this->error("Failed to read state file: " . $e->getMessage());
            }
        }
    }

    private function saveState()
    {
        $state = [
            'queue' => $this->queue,
            'currentUrl' => $this->currentUrl,
        ];
        file_put_contents(storage_path($this->stateFile), json_encode($state));
    }


    // public function handle()
    // {
    //     $url = $this->argument('url');
    //     $siteType = $this->argument('siteType');

    //     if ($this->currentUrl === null) {
    //         $this->currentUrl = $url;
    //     }

    //     $httpClient = HttpClient::create([
    //         'timeout' => 380,
    //         'verify_peer' => false,
    //         'verify_host' => false,
    //     ]);

    //     $client = new Client($httpClient);

    //     if (empty($this->queue)) {
    //         $this->queue[] = $this->currentUrl;
    //     }

    //     while (!empty($this->queue)) {
    //         $nextUrl = array_shift($this->queue);

    //         if ($siteType === 'cvruwiki') {
    //             $this->crawlRuWiki($client, $nextUrl);
    //         } elseif ($siteType === 'chuvash') {
    //             $this->crawlChuvash($client, $nextUrl);
    //         } elseif ($siteType === 'hypar') {
    //             $this->crawlHypar($client, $nextUrl);
    //         } elseif ($siteType === 'avangard') {
    //             $this->crawlAvangard($client, $nextUrl);
    //         } elseif ($siteType === 'chuvashnews') {
    //             $this->crawlChuvashnews($client, $nextUrl);
    //         } elseif ($siteType === 'chuvashblogs') {
    //             $this->crawlChuvashBlogs($client, $nextUrl);
    //         } else {
    //             $this->error("Unknown site type: $siteType");
    //             return;
    //         }
    //     }
    // }





    // private function crawlRuWiki(Client $client, $url)
    // {
    //     $this->info("Crawling RUVICKI URL: $url");

    //     // Check if the URL is already visited or exists in the database
    //     if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
    //         $this->info("URL already visited: $url");
    //         return;
    //     }

    //     $this->visited[$url] = true;
    //     $this->saveVisitedUrls();
    //     $this->currentUrl = $url;
    //     $this->saveState();

    //     // Exclude service pages and special URLs
    //     $excludedSubstrings = [
    //         '#',
    //         'action=edit',
    //         'printable=yes',
    //         'Special:',
    //         'File:',
    //         'Category:',
    //         'User:',
    //         'Template:',
    //         'Help:',
    //         'w/index.php',
    //         'РУВИКИ:',
    //         '%D0%A0%D0%A3%D0%92%D0%98%D0%9A%D0%98%'
    //     ];

    //     foreach ($excludedSubstrings as $substring) {
    //         if (strpos($url, $substring) !== false) {
    //             $this->logOrPrint("URL with excluded substring found: $url");
    //             return;
    //         }
    //     }

    //     $crawler = $this->requestWithRetry($client, $url);
    //     if ($crawler === null) {
    //         $this->logOrPrint("Skipped URL due to timeout: $url");
    //         return;
    //     }

    //     $text = '';
    //     $content = '';
    //     $title = '';
    //     $page = null;

    //     // Extract page number if exists (for multi-page articles)
    //     if (preg_match('/\.([0-9]+)\.html$/', $url, $matches)) {
    //         $page = $matches[1];
    //     }

    //     // Parse title
    //     $titleSelectors = [
    //         '#firstHeading > span',
    //         '.firstHeading',
    //         'h1.firstHeading',
    //         '#firstHeading',
    //         'h1'
    //     ];

    //     foreach ($titleSelectors as $selector) {
    //         if ($crawler->filter($selector)->count() > 0) {
    //             $title = trim($crawler->filter($selector)->text());
    //             $this->info("Found title: $title");
    //             break;
    //         }
    //     }

    //     // Parse content - multiple selectors for different content types
    //     $contentSelectors = [
    //         '#mw-content-text > div.mw-parser-output > p',
    //         // '#mw-content-text > div.mw-parser-output > ul',
    //         // '#mw-content-text > div.mw-parser-output > ol',
    //         // '#mw-content-text > div.mw-parser-output > div',
    //         // '#mw-content-text > div.mw-parser-output > table',
    //         '#mw-content-text > div.mw-parser-output > h2',
    //         '#mw-content-text > div.mw-parser-output > h3',
    //         '#mw-content-text > div.mw-parser-output > h4',
    //         '.mw-parser-output p',
    //         '.mw-parser-output ul',
    //         '.mw-parser-output ol',
    //         // '.mw-parser-output div',
    //         // '.mw-parser-output table'
    //     ];

    //     foreach ($contentSelectors as $selector) {
    //         if ($crawler->filter($selector)->count() > 0) {
    //             $crawler->filter($selector)->each(function ($node) use (&$text) {
    //                 $paragraph = trim($node->text());

    //                 // Filter out empty content, navigation, and citations
    //                 if (
    //                     !empty($paragraph) &&
    //                     strlen($paragraph) > 10 &&
    //                     $this->isValidWikiContent($paragraph) &&
    //                     !$this->isNavigationOrCitation($paragraph)
    //                 ) {
    //                     $text .= $paragraph . PHP_EOL . PHP_EOL;
    //                     $this->info("✅ Added content: " . substr($paragraph, 0, 50) . "...");
    //                 }
    //             });
    //         }
    //     }

    //     // If no content found with selectors, try to get all text from main container
    //     if (empty($text)) {
    //         $mainContainerSelectors = [
    //             '#mw-content-text > div.mw-parser-output',
    //             '.mw-parser-output',
    //             '#mw-content-text'
    //         ];

    //         foreach ($mainContainerSelectors as $selector) {
    //             if ($crawler->filter($selector)->count() > 0) {
    //                 $text = trim($crawler->filter($selector)->text());
    //                 $this->info("Extracted content from main container");
    //                 break;
    //             }
    //         }
    //     }

    //     if (!empty($text)) {
    //         $this->logOrPrint("Scraped RUVICKI URL: $url");

    //         $content = $text;

    //         try {
    //             ScrapedData::create([
    //                 'url' => $url,
    //                 'page' => $page,
    //                 'title' => $title,
    //                 'author' => '', // RUVICKI usually doesn't have explicit authors
    //                 'type' => 'wiki', // Set type as wiki
    //                 'year' => '', // Year not typically available
    //                 'tags' => '', // Tags not typically available
    //                 'content' => $content
    //             ]);

    //             $this->info("✅ Successfully saved to database: $url");
    //         } catch (\Exception $e) {
    //             Log::error('Database Insert Error for RUVICKI', [
    //                 'url' => $url,
    //                 'page' => $page,
    //                 'title' => $title,
    //                 'content' => $content,
    //                 'error' => $e->getMessage()
    //             ]);
    //             $this->error("❌ Database insert error: " . $e->getMessage());
    //         }
    //     } else {
    //         $this->logOrPrint("No text found at RUVICKI URL: $url");

    //         // Log URLs with no content for debugging
    //         try {
    //             $logMessage = date('Y-m-d H:i:s') . " - No content found (RUVICKI): " . $url . PHP_EOL;
    //             file_put_contents(storage_path('no_content_ruwiki_urls.txt'), $logMessage, FILE_APPEND | LOCK_EX);
    //         } catch (\Exception $e) {
    //             $this->warn("Could not write to no_content_ruwiki_urls.txt: " . $e->getMessage());
    //         }
    //     }

    //     // Collect links for further crawling
    //     $crawler->filter('a')->each(function ($node) {
    //         $href = $node->attr('href');

    //         if (
    //             !empty($href) &&
    //             strpos($href, '#') !== 0 &&
    //             strpos($href, 'javascript:') !== 0
    //         ) {
    //             $absoluteUrl = $this->makeAbsoluteUrl($href, $this->currentUrl);

    //             if ($absoluteUrl && $this->isRuWikiUrl($absoluteUrl)) {
    //                 // Additional filtering for RUVICKI
    //                 $isExcluded = (
    //                     strpos($absoluteUrl, 'action=edit') !== false ||
    //                     strpos($absoluteUrl, 'printable=yes') !== false ||
    //                     strpos($absoluteUrl, 'Special:') !== false ||
    //                     strpos($absoluteUrl, 'File:') !== false ||
    //                     strpos($absoluteUrl, 'Category:') !== false ||
    //                     strpos($absoluteUrl, 'User:') !== false ||
    //                     strpos($absoluteUrl, 'Template:') !== false ||
    //                     strpos($absoluteUrl, 'Help:') !== false ||
    //                     strpos($absoluteUrl, 'w/index.php') !== false
    //                 );

    //                 if (!$isExcluded) {
    //                     if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
    //                         $this->queue[] = $absoluteUrl;
    //                         $this->saveState();
    //                         $this->info("🔗 Added RUVICKI link to queue: $absoluteUrl");
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }


    // public function handle()
    // {
    //     $url = $this->argument('url');
    //     $siteType = $this->argument('siteType');

    //     if ($this->currentUrl === null) {
    //         $this->currentUrl = $url;
    //     }

    //     $httpClient = HttpClient::create([
    //         'timeout' => 30,
    //         'verify_peer' => false,
    //         'verify_host' => false,
    //     ]);

    //     $client = new Client($httpClient);

    //     if (empty($this->queue)) {
    //         $this->queue[] = $this->currentUrl;
    //         $this->info("🚀 Starting with URL: $this->currentUrl");
    //     }

    //     $processed = 0;
    //     $maxPages = 200; // Увеличим лимит

    //     while (!empty($this->queue) && $processed < $maxPages) {
    //         $nextUrl = array_shift($this->queue);

    //         $this->info("🎯 Processing: " . basename($nextUrl));

    //         if ($siteType === 'ruwiki' || $siteType === 'cvruwiki') {
    //             $this->crawlRuWiki($client, $nextUrl);
    //         } elseif ($siteType === 'chuvash') {
    //             $this->crawlChuvash($client, $nextUrl);
    //         }

    //         $processed++;
    //         $this->info("📊 PROGRESS: $processed pages processed, " . count($this->queue) . " in queue");

    //         // Небольшая пауза
    //         usleep(500000); // 0.5 секунды
    //     }

    //     if (empty($this->queue)) {
    //         $this->info("💤 Queue is empty - no more links found");
    //     }

    //     $this->info("🎉 FINISHED: Processed $processed pages total");
    // }

    public function handle()
    {
        $url = $this->argument('url');
        $siteType = $this->argument('siteType');

        if ($this->currentUrl === null) {
            $this->currentUrl = $url;
        }

        $httpClient = HttpClient::create([
            'timeout' => 15,
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        $client = new Client($httpClient);

        // ⚡ ДОБАВЬТЕ ЭТО ПРЯМО ЗДЕСЬ - ПЕРЕД ПРОВЕРКОЙ ОЧЕРЕДИ
        // ЕСЛИ ОЧЕРЕДЬ ПУСТАЯ ИЛИ МАЛЕНЬКАЯ - ПРИНУДИТЕЛЬНЫЙ СТАРТ
        if (empty($this->queue) || count($this->queue) < 5) {
            $this->forceStartWithRealArticles();
        }

        // Если после принудительного старта очередь все еще пустая, добавляем текущий URL
        if (empty($this->queue)) {
            $this->queue[] = $this->currentUrl;
            $this->info("🚀 Starting with URL: $this->currentUrl");
        }

        $processed = 0;
        $maxPages = 1000000;
        $servicePagesSkipped = 0;

        // ⚡ АГРЕССИВНАЯ ОЧИСТКА ПЕРЕД СТАРТОМ
        $this->aggressiveQueueCleanup();

        while (!empty($this->queue) && $processed < $maxPages) {
            // ⚡ ОЧИСТКА ОЧЕРЕДИ КАЖДЫЕ 3 СТРАНИЦЫ
            if ($processed % 3 === 0) {
                $cleaned = $this->aggressiveQueueCleanup();
                if ($cleaned > 0) {
                    $this->info("🧹 Cleaned $cleaned service pages from queue");
                }
            }

            $nextUrl = array_shift($this->queue);

            // ⚡ СТРОГАЯ ПРОВЕРКА С ОТЛАДКОЙ
            $isService = $this->isStrictlyServicePage($nextUrl);
            if ($isService) {
                $this->info("🚫 SERVICE PAGE SKIPPED: " . $this->getArticleName($nextUrl));
                $this->visited[$nextUrl] = true;
                $this->saveVisitedUrls();
                $servicePagesSkipped++;

                // Если слишком много служебных страниц подряд - экстренная очистка
                if ($servicePagesSkipped >= 10) {
                    $this->info("🔄 EMERGENCY: Too many service pages, aggressive cleanup...");
                    $this->emergencyQueueCleanup();
                    $servicePagesSkipped = 0;
                }
                continue;
            } else {
                $servicePagesSkipped = 0; // Сбрасываем счетчик
            }

            $this->info("🎯 [$processed] Processing: " . $this->getArticleName($nextUrl));

            if ($siteType === 'ruwiki' || $siteType === 'cvruwiki') {
                $this->crawlRuWiki($client, $nextUrl);
            }

            $processed++;

            if ($processed % 10 === 0) {
                $this->info("📊 PROGRESS: $processed pages processed, " . count($this->queue) . " in queue");
            }

            usleep(100000);
        }

        $this->info("🎉 FINISHED: Processed $processed pages total");
    }

    // ⚡ ДОБАВЬТЕ ЭТУ ФУНКЦИЮ В КЛАСС (например, после метода aggressiveQueueCleanup)
    private function forceStartWithRealArticles()
    {
        // ⚡ ПРИНУДИТЕЛЬНО НАЧИНАЕМ С РЕАЛЬНЫХ СТАТЕЙ
        $realArticles = [
            'https://cv.ruwiki.ru/wiki/Шупашкар',
            'https://cv.ruwiki.ru/wiki/Чăваш_Ен',
            'https://cv.ruwiki.ru/wiki/Чăваш_чĕлхи',
            'https://cv.ruwiki.ru/wiki/Вырăс_чĕлхи',
            'https://cv.ruwiki.ru/wiki/Элĕк',
            'https://cv.ruwiki.ru/wiki/Канаш',
            'https://cv.ruwiki.ru/wiki/Çĕнĕ_Шупашкар',
            'https://cv.ruwiki.ru/wiki/Куславкка',
            'https://cv.ruwiki.ru/wiki/Вăрмар',
            'https://cv.ruwiki.ru/wiki/Етĕрне'
        ];

        $this->queue = $realArticles;
        $this->saveState();
        $this->info("🚀 FORCE START: Starting with 10 real articles");

        // ⚡ ПОКАЖЕМ КАКИЕ СТАТЬИ ДОБАВЛЕНЫ
        foreach ($realArticles as $article) {
            $this->info("   📖 " . $this->getArticleName($article));
        }
    }


    private function aggressiveQueueCleanup()
    {
        $initialCount = count($this->queue);
        $newQueue = [];

        foreach ($this->queue as $url) {
            if (!$this->isStrictlyServicePage($url)) {
                $newQueue[] = $url;
            } else {
                $this->visited[$url] = true;
            }
        }

        $this->queue = $newQueue;
        $this->saveVisitedUrls();
        $this->saveState();

        return $initialCount - count($this->queue);
    }

    private function isStrictlyServicePage($url)
    {
        // ⚡ ПРАВИЛЬНАЯ ПРОВЕРКА СЛУЖЕБНЫХ СТРАНИЦ
        $blockedPatterns = [
            'Ятарлă:',
            '%D0%AF%D1%82%D0%B0%D1%80%D0%BB%C4%83%', // URL-encoded Ятарлă:
            'РУВИКИ:',
            '%D0%A0%D0%A3%D0%92%D0%98%D0%9A%D0%98%', // URL-encoded РУВИКИ:
            'Special:',
            'File:',
            'User:',
            'Template:',
            'Help:',
            'Portal:',
            'MediaWiki:',
            'Project:',
            'Обсуждение:',
            'Talk:',
            'w/index.php',
            'action=edit',
            'printable=yes',
            'oldid=',
            'diff=',
            'search=',
            'redirect='
        ];

        foreach ($blockedPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true;
            }
        }

        // Дополнительная проверка: если в пути есть двоеточие после /wiki/ - это служебная страница
        $path = parse_url($url, PHP_URL_PATH);
        if ($path && preg_match('/\/wiki\/[^:]+:/', $path)) {
            return true;
        }

        return false;
    }

    private function cleanQueueFromServicePages()
    {
        $initialCount = count($this->queue);
        $newQueue = [];

        foreach ($this->queue as $url) {
            if (!$this->isServiceOrSpecialPage($url)) {
                $newQueue[] = $url;
            } else {
                $this->visited[$url] = true;
            }
        }

        $this->queue = $newQueue;
        $this->saveVisitedUrls();
        $this->saveState();

        return $initialCount - count($this->queue);
    }

    private function getArticleName($url)
    {
        // Извлекаем читаемое название статьи из URL
        $path = parse_url($url, PHP_URL_PATH);
        if ($path && preg_match('/\/wiki\/(.+)$/', $path, $matches)) {
            return urldecode($matches[1]);
        }
        return basename($url);
    }



    private function isServiceOrSpecialPage($url)
    {
        $servicePatterns = [
            // ЗАПРЕЩЕННЫЕ - реально служебные
            'Special:',
            'File:',
            'User:',
            'Template:',
            'Help:',
            'MediaWiki:',
            'w/index.php',
            'action=edit',
            'printable=yes',
            'oldid=',
            'diff=',
            'search=',
            'redirect='
        ];

        // ⚡ РАЗРЕШЕННЫЕ - навигационные страницы со статьями
        $allowedPatterns = [
            'РУВИКИ:Алфавитлӗ_кӗтартмӗш', // АЛФАВИТНЫЙ УКАЗАТЕЛЬ - РАЗРЕШАЕМ!
            'Ятарлă:Все_страницы', // ВСЕ СТРАНИЦЫ - РАЗРЕШАЕМ!
        ];

        // Если URL содержит разрешенный паттерн - НЕ считаем служебным
        foreach ($allowedPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return false;
            }
        }

        // Проверяем запрещенные паттерны
        foreach ($servicePatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private function cleanQueueFromServiceUrls()
    {
        $cleanQueue = [];
        foreach ($this->queue as $url) {
            if (!$this->isServiceUrl($url)) {
                $cleanQueue[] = $url;
            } else {
                $this->visited[$url] = true;
                $this->info("🧹 Removed service URL from queue: $url");
            }
        }
        $this->queue = $cleanQueue;
        $this->saveVisitedUrls();
    }

    private function isServiceUrl($url)
    {
        $servicePatterns = [
            '/РУВИКИ:/i',
            '/%D0%A0%D0%A3%D0%92%D0%98%D0%9A%D0%98%/i',
            '/Ятарлă:/i',
            '/%D0%AF%D1%82%D0%B0%D1%80%D0%BB%C4%83%/i',
            '/Special:/i',
            '/File:/i',
            '/Category:/i',
            '/User:/i',
            '/Template:/i',
            '/Help:/i',
            '/Portal:/i',
            '/w\/index\.php/i'
        ];

        foreach ($servicePatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        return false;
    }



    // private function crawlRuWiki(Client $client, $url)
    // {
    //     // ⚡ СУПЕР-СТРОГАЯ ПРОВЕРКА В НАЧАЛЕ
    //     if ($this->isStrictlyServicePage($url)) {
    //         $this->info("🚫 STRICT SKIP: " . $this->getArticleName($url));
    //         $this->visited[$url] = true;
    //         $this->saveVisitedUrls();
    //         return;
    //     }

    //     $this->info("🎯 PARSING: " . $this->getArticleName($url));

    //     if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
    //         $this->info("✅ ALREADY VISITED: " . $this->getArticleName($url));
    //         return;
    //     }

    //     $this->visited[$url] = true;
    //     $this->saveVisitedUrls();
    //     $this->currentUrl = $url;
    //     $this->saveState();

    //     // Exclude service pages and special URLs
    //     $excludedSubstrings = [
    //         '#',
    //         'action=edit',
    //         'printable=yes',
    //         'Special:',
    //         'File:',
    //         'Category:',
    //         'User:',
    //         'Template:',
    //         'Help:',
    //         'w/index.php',
    //         'РУВИКИ:',
    //         '%D0%A0%D0%A3%D0%92%D0%98%D0%9A%D0%98%',
    //         'Ятарлă:', // ИСКЛЮЧАЕМ страницы категорий
    //         '%D0%AF%D1%82%D0%B0%D1%80%D0%BB%C4%83%' // ИСКЛЮЧАЕМ URL-encoded категории
    //     ];

    //     foreach ($excludedSubstrings as $substring) {
    //         if (strpos($url, $substring) !== false) {
    //             $this->logOrPrint("⏩ URL with excluded substring found: $url");
    //             return;
    //         }
    //     }

    //     $crawler = $this->requestWithRetry($client, $url);
    //     if ($crawler === null) {
    //         $this->logOrPrint("⏰ Skipped URL due to timeout: $url");
    //         return;
    //     }

    //     // ПАРСИНГ СОДЕРЖИМОГО (это должно быть ПЕРВЫМ)
    //     $text = '';
    //     $content = '';
    //     $title = '';
    //     $page = null;

    //     // Parse title
    //     $titleSelectors = [
    //         '#firstHeading > span',
    //         '.firstHeading',
    //         'h1.firstHeading',
    //         '#firstHeading',
    //         'h1'
    //     ];

    //     foreach ($titleSelectors as $selector) {
    //         if ($crawler->filter($selector)->count() > 0) {
    //             $title = trim($crawler->filter($selector)->text());
    //             $this->info("📖 Found title: $title");
    //             break;
    //         }
    //     }

    //     // Parse content
    //     $contentSelectors = [
    //         '#mw-content-text > div.mw-parser-output > p',
    //         '#mw-content-text > div.mw-parser-output > ul',
    //         '#mw-content-text > div.mw-parser-output > ol',
    //         // '#mw-content-text > div.mw-parser-output > div',
    //         // '#mw-content-text > div.mw-parser-output > table'
    //     ];

    //     $contentFound = false;
    //     foreach ($contentSelectors as $selector) {
    //         if ($crawler->filter($selector)->count() > 0) {
    //             $crawler->filter($selector)->each(function ($node) use (&$text, &$contentFound) {
    //                 $paragraph = trim($node->text());

    //                 if (!empty($paragraph) && strlen($paragraph) > 20 && $this->isValidWikiContent($paragraph)) {
    //                     $text .= $paragraph . PHP_EOL . PHP_EOL;
    //                     $contentFound = true;
    //                 }
    //             });

    //             if ($contentFound) {
    //                 $this->info("✅ Found content with selector: $selector");
    //                 break;
    //             }
    //         }
    //     }

    //     // Save to database if content found
    //     if (!empty($text)) {
    //         try {
    //             ScrapedData::create([
    //                 'url' => $url,
    //                 'page' => $page,
    //                 'title' => $title,
    //                 'author' => '',
    //                 'type' => 'wiki',
    //                 'year' => '',
    //                 'tags' => '',
    //                 'content' => $text
    //             ]);

    //             $this->info("💾 SUCCESS: Saved to database - $title");
    //         } catch (\Exception $e) {
    //             $this->error("❌ Database error: " . $e->getMessage());
    //         }
    //     } else {
    //         $this->warn("⚠️ No content found for: $url");
    //     }

    //     // 🔽 ОГРАНИЧЕННЫЙ СБОР ССЫЛОК (после парсинга)
    //     $this->collectRuWikiLinks($crawler, $url);
    // }

    private function crawlRuWiki(Client $client, $url)
    {
        // ⚡ EMERGENCY SKIP - но РАЗРЕШАЕМ алфавитный указатель
        $problematicUrls = [
            'Ятарлă:Спецстраницы',
            'РУВИКИ:Шырав',
            'Ятарлă:Связанные_правки',
            'Ятарлă:Свежие_правки',
            'Ятарлă:Новые_страницы',
            'РУВИКИ:Канашлу',
            'РУВИКИ:Порталу',
            'РУВИКИ:Пулӗшу'
            // НЕ включаем РУВИКИ:Алфавитлӗ_кӗтартмӗш - это нужно!
        ];

        foreach ($problematicUrls as $problematic) {
            if (strpos($url, $problematic) !== false) {
                $this->info("🚫 EMERGENCY SKIP: $url");
                $this->visited[$url] = true;
                $this->saveVisitedUrls();
                return;
            }
        }

        $this->info("🎯 PARSING: " . $this->getArticleName($url));

        // Check if the URL is already visited or exists in the database
        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        // Exclude service pages and special URLs
        $excludedSubstrings = [
            '#',
            'action=edit',
            'printable=yes',
            'Special:',
            'File:',
            'Category:',
            'User:',
            'Template:',
            'Help:',
            'w/index.php',
            'РУВИКИ:',
            '%D0%A0%D0%A3%D0%92%D0%98%D0%9A%D0%98%',
            'Ятарлă:', // ИСКЛЮЧАЕМ страницы категорий
            '%D0%AF%D1%82%D0%B0%D1%80%D0%BB%C4%83%' // ИСКЛЮЧАЕМ URL-encoded категории
        ];

        foreach ($excludedSubstrings as $substring) {
            if (strpos($url, $substring) !== false) {
                $this->logOrPrint("⏩ URL with excluded substring found: $url");
                return;
            }
        }

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("⏰ Skipped URL due to timeout: $url");
            return;
        }

        // ПАРСИНГ СОДЕРЖИМОГО
        $content = '';
        $title = '';
        $page = null;

        // Parse title
        $titleSelectors = [
            '#firstHeading > span',
            '.firstHeading',
            'h1.firstHeading',
            '#firstHeading',
            'h1'
        ];

        foreach ($titleSelectors as $selector) {
            if ($crawler->filter($selector)->count() > 0) {
                $title = trim($crawler->filter($selector)->text());
                $this->info("📖 Found title: $title");
                break;
            }
        }

        // ⚡ ЗАМЕНИТЕ ЭТОТ БЛОК - парсим контент с заголовками
        $text = $this->extractCvRuWikiContent($crawler);

        // Save to database if content found
        if (!empty($text)) {
            try {
                ScrapedData::create([
                    'url' => $url,
                    'page' => $page,
                    'title' => $title,
                    'author' => '',
                    'type' => 'wiki',
                    'year' => '',
                    'tags' => '',
                    'content' => $text
                ]);

                $this->info("💾 SUCCESS: Saved to database - $title");
            } catch (\Exception $e) {
                $this->error("❌ Database error: " . $e->getMessage());
            }
        } else {
            $this->warn("⚠️ No content found for: $url");
        }

        // 🔽 ОГРАНИЧЕННЫЙ СБОР ССЫЛОК (после парсинга)
        $this->collectRuWikiLinks($crawler, $url);
    }

    private function extractCvRuWikiContent($crawler)
    {
        $text = '';

        // Основной контейнер с контентом
        $mainContainer = '#mw-content-text > div.mw-parser-output';

        if ($crawler->filter($mainContainer)->count() === 0) {
            $this->warn("❌ Main content container not found");
            return '';
        }

        $contentContainer = $crawler->filter($mainContainer);

        // ПАРСИМ ВСЕ ЭЛЕМЕНТЫ ПОСЛЕДОВАТЕЛЬНО
        $contentContainer->filter('*')->each(function ($node) use (&$text) {
            $tagName = $node->nodeName();
            $elementId = $node->attr('id');
            $elementClass = $node->attr('class');

            // ⚡ ОБРАБАТЫВАЕМ ЗАГОЛОВКИ (h1-h6 с ID)
            if (in_array($tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) && !empty($elementId)) {
                $headerText = trim($node->text());
                if (!empty($headerText) && $this->isValidHeader($headerText)) {
                    // Добавляем заголовок с отступом
                    $level = (int) substr($tagName, 1);
                    $indent = str_repeat('#', $level);
                    $text .= PHP_EOL . $indent . ' ' . $headerText . PHP_EOL . PHP_EOL;
                    $this->info("📌 Found header: $headerText (level $level)");
                }
            }

            // ⚡ ОБРАБАТЫВАЕМ ПАРАГРАФЫ
            elseif ($tagName === 'p') {
                $paragraph = trim($node->text());
                if ($this->isValidContentParagraph($paragraph)) {
                    $text .= $paragraph . PHP_EOL . PHP_EOL;
                }
            }

            // ⚡ ОБРАБАТЫВАЕМ СПИСКИ
            elseif ($tagName === 'ul' || $tagName === 'ol') {
                $node->filter('li')->each(function ($liNode) use (&$text) {
                    $listItem = trim($liNode->text());
                    if ($this->isValidContentParagraph($listItem)) {
                        $text .= '• ' . $listItem . PHP_EOL;
                    }
                });
                $text .= PHP_EOL;
            }
        });



        $this->info("📝 Extracted content length: " . strlen($text));
        return $text;
    }

    private function isValidHeader($headerText)
    {
        if (empty($headerText) || strlen($headerText) > 200) {
            return false;
        }

        // Исключаем системные заголовки
        $excludedHeaders = [
            'Навигация',
            'Инструменты',
            'Поиск',
            'Navigation',
            'Tools',
            'Search',
            'Содержание',
            'Contents'
        ];

        foreach ($excludedHeaders as $excluded) {
            if (strpos($headerText, $excluded) !== false) {
                return false;
            }
        }

        return true;
    }


    private function collectRuWikiLinks($crawler, $currentUrl)
    {
        // ⚡ БОЛЬШЕ ССЫЛОК ДЛЯ АЛФАВИТНЫХ УКАЗАТЕЛЕЙ
        $isNavigationPage = (
            strpos($currentUrl, 'РУВИКИ:Алфавитлӗ_кӗтартмӗш') !== false ||
            strpos($currentUrl, 'Ятарлă:Все_страницы') !== false
        );

        $maxLinks = $isNavigationPage ? 500 : 30; // 500 для указателей, 30 для обычных
        $linksCount = 0;

        $crawler->filter('a')->each(function ($node) use ($currentUrl, &$linksCount, $maxLinks, $isNavigationPage) {
            if ($linksCount >= $maxLinks) {
                return;
            }

            $href = $node->attr('href');
            $linkText = trim($node->text());

            if (
                !empty($href) &&
                strpos($href, '#') !== 0 &&
                strpos($href, 'javascript:') !== 0 &&
                strpos($href, 'mailto:') !== 0
            ) {
                $absoluteUrl = $this->makeAbsoluteUrl($href, $currentUrl);

                if ($absoluteUrl && $this->isRuWikiUrl($absoluteUrl)) {
                    // ДЛЯ НАВИГАЦИОННЫХ СТРАНИЦ - МЕНЕЕ СТРОГАЯ ФИЛЬТРАЦИЯ
                    if ($isNavigationPage) {
                        // Разрешаем Category: и другие полезные страницы
                        $isExcluded = (
                            strpos($absoluteUrl, 'action=edit') !== false ||
                            strpos($absoluteUrl, 'printable=yes') !== false ||
                            strpos($absoluteUrl, 'Special:') !== false ||
                            strpos($absoluteUrl, 'User:') !== false ||
                            strpos($absoluteUrl, 'w/index.php') !== false
                        );
                    } else {
                        // Обычная строгая фильтрация
                        $isExcluded = $this->isServiceOrSpecialPage($absoluteUrl);
                    }

                    if (!$isExcluded) {
                        if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
                            $this->queue[] = $absoluteUrl;
                            $linksCount++;

                            if ($isNavigationPage && !empty($linkText)) {
                                $this->info("📚 Navigation link: $linkText");
                            }
                        }
                    }
                }
            }
        });

        $this->saveVisitedUrls();
        if ($linksCount > 0) {
            $type = $isNavigationPage ? "navigation" : "article";
            $this->info("✅ Added $linksCount $type links to queue");
        }
        $this->saveState();
    }


    private function isRuWikiUrl($url)
    {
        try {
            $parsedUrl = parse_url($url);
            if (!isset($parsedUrl['host'])) {
                return false;
            }

            return strpos($parsedUrl['host'], 'cv.ruwiki.ru') !== false;
        } catch (\Exception $e) {
            $this->warn("Error checking domain for $url: " . $e->getMessage());
            return false;
        }
    }

    private function isValidWikiContent($content)
    {
        if (empty($content) || strlen($content) < 10) {
            return false;
        }

        // Exclude citations, navigation, etc.
        $excludedPatterns = [
            '/^\[\d+\]$/', // citations [1], [2]
            '/^редактировать$/i',
            '/^править$/i',
            '/^навигация$/i',
            '/^категории$/i',
            '/^страницы$/i',
            '/^перейти к навигации$/i',
            '/^перейти к поиску$/i',
        ];

        foreach ($excludedPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        return true;
    }

    private function isNavigationOrCitation($content)
    {
        $navigationPatterns = [
            '/^\d+\.\d+\.\d+$/', // dates
            '/^#/',
            '/^\[\w+\]$/',
            '/^edit$/i',
            '/^source$/i',
            '/^view history$/i',
        ];

        foreach ($navigationPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }




    //   мне надо всего лишь запарсить страницы с  https://www.chuvash.org/blogs/2.html по https://www.chuvash.org/blogs/387.html 
    //   на которых не надо вытаскивать текст а надо искать ссылки на этих страницах вида https://www.chuvash.org/blogs/comments/41.html 
    //   ТО есть надо искать текст только на страницах типа https://www.chuvash.org/blogs/comments/41.html 
    //   Вот мой предыдущий метод в котором это работало. Надо сделать так же как в следующем примере (здесь страницы с https://www.chuvash.org/a/news/2.html по https://www.chuvash.org/a/news/4028.html - это страницы для поиска страниц типа https://www.chuvash.org/news/9.html) - 




    private function requestWithRetry(Client $client, $url, $retries = 3)
    {
        for ($i = 0; $i < $retries; $i++) {
            try {
                return $client->request('GET', $url);
            } catch (\Symfony\Component\HttpClient\Exception\TimeoutException $e) {
                if ($i == $retries - 1) {
                    file_put_contents(storage_path('timeout_urls.txt'), $url . PHP_EOL, FILE_APPEND);
                    return null;
                }
                sleep(2);
            }
        }
        return null;
    }




    // private function crawlChuvashBlogs(Client $client, $url)
    // {
    //     $this->info("Crawling URL: $url");

    //     // ПРОВЕРКА НА ИСКЛЮЧАЕМЫЕ ПАТТЕРНЫ

    //     if (
    //         strpos($url, '/news/tags/') !== false ||
    //         strpos($url, '/news/tema/') !== false ||
    //         strpos($url, 'chuvash.org/wiki/') !== false
    //     ) {
    //         $this->info("⏩ URL contains excluded pattern, skipping: $url");
    //         return;
    //     }


    //     // ПРИОРИТЕТНАЯ ОБРАБОТКА: если это страница со списком новостей (/a/news/)
    //     if (preg_match('/\/a\/news\/\d+\.html$/', $url)) {
    //         // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: если уже посещали, не обрабатываем снова
    //         if (isset($this->visited[$url])) {
    //             $this->info("⏩ News list already visited, skipping: $url");
    //             return;
    //         }
    //         $this->info("🚀 PRIORITY: News list page detected, fast processing");
    //         return $this->processNewsListPage($client, $url);
    //     }

    //     if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
    //         $this->info("URL already visited: $url");
    //         return;
    //     }

    //     $this->visited[$url] = true;
    //     $this->saveVisitedUrls();
    //     $this->currentUrl = $url;
    //     $this->saveState();

    //     $this->info("Making request to: $url");
    //     $crawler = $this->requestWithRetry($client, $url);
    //     if ($crawler === null) {
    //         $this->logOrPrint("Skipped URL due to timeout: $url");
    //         return;
    //     }

    //     $this->info("Successfully received page content");

    //     // ОПРЕДЕЛЕНИЕ ТИПА СТРАНИЦЫ
    //     $isArticlePage = false;
    //     if (strpos($url, 'chuvash.org/news/') !== false) {
    //         $isArticlePage = true;
    //         $this->info("This is an ARTICLE page");
    //     } else {
    //         $this->info("This is INDEX/ARCHIVE page, collecting links only");
    //         // Для не-статей только собираем ссылки
    //         $this->collectLinksFromPage($crawler);
    //         return;
    //     }

    //     $text = '';
    //     $title = '';
    //     $articleType = '';
    //     $publishDate = '';
    //     $author = '';
    //     $year = '';

    //     // ПАРСИНГ СТАТЬИ
    //     try {
    //         // Заголовок
    //         $titleSelector = 'body > div.varnelle > div > div.area2 > div > h2';
    //         if ($crawler->filter($titleSelector)->count() > 0) {
    //             $title = trim($crawler->filter($titleSelector)->text());
    //             $this->info("Found title: $title");
    //         } else {
    //             $this->warn("Title not found with selector: $titleSelector");
    //         }

    //         // Автор
    //         $author = $this->findAuthorblogs($crawler);

    //         // Дата и год
    //         $yearData = $this->findDateAndYearblogs($crawler);
    //         $year = $yearData['year'];
    //         $publishDate = $yearData['date'];

    //         // Тип/категория
    //         $articleType = $this->findArticleTypeblogs($crawler);

    //         // Контент
    //         $text = $this->findContentblogs($crawler);
    //     } catch (\Exception $e) {
    //         $this->error("Error parsing article: " . $e->getMessage());
    //         Log::error('Parsing error for Chuvashnews', [
    //             'url' => $url,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //     }

    //     // СОХРАНЕНИЕ В БАЗУ ДАННЫХ
    //     $this->saveArticleToDatabase($url, $title, $author, $articleType, $year, $text);

    //     // СБОР ССЫЛОК С СТРАНИЦЫ
    //     $this->collectLinksFromPage($crawler);
    // }
    // findAuthor findDateAndYear findArticleType findContent

    private function crawlChuvashblogs(Client $client, $url)
    {
        $this->info("Crawling URL: $url");

        // ПРОВЕРКА НА ИСКЛЮЧАЕМЫЕ ПАТТЕРНЫ
        $excludedPatterns = [
            '/news/tags/',
            '/news/tema/',
            'chuvash.org/lib/',
            'chuvash.org/video/',
            'chuvash.org/gallery/',
            'chuvash.org/news/',
            'chuvash.org/calendar/',
            'chuvash.org/blogs/tags/',
            'chuvash.org/a/news/',
            'chuvash.org/cgi-bin/',
            'chuvash.org/files/',
            'chuvash.org/wiki/',
        ];

        foreach ($excludedPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                $this->info("⏩ URL contains excluded pattern '$pattern', skipping: $url");
                return;
            }
        }


        // ПРИОРИТЕТНАЯ ОБРАБОТКА: если это страница со списком блогов (/blogs/)
        if (preg_match('/\/blogs\/\d+\.html$/', $url)) {
            // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: если уже посещали, не обрабатываем снова
            if (isset($this->visited[$url])) {
                $this->info("⏩ Blogs list already visited, skipping: $url");
                return;
            }
            $this->info("🚀 PRIORITY: Blogs list page detected, fast processing");
            return $this->processBlogsListPage($client, $url);
        }

        // ПРИОРИТЕТНАЯ ОБРАБОТКА: если это страница комментариев (/blogs/comments/)
        if (preg_match('/\/blogs\/comments\/\d+\.html$/', $url)) {
            // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: если уже посещали, не обрабатываем снова
            if (isset($this->visited[$url])) {
                $this->info("⏩ Comments page already visited, skipping: $url");
                return;
            }
            $this->info("🚀 PRIORITY: Comments page detected, processing content");
            return $this->processCommentsPage($client, $url);
        }

        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $this->info("Making request to: $url");
        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped URL due to timeout: $url");
            return;
        }

        $this->info("Successfully received page content");

        // Для всех остальных страниц только собираем ссылки
        $this->info("This is INDEX/ARCHIVE page, collecting links only");
        $this->collectLinksFromPage($crawler);
    }


    private function processBlogsListPage(Client $client, $url)
    {
        $this->info("🚀 FAST PROCESSING blogs list: $url");

        // ПРОВЕРКА: если уже посещали эту страницу списка, пропускаем
        if (isset($this->visited[$url])) {
            $this->info("Blogs list already processed: $url");
            return;
        }

        // ПОМЕЧАЕМ КАК ПОСЕЩЕННУЮ ПЕРЕД ОБРАБОТКОЙ
        $this->visited[$url] = true;
        $this->saveVisitedUrls();

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped blogs list due to timeout: $url");
            return;
        }

        $commentsLinksCount = 0;

        // БЫСТРЫЙ ПОИСК ССЫЛОК НА КОММЕНТАРИИ
        $crawler->filter('a')->each(function ($node) use (&$commentsLinksCount) {
            $href = $node->attr('href');

            if (!empty($href) && preg_match('/\/blogs\/comments\/\d+\.html$/', $href)) {
                $absoluteUrl = $this->makeAbsoluteUrl($href, $this->currentUrl);

                if ($absoluteUrl && $this->isSameDomain($absoluteUrl)) {
                    // ПРОВЕРЯЕМ, ЧТО ЭТО ИМЕННО СТРАНИЦА КОММЕНТАРИЕВ
                    if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
                        // ДОБАВЛЯЕМ В НАЧАЛО ОЧЕРЕДИ для приоритетной обработки
                        array_unshift($this->queue, $absoluteUrl);
                        $commentsLinksCount++;
                        $this->info("✅ Found comments page: $absoluteUrl");
                    }
                }
            }
        });

        $this->info("✅ Added $commentsLinksCount comments links to queue from: $url");
        $this->saveState();

        // Также собираем другие ссылки для продолжения обхода
        $this->collectLinksFromPage($crawler);
    }

    private function processCommentsPage(Client $client, $url)
    {
        $this->info("🚀 PROCESSING comments page: $url");

        // ПРОВЕРКА: если уже посещали эту страницу комментариев, пропускаем
        if (isset($this->visited[$url])) {
            $this->info("Comments page already processed: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped comments page due to timeout: $url");
            return;
        }

        $text = '';
        $title = '';
        $articleType = 'blog_comment';
        $publishDate = '';
        $author = '';
        $year = '';

        // ПАРСИНГ СТРАНИЦЫ КОММЕНТАРИЕВ
        try {
            // Заголовок (если есть)
            $titleSelector = 'body > div.varnelle > div > div.area2 > div > h2, body > div.varnelle > div > div.area2 > div > h1';
            if ($crawler->filter($titleSelector)->count() > 0) {
                $title = trim($crawler->filter($titleSelector)->text());
                $this->info("Found title: $title");
            } else {
                $this->warn("Title not found with selector: $titleSelector");
                $title = 'Blog Comment - ' . $url;
            }

            // Автор
            $author = $this->findAuthorblogs($crawler);

            // Дата и год
            $yearData = $this->findDateAndYearblogs($crawler);
            $year = $yearData['year'];
            $publishDate = $yearData['date'];

            // Контент
            $text = $this->findContentblogs($crawler);
        } catch (\Exception $e) {
            $this->error("Error parsing comments page: " . $e->getMessage());
            Log::error('Parsing error for Chuvash blogs comments', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // СОХРАНЕНИЕ В БАЗУ ДАННЫХ
        $this->saveArticleToDatabase($url, $title, $author, $articleType, $year, $text);

        // СБОР ССЫЛОК С СТРАНИЦЫ (может быть полезно для навигации)
        $this->collectLinksFromPage($crawler);
    }



    private function findAuthorblogs($crawler)
    {
        $authorSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > b > a > b',
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > b > a',
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) a b',
            '.chblocktext_yellow b a',
            '.chblocktext_yellow a b',
            'div.chblocktext_yellow a[href*="author"]',
            'div.chblocktext_yellow b',
        ];

        foreach ($authorSelectors as $authorSelector) {
            if ($crawler->filter($authorSelector)->count() > 0) {
                $author = trim($crawler->filter($authorSelector)->text());
                if (!empty($author) && strlen($author) < 100 && $this->looksLikeAuthorName($author)) {
                    $this->info("✅ Found author with selector '$authorSelector': $author");
                    return $author;
                }
            }
        }

        $this->warn("❌ Author not found with any selector");
        return '';
    }

    private function findDateAndYearblogs($crawler)
    {
        $dateSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > span',
            '.chblocktext_yellow div:nth-child(3) span',
        ];

        foreach ($dateSelectors as $dateSelector) {
            if ($crawler->filter($dateSelector)->count() > 0) {
                $rawDateText = trim($crawler->filter($dateSelector)->text());
                $this->info("🔍 Raw date text: '$rawDateText'");

                if ($this->looksLikeDate($rawDateText)) {
                    $publishDate = $rawDateText;
                    $year = $this->extractYearFromDate($publishDate);
                    $this->info("✅ Found REAL date: $publishDate -> Year: $year");
                    return ['date' => $publishDate, 'year' => $year];
                }
            }
        }

        // Fallback: поиск года в тексте страницы
        $pageText = $crawler->text();
        if (preg_match('/\b(20\d{2})\b/', $pageText, $matches)) {
            $year = $matches[1];
            $this->info("✅ Found year in page text: $year");
            return ['date' => '', 'year' => $year];
        }

        $year = date('Y');
        $this->info("⚠️ Year not found, using current: $year");
        return ['date' => '', 'year' => $year];
    }

    private function findArticleTypeblogs($crawler)
    {
        // Для блогов тип обычно "blog" или можно определить по структуре
        return 'blog';
    }

    // private function findContentblogs($crawler)
    // {
    //     $text = '';

    //     // СЕЛЕКТОРЫ ДЛЯ КОНТЕНТА БЛОГОВ
    //     $contentSelectors = [

    //         // не понимаю почему программа не находит content например на странице https://www.chuvash.org/blogs/comments/2348.html
    //         // в котором content находится в селекторах -  
    //         // 'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex > p:nth-child(1)',
    //         // 'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex > p:nth-child(2)',
    //         // 'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex > p:nth-child(3)' и так далее 

    //         'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex',
    //         'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog',
    //         '.hipar_text',
    //         'div.hipar_text',
    //         '.chblocktext_yellow .hipar_text',
    //         'div.chblocktext_yellow div.hipar_text'
    //     ];

    //     $contentContainer = null;
    //     $usedSelector = '';

    //     foreach ($contentSelectors as $selector) {
    //         if ($crawler->filter($selector)->count() > 0) {
    //             $contentContainer = $crawler->filter($selector);
    //             $usedSelector = $selector;
    //             $this->info("✅ Found content container with selector: $selector");
    //             break;
    //         }
    //     }

    //     if ($contentContainer === null) {
    //         $this->warn("❌ Content container not found with any selector");
    //         return '';
    //     }

    //     // ПАРСИМ КОНТЕНТ ИЗ ПАРАГРАФОВ
    //     $paragraphs = $contentContainer->filter('p:not(.news_tags):not([class*="tag"]):not([class*="meta"])');

    //     if ($paragraphs->count() > 0) {
    //         $this->info("Found {$paragraphs->count()} content paragraphs");

    //         $paragraphs->each(function ($node) use (&$text) {
    //             $paragraph = trim($node->text());

    //             // ФИЛЬТРАЦИЯ ПАРАГРАФОВ
    //             if ($this->isValidContentParagraph($paragraph)) {
    //                 $text .= $paragraph . PHP_EOL . PHP_EOL;
    //                 $this->info("✅ Added paragraph: " . substr($paragraph, 0, 50) . "...");
    //             } else {
    //                 $this->info("⏩ Skipped paragraph: " . substr($paragraph, 0, 50) . "...");
    //             }
    //         });
    //     } else {
    //         $this->warn("No paragraphs found, trying to get all text from container");
    //         $text = trim($contentContainer->text());
    //     }

    //     $this->info("Final content length: " . strlen($text));
    //     return $text;
    // }




    private function findContentblogs($crawler)
    {
        $text = '';

        // ОБНОВЛЕННЫЕ СЕЛЕКТОРЫ ДЛЯ КОНТЕНТА БЛОГОВ (комментариев)
        $contentSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex',
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog',
            '.blog noindex',
            '.blog',
            'div.blog noindex',
            'div.blog'
        ];

        $contentContainer = null;
        $usedSelector = '';

        foreach ($contentSelectors as $selector) {
            if ($crawler->filter($selector)->count() > 0) {
                $contentContainer = $crawler->filter($selector);
                $usedSelector = $selector;
                $this->info("✅ Found content container with selector: $selector");
                break;
            }
        }

        if ($contentContainer === null) {
            $this->warn("❌ Content container not found with any selector");

            // ДОПОЛНИТЕЛЬНАЯ ОТЛАДКА: покажем все доступные селекторы
            $this->debugAvailableSelectors($crawler);
            return '';
        }

        // ОТЛАДКА: покажем структуру найденного контейнера
        $this->debugContainerStructure($contentContainer, $usedSelector);

        // ПАРСИМ КОНТЕНТ ИЗ ПАРАГРАФОВ внутри noindex или напрямую
        $paragraphs = $contentContainer->filter('p');

        if ($paragraphs->count() > 0) {
            $this->info("Found {$paragraphs->count()} content paragraphs");

            $paragraphs->each(function ($node) use (&$text) {
                $paragraph = trim($node->text());

                // ФИЛЬТРАЦИЯ ПАРАГРАФОВ (менее строгая для блогов)
                if ($this->isValidBlogContentParagraph($paragraph)) {
                    $text .= $paragraph . PHP_EOL . PHP_EOL;
                    $this->info("✅ Added paragraph: " . substr($paragraph, 0, 50) . "...");
                } else {
                    $this->info("⏩ Skipped paragraph: " . substr($paragraph, 0, 50) . "...");
                }
            });
        } else {
            $this->warn("No paragraphs found, trying to get all text from container");
            $text = trim($contentContainer->text());

            // Если текст слишком короткий, возможно контент в другом месте
            if (strlen($text) < 50) {
                $this->warn("Text too short, trying alternative selectors");
                $text = $this->tryAlternativeSelectors($crawler);
            }
        }

        $this->info("Final content length: " . strlen($text));
        return $text;
    }

    // ДОПОЛНИТЕЛЬНЫЙ МЕТОД ДЛЯ АЛЬТЕРНАТИВНЫХ СЕЛЕКТОРОВ
    private function tryAlternativeSelectors($crawler)
    {
        $text = '';

        // Попробуем найти контент по конкретным селекторам параграфов
        $specificSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex > p',
            'div.blog noindex p',
            '.chblocktext_yellow .blog p',
            'div.chblocktext_yellow div.blog p'
        ];

        foreach ($specificSelectors as $selector) {
            if ($crawler->filter($selector)->count() > 0) {
                $this->info("✅ Found content with specific selector: $selector");
                $crawler->filter($selector)->each(function ($node) use (&$text) {
                    $paragraph = trim($node->text());
                    if ($this->isValidBlogContentParagraph($paragraph)) {
                        $text .= $paragraph . PHP_EOL . PHP_EOL;
                    }
                });
                break;
            }
        }

        return $text;
    }

    // ОТЛАДОЧНЫЙ МЕТОД ДЛЯ ПОИСКА ВСЕХ ВОЗМОЖНЫХ СЕЛЕКТОРОВ
    private function debugAvailableSelectors($crawler)
    {
        $this->info("🔍 DEBUG: Available containers on page:");

        $possibleContainers = [
            '.blog',
            '.chblocktext_yellow',
            '.area2',
            'noindex',
            'div[class*="blog"]',
            'div[class*="content"]',
            'div[class*="text"]'
        ];

        foreach ($possibleContainers as $container) {
            if ($crawler->filter($container)->count() > 0) {
                $count = $crawler->filter($container)->count();
                $this->info("   - $container: found $count elements");

                // Покажем немного текста из первого элемента
                $firstElement = $crawler->filter($container)->first();
                $sampleText = trim($firstElement->text());
                if (!empty($sampleText)) {
                    $this->info("     Sample: " . substr($sampleText, 0, 100) . "...");
                }
            }
        }
    }

    // ОБНОВЛЕННЫЙ МЕТОД ФИЛЬТРАЦИИ ДЛЯ БЛОГОВ (менее строгий)
    private function isValidBlogContentParagraph($paragraph)
    {
        if (empty($paragraph) || strlen($paragraph) < 5) {
            return false;
        }

        // ИСКЛЮЧАЕМ МЕТАДАННЫЕ (более специфично для блогов)
        $excludedPatterns = [
            '/^\d{2}\.\d{2}\.\d{2,4}/', // даты
            '/^[А-Яа-яё]+\s+[А-Яа-яё]+$/u', // только ФИО
            '/^#\w+/', // хештеги
            '/тег|tag|автор|author|категория|category|просмотр|view|комментар|comment/i',
            '/^\s*[,\\.\\-\\s]*\s*$/', // только пунктуация
            '/^https?:\/\//', // URL
        ];

        foreach ($excludedPatterns as $pattern) {
            if (preg_match($pattern, $paragraph)) {
                return false;
            }
        }

        // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: должен содержать нормальные слова
        if (preg_match('/\b[А-Яа-яё]+\b/u', $paragraph) === 0) {
            return false;
        }

        return true;
    }


    private function debugContainerStructure($container, $selector)
    {
        $this->info("🔍 DEBUG Container structure for: $selector");

        // Покажем все дочерние элементы
        $container->filter('*')->each(function ($node) {
            $tagName = $node->nodeName();
            $class = $node->attr('class') ?: 'no-class';
            $text = trim($node->text());

            if (!empty($text)) {
                $this->info("   - {$tagName}.{$class}: " . substr($text, 0, 80) .
                    (strlen($text) > 80 ? '...' : ''));
            }
        });
    }

    private function isValidContentParagraph($paragraph)
    {
        if (empty($paragraph) || strlen($paragraph) < 10) {
            return false;
        }

        // ИСКЛЮЧАЕМ МЕТАДАННЫЕ
        $excludedPatterns = [
            '/^\d{2}\.\d{2}\.\d{4}/', // даты
            '/^[А-Яа-яё]+\s+[А-Яа-яё]+$/u', // только ФИО
            '/^#\w+/', // хештеги
            '/тег|tag|автор|author|категория|category|просмотр|view/i',
            '/Сутатăп/', // исключаем этот текст
            '/^\s*[,\\.\\-\\s]*\s*$/', // только пунктуация
        ];

        foreach ($excludedPatterns as $pattern) {
            if (preg_match($pattern, $paragraph)) {
                return false;
            }
        }

        // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: должен содержать нормальные слова
        if (preg_match('/\b[А-Яа-яё]+\b/u', $paragraph) === 0) {
            return false;
        }

        return true;
    }

    private function saveArticleToDatabase($url, $title, $author, $articleType, $year, $text)
    {
        if (!empty($text)) {
            try {
                ScrapedData::create([
                    'url' => $url,
                    'page' => null,
                    'title' => $title,
                    'author' => $author,
                    'type' => $articleType,
                    'year' => $year ?? '',
                    'tags' => '',
                    'content' => $text
                ]);
                $this->info("✅ Saved to DATABASE: $url");
            } catch (\Exception $e) {
                Log::error('Database Insert Error for Chuvashnews', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                $this->error("❌ Database insert error: " . $e->getMessage());
            }
        } else {
            $this->info("❌ No content found for article: $url");
            // Логирование в файл
            try {
                $logMessage = date('Y-m-d H:i:s') . " - No content found: " . $url . PHP_EOL;
                file_put_contents(storage_path('no_content_urls.txt'), $logMessage, FILE_APPEND | LOCK_EX);
                $this->info("📝 URL logged to no_content_urls.txt: $url");
            } catch (\Exception $e) {
                $this->warn("Could not write to no_content_urls.txt: " . $e->getMessage());
            }
        }
    }

    private function collectLinksFromPage($crawler)
    {
        $crawler->filter('a')->each(function ($node) {
            $href = $node->attr('href');
            if (!empty($href) && strpos($href, '#') !== 0 && strpos($href, 'javascript:') !== 0) {
                $absoluteUrl = $this->makeAbsoluteUrl($href, $this->currentUrl);
                if ($absoluteUrl && $this->isSameDomain($absoluteUrl)) {
                    if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
                        $this->queue[] = $absoluteUrl;
                        $this->saveState();
                    }
                }
            }
        });
    }

    private function processNewsListPage(Client $client, $url)
    {
        $this->info("🚀 FAST PROCESSING news list: $url");

        // ПРОВЕРКА: если уже посещали эту страницу списка, пропускаем
        if (isset($this->visited[$url])) {
            $this->info("News list already processed: $url");
            return;
        }

        // ПОМЕЧАЕМ КАК ПОСЕЩЕННУЮ ПЕРЕД ОБРАБОТКОЙ
        $this->visited[$url] = true;
        $this->saveVisitedUrls();

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped news list due to timeout: $url");
            return;
        }

        $newsCount = 0;

        // БЫСТРЫЙ ПОИСК ССЫЛОК НА НОВОСТИ
        $crawler->filter('a')->each(function ($node) use (&$newsCount) {
            $href = $node->attr('href');

            if (!empty($href) && preg_match('/\/news\/\d+\.html$/', $href)) {
                $absoluteUrl = $this->makeAbsoluteUrl($href, $this->currentUrl);

                if ($absoluteUrl && $this->isSameDomain($absoluteUrl)) {
                    // ПРОВЕРЯЕМ, ЧТО ЭТО ИМЕННО СТРАНИЦА НОВОСТИ, А НЕ СПИСОК
                    if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
                        // ДОБАВЛЯЕМ В НАЧАЛО ОЧЕРЕДИ для приоритетной обработки
                        array_unshift($this->queue, $absoluteUrl);
                        $newsCount++;
                    }
                }
            }
        });

        $this->info("✅ Added $newsCount news links to queue from: $url");
        $this->saveState();

        // УБЕДИТЕЛЬНАЯ ПРОВЕРКА: логируем, что страница списка обработана
        $this->info("📝 News list page COMPLETED: $url");
    }

    private function looksLikeDate($text)
    {
        if (empty($text) || strlen($text) > 100) {
            return false;
        }

        $text = trim($text);

        // Паттерны для дат
        $datePatterns = [
            '/^\d{2}\.\d{2}\.\d{4}\s+\d{2}:\d{2}$/', // 20.10.2025 12:09
            '/^\d{2}\.\d{2}\.\d{4}$/',               // 20.10.2025
            '/\d{2}\.\d{2}\.\d{4}/',                 // содержит дату
            '/\d{4}-\d{2}-\d{2}/',                   // 2025-10-20
            '/\d{1,2}\s+[а-яё]+\s+\d{4}/iu',         // 20 октября 2025
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        // Исключаем известные не-даты
        $excludedTexts = ['Сутатăп', 'Продается', 'Реклама', 'Advertisement'];
        foreach ($excludedTexts as $excluded) {
            if (strpos($text, $excluded) !== false) {
                return false;
            }
        }

        return false;
    }

    private function logOrPrint($message)
    {
        $this->info($message);
    }

    private function isSameDomain($url)
    {
        try {
            $parsedUrl = parse_url($url);
            $parsedBaseUrl = parse_url($this->argument('url'));

            if (!isset($parsedUrl['host']) || !isset($parsedBaseUrl['host'])) {
                return false;
            }

            // Нормализуем домены (убираем www)
            $urlHost = preg_replace('/^www\./', '', $parsedUrl['host']);
            $baseHost = preg_replace('/^www\./', '', $parsedBaseUrl['host']);

            return $urlHost === $baseHost;
        } catch (\Exception $e) {
            $this->warn("Error checking domain for $url: " . $e->getMessage());
            return false;
        }
    }


    // НОВАЯ ФУНКЦИЯ: Проверка на наличие чувашских символов
    private function containsChuvashCharacters($text)
    {
        if (empty($text)) {
            return false;
        }

        // Чувашские специфические символы
        $chuvashChars = ['ĕ', 'ă', 'ç', 'ÿ', 'Ĕ', 'Ă', 'Ç', 'Ÿ'];
        foreach ($chuvashChars as $char) {
            if (strpos($text, $char) !== false) {
                return true;
            }
        }

        return false;
    }


    private function crawlChuvash(Client $client, $url)
    {
        $this->info("Crawling URL: $url");

        // Check if the URL is already visited or exists in the database
        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $excludedSubstrings = ['#', '/stat/', '/s0/', '/s1/', '/s2/', '/s3/', '/s4/', '/s5/'];
        foreach ($excludedSubstrings as $substring) {
            if (strpos($url, $substring) !== false) {
                $this->logOrPrint("URL with excluded substring found: $url");
                return;
            }
        }

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped URL due to timeout: $url");
            return;
        }

        $text = '';
        $content = '';

        $page = null;
        if (preg_match('/\.([0-9]+)\.html$/', $url, $matches)) {
            $page = $matches[1];
        }

        $titleSelector = '';
        $authorSelector = '';
        $typeSelector = '';
        $yearSelector = '';
        $tagSelector = [];

        if (strpos($url, '/author/') !== false) {
            $cssSelectors = [
                '#posts > div > div > div.author_teple'
            ];
        } elseif (strpos($url, '/haylav/') !== false) {
            $cssSelectors = [
                '#posts > div:nth-child(1) > div.story'
            ];
            $metaNode = $crawler->filter('#posts > div:nth-child(1) > div.meta');
            $titleNode = $crawler->filter('#posts > div.post > h2');

            if ($titleNode->count() > 0) {
                $titleSelector = $titleNode->text();
            }

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
            $cssSelectors = [
                '#posts > div > div > div',
                '#posts > div > div',
            ];
        }

        foreach ($cssSelectors as $selector) {
            $node = $crawler->filter($selector);
            if ($node->count() > 0) {
                $node->children()->each(function ($childNode) use (&$text) {
                    if (!$childNode->matches('ul')) {
                        $text .= $childNode->text() . PHP_EOL;
                    }
                });
            }
        }

        if (!empty($text)) {
            $this->logOrPrint("Scraped URL: $url");
            $this->logOrPrint($text);

            $content .= $text . PHP_EOL;

            try {
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
                throw $e;
            }
        } else {
            $this->logOrPrint("No text found at URL: $url");
        }

        $crawler->filter('a')->each(function ($node) use ($client) {
            $link = $node->link();
            $url = $link->getUri();

            if ($this->isSameDomain($url) && !isset($this->visited[$url]) && !ScrapedData::where('url', $url)->exists()) {
                $this->queue[] = $url;
                $this->saveState();
            }
        });
    }


    private function crawlHypar(Client $client, $url)
    {
        $this->info("Crawling URL: $url");

        // Check if the URL is already visited or exists in the database
        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $excludedSubstrings = ['#', '//hypar.ru/ru/', '/cv/tegi/', '/files/pictures/', '/cv/avtory/', '/cv/video', '/cv/node/'];
        foreach ($excludedSubstrings as $substring) {
            if (strpos($url, $substring) !== false) {
                $this->logOrPrint("URL with excluded substring found: $url");
                return;
            }
        }

        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped URL due to timeout: $url");
            return;
        }

        $text = '';
        $content = '';

        $page = null;

        $titleSelector = '';
        $authorSelector = '';
        $typeSelector = '';
        $yearSelector = '';
        $tagSelector = '';   //  #component > div > div.news_text

        if (strpos($url, '/cv/izdaniya/') !== false) {
            $cssSelectors = [
                '#block-system-main > div > div.content.node-newsmolgaz > div.field.field-name-body.field-type-text-with-summary.field-label-hidden > div',
                '#block-system-main > div > div.content.node-news > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', // http://hypar.ru/cv/izdaniya/hypar/elektrosamokatpa-cula-tuhar-i
                '#block-system-main > div > div.content.node-newstan > div.field.field-name-body.field-type-text-with-summary.field-label-hidden',
                '#block-system-main > div > div.content.node-newsch > div.field.field-name-body.field-type-text-with-summary.field-label-hidden > div > div',
                '#block-system-main > div > div.content.node-newshrs > div.field.field-name-body.field-type-text-with-summary.field-label-hidden',
            ];
            $titleSelector = '#post-content > h1';
            $yearSelector = '#block-system-main > div > div.news-date';
            $tagSelector = '#block-system-main > div > div.content.node-newsnews > div.field.field-name-field-newsterm-rubric.field-type-taxonomy-term-reference.field-label-inline.clearfix';
            $authorSelector = '#block-system-main > div > div.content.node-newsmolgaz > div.field.field-name-field-author.field-type-taxonomy-term-reference.field-label-above';
        } elseif (strpos($url, '/cv/news/') !== false) {
            $cssSelectors = [
                '#block-system-main > div > div.content.node-newsnews > div.field.field-name-body.field-type-text-with-summary.field-label-hidden > div',
            ];

            $titleSelector = '#post-content > h1';
            $yearSelector = '#block-system-main > div > div.news-date';
            $tagSelector = '#block-system-main > div > div.content.node-newsnews > div.field.field-name-field-newsterm-rubric.field-type-taxonomy-term-reference.field-label-inline.clearfix';
            $authorSelector = '#block-system-main > div > div.content.node-newsnews > div.field.field-name-field-author.field-type-taxonomy-term-reference.field-label-inline.clearfix > div.field-items > div';
        } else {

            $cssSelectors = [
                '#block-system-main > div > div.content.node-digest > div',
                //'#block-system-main > div > div.content.node-news > div.field.field-name-body.field-type-text-with-summary.field-label-hidden > div > div',  // http://hypar.ru/cv/tippe-yulsan-cal-kuc-calat
                '#block-system-main > div > div.content.node-news > div.field.field-name-body.field-type-text-with-summary.field-label-hidden > div',   // http://hypar.ru/cv/payanhi-camraksem-akalchan-tata-kitay-chelhisene-verenesshen
                '#block-system-main > div > div.content.node-newsnews > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', // http://hypar.ru/cv/urah-yat-hurasche
                '#block-system-main > div > div.content.node-newsmolgaz > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', //http://hypar.ru/cv/telekre-te-scena-cinche-vylyat
                '#block-system-main > div > div.content.node-newshrs > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', // http://hypar.ru/cv/menle-cher-chun-shi
                '#block-system-main > div > div.content.node-newsch > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', // http://hypar.ru/cv/purnac-parneleneshen-tavah
                '#block-system-main > div > div.content.node-newstan > div.field.field-name-body.field-type-text-with-summary.field-label-hidden', //http://hypar.ru/cv/yalan-kulakansem-numay-puranacce
                //  '#block-system-main > div > div > div > div',       // http://hypar.ru/cv/tavan-kultura-raccey-kulturin-chere-teprencheke-viktorina      



            ];
            $titleSelector = '#post-content > h1';
            $yearSelector =  '#block-system-main > div > div.news-date'; // http://hypar.ru/cv/tippe-yulsan-cal-kuc-calat
            $tagSelector =   '#block-system-main > div > div.content.node-news > div.field.field-name-field-newsterm-tags.field-type-taxonomy-term-reference.field-label-hidden > div > div > a';
            $authorSelector = '#block-system-main > div > div.content.node-news > div.field.field-name-field-author.field-type-taxonomy-term-reference.field-label-inline.clearfix > div.field-items > div > a';
        }

        foreach ($cssSelectors as $selector) {
            $node = $crawler->filter($selector);
            if ($node->count() > 0) {
                $node->children()->each(function ($childNode) use (&$text) {
                    if (!$childNode->matches('ul')) {
                        $text .= $childNode->text() . PHP_EOL;
                    }
                });
            }
        }

        if (!empty($text)) {
            $this->logOrPrint("Scraped URL: $url");
            $this->logOrPrint($text);

            $content .= $text . PHP_EOL;

            $title = $crawler->filter($titleSelector)->count() ? $crawler->filter($titleSelector)->text() : '';
            $year = $crawler->filter($yearSelector)->count() ? $crawler->filter($yearSelector)->text() : '';
            $author = $crawler->filter($authorSelector)->count() ? $crawler->filter($authorSelector)->text() : '';
            $tags = $crawler->filter($tagSelector)->each(function ($node) {
                return $node->text();
            });

            try {
                $data = [
                    'url' => $url,
                    'page' => $page,
                    'title' => $title,
                    'author' => $author,
                    'type' => $typeSelector,
                    'year' => $year,
                    'tags' => implode(', ', $tags),
                    'content' => $content
                ];

                // $dataString = json_encode($data, JSON_PRETTY_PRINT);
                // $dataString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $dataString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                file_put_contents(storage_path('scraped_data.txt'), $dataString . PHP_EOL, FILE_APPEND);
            } catch (\Exception $e) {
                Log::error('File Write Error', [
                    'url' => $url,
                    'page' => $page,
                    'title' => $title,
                    'author' => $authorSelector,
                    'type' => $typeSelector,
                    'year' => $year,
                    'tags' => implode(', ', $tags),
                    'content' => $content,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            //  $this->logOrPrint("No text found at URL: $url");
            $this->logOrPrint("No text found at URL: $url");

            // Write URLs with no text to a separate file
            try {
                file_put_contents(storage_path('no_text_urls.txt'), $url . PHP_EOL, FILE_APPEND);
            } catch (\Exception $e) {
                Log::error('File Write Error for No Text URLs', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        $crawler->filter('a')->each(function ($node) use ($client) {
            $link = $node->link();
            $url = $link->getUri();

            if ($this->isSameDomain($url) && !isset($this->visited[$url]) && !ScrapedData::where('url', $url)->exists()) {
                $this->queue[] = $url;
                $this->saveState();
            }
        });
    }


    private function crawlChuvashnews(Client $client, $url)
    {
        $this->info("Crawling URL: $url");

        // ПРОВЕРКА НА ИСКЛЮЧАЕМЫЕ ПАТТЕРНЫ

        if (
            strpos($url, '/news/tags/') !== false ||
            strpos($url, '/news/tema/') !== false ||
            strpos($url, 'chuvash.org/lib/') !== false ||
            strpos($url, 'chuvash.org/wiki/') !== false
        ) {
            $this->info("⏩ URL contains excluded pattern, skipping: $url");
            return;
        }


        // ПРИОРИТЕТНАЯ ОБРАБОТКА: если это страница со списком новостей (/a/news/)
        if (preg_match('/\/a\/news\/\d+\.html$/', $url)) {
            // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: если уже посещали, не обрабатываем снова
            if (isset($this->visited[$url])) {
                $this->info("⏩ News list already visited, skipping: $url");
                return;
            }
            $this->info("🚀 PRIORITY: News list page detected, fast processing");
            return $this->processNewsListPage($client, $url);
        }

        // ДОБАВЬТЕ ЭТОТ БЛОК ДЛЯ ПРИОРИТЕТНОЙ ОБРАБОТКИ СТРАНИЦ БЛОГОВ
        if (preg_match('/\/blogs\/\d+\.html$/', $url)) {
            // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: если уже посещали, не обрабатываем снова
            if (isset($this->visited[$url])) {
                $this->info("⏩ Blog list already visited, skipping: $url");
                return;
            }
            $this->info("🚀 PRIORITY: Blog list page detected, fast processing");
            return $this->processBlogListPage($client, $url);
        }

        // ПРИОРИТЕТНАЯ ОБРАБОТКА: если это страница статьи блога
        if (preg_match('/\/blogs\/comments\/\d+\.html$/', $url)) {
            if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
                $this->info("⏩ Blog article already visited, skipping: $url");
                return;
            }
            $this->info("🚀 PRIORITY: Blog article page detected");
            return $this->processBlogArticlePage($client, $url);
        }



        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }




        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $this->info("Making request to: $url");
        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped URL due to timeout: $url");
            return;
        }

        $this->info("Successfully received page content");

        // ОПРЕДЕЛЕНИЕ ТИПА СТРАНИЦЫ
        $isArticlePage = false;
        if (strpos($url, 'chuvash.org/news/') !== false) {
            $isArticlePage = true;
            $this->info("This is an ARTICLE page");
        } else {
            $this->info("This is INDEX/ARCHIVE page, collecting links only");
            // Для не-статей только собираем ссылки
            $this->collectLinksFromPage($crawler);
            return;
        }

        $text = '';
        $title = '';
        $articleType = '';
        $publishDate = '';
        $author = '';
        $year = '';

        // ПАРСИНГ СТАТЬИ
        try {
            // Заголовок
            $titleSelector = 'body > div.varnelle > div > div.area2 > div > h2';
            if ($crawler->filter($titleSelector)->count() > 0) {
                $title = trim($crawler->filter($titleSelector)->text());
                $this->info("Found title: $title");
            } else {
                $this->warn("Title not found with selector: $titleSelector");
            }

            // Автор
            $author = $this->findAuthor($crawler);

            // Дата и год
            $yearData = $this->findDateAndYear($crawler);
            $year = $yearData['year'];
            $publishDate = $yearData['date'];

            // Тип/категория
            $articleType = $this->findArticleType($crawler);

            // Контент
            $text = $this->findContent($crawler);
        } catch (\Exception $e) {
            $this->error("Error parsing article: " . $e->getMessage());
            Log::error('Parsing error for Chuvashnews', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // СОХРАНЕНИЕ В БАЗУ ДАННЫХ
        $this->saveArticleToDatabase($url, $title, $author, $articleType, $year, $text);

        // СБОР ССЫЛОК С СТРАНИЦЫ
        $this->collectLinksFromPage($crawler);
    }


    private function findAuthor($crawler)
    {
        $authorSelectors = [
            //  body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(1) > a > span
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > b > a > b',
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > b > a',
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) a b',
            '.chblocktext_yellow b a',
            '.chblocktext_yellow a b',
            'div.chblocktext_yellow a[href*="author"]',
            'div.chblocktext_yellow b',
        ];

        foreach ($authorSelectors as $authorSelector) {
            if ($crawler->filter($authorSelector)->count() > 0) {
                $author = trim($crawler->filter($authorSelector)->text());
                if (!empty($author) && strlen($author) < 100 && $this->looksLikeAuthorName($author)) {
                    $this->info("✅ Found author with selector '$authorSelector': $author");
                    return $author;
                }
            }
        }

        $this->warn("❌ Author not found with any selector");
        return '';
    }

    private function findDateAndYear($crawler)
    {
        $dateSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div:nth-child(3) > span',
            '.chblocktext_yellow div:nth-child(3) span',
        ];

        foreach ($dateSelectors as $dateSelector) {
            if ($crawler->filter($dateSelector)->count() > 0) {
                $rawDateText = trim($crawler->filter($dateSelector)->text());
                $this->info("🔍 Raw date text: '$rawDateText'");

                if ($this->looksLikeDate($rawDateText)) {
                    $publishDate = $rawDateText;
                    $year = $this->extractYearFromDate($publishDate);
                    $this->info("✅ Found REAL date: $publishDate -> Year: $year");
                    return ['date' => $publishDate, 'year' => $year];
                }
            }
        }

        // Fallback: поиск года в тексте страницы
        $pageText = $crawler->text();
        if (preg_match('/\b(20\d{2})\b/', $pageText, $matches)) {
            $year = $matches[1];
            $this->info("✅ Found year in page text: $year");
            return ['date' => '', 'year' => $year];
        }

        $year = date('Y');
        $this->info("⚠️ Year not found, using current: $year");
        return ['date' => '', 'year' => $year];
    }

    private function findArticleType($crawler)
    {
        $typeSelectors = [
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.hipar_text > span > a',
            '.hipar_text span a',
            '.hipar_text a',
            'div.hipar_text a[href*="/news/tema/"]',
            '.chblocktext_yellow a[href*="/news/tema/"]',
        ];

        foreach ($typeSelectors as $typeSelector) {
            if ($crawler->filter($typeSelector)->count() > 0) {
                $articleType = trim($crawler->filter($typeSelector)->text());
                $this->info("✅ Found type with selector '$typeSelector': $articleType");
                return $articleType;
            }
        }

        $this->warn("❌ Type not found with any selector");
        return '';
    }

    private function findContent($crawler)
    {
        $text = '';

        // ПРОБУЕМ РАЗНЫЕ СЕЛЕКТОРЫ ДЛЯ КОНТЕНТА
        $contentSelectors = [

            //  body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.blog > noindex > p:nth-child(2)
            'body > div.varnelle > div > div.area2 > div > div.chblocktext_yellow > div > div.hipar_text',
            '.hipar_text',
            'div.hipar_text',
            '.chblocktext_yellow .hipar_text',
            'div.chblocktext_yellow div.hipar_text'
        ];

        $contentContainer = null;
        $usedSelector = '';

        foreach ($contentSelectors as $selector) {
            if ($crawler->filter($selector)->count() > 0) {
                $contentContainer = $crawler->filter($selector);
                $usedSelector = $selector;
                $this->info("✅ Found content container with selector: $selector");
                break;
            }
        }

        if ($contentContainer === null) {
            $this->warn("❌ Content container not found with any selector");
            return '';
        }

        // ОТЛАДКА: покажем структуру найденного контейнера
        $this->debugContainerStructure($contentContainer, $usedSelector);

        // ПАРСИМ КОНТЕНТ ИЗ ПАРАГРАФОВ, ИСКЛЮЧАЯ НЕЖЕЛАТЕЛЬНЫЕ
        $paragraphs = $contentContainer->filter('p:not(.news_tags):not([class*="tag"]):not([class*="meta"])');

        if ($paragraphs->count() > 0) {
            $this->info("Found {$paragraphs->count()} content paragraphs");

            $paragraphs->each(function ($node) use (&$text) {
                $paragraph = trim($node->text());

                // ФИЛЬТРАЦИЯ ПАРАГРАФОВ
                if ($this->isValidContentParagraph($paragraph)) {
                    $text .= $paragraph . PHP_EOL . PHP_EOL;
                    $this->info("✅ Added paragraph: " . substr($paragraph, 0, 50) . "...");
                } else {
                    $this->info("⏩ Skipped paragraph: " . substr($paragraph, 0, 50) . "...");
                }
            });
        } else {
            $this->warn("No paragraphs found, trying to get all text from container");
            $text = trim($contentContainer->text());
        }

        $this->info("Final content length: " . strlen($text));
        return $text;
    }

    private function crawlAvangard(Client $client, $url)
    {
        $this->info("Crawling URL: $url");

        // ТОЧНО ТАКАЯ ЖЕ ПРОВЕРКА КАК В crawlHypar
        if (isset($this->visited[$url]) || ScrapedData::where('url', $url)->exists()) {
            $this->info("URL already visited: $url");
            return;
        }

        $this->visited[$url] = true;
        $this->saveVisitedUrls();
        $this->currentUrl = $url;
        $this->saveState();

        $this->info("Making request to: $url");
        $crawler = $this->requestWithRetry($client, $url);
        if ($crawler === null) {
            $this->logOrPrint("Skipped URL due to timeout: $url");
            return;
        }

        $this->info("Successfully received page content");

        // ОПРЕДЕЛЕНИЕ ТИПА СТРАНИЦЫ (упрощенная версия)
        $isArticlePage = false;
        if (
            // strpos($url, '/ru/aktualno/') !== false ||
            strpos($url, '/ru/gazeta/') !== false ||
            preg_match('/\/gazeta\/\d+/', $url)
        ) {
            $isArticlePage = true;
            $this->info("This is an ARTICLE page");
        } else {
            $this->info("This is INDEX/ARCHIVE page, collecting links only");
        }

        $text = '';
        $title = '';
        $articleType = '';
        $publishDate = '';
        $author = '';
        $year = '';

        // ПАРСИНГ СОДЕРЖИМОГО ТОЛЬКО ДЛЯ СТРАНИЦ СТАТЕЙ
        if ($isArticlePage) {
            try {
                // Заголовок
                $titleSelector = '#component > div > div.article_heading > h2';
                if ($crawler->filter($titleSelector)->count() > 0) {
                    $title = trim($crawler->filter($titleSelector)->text());
                    $this->info("Found title: $title");
                }

                // Дата
                $dateSelector = '#component > div > div.article_heading > div > span.published';
                if ($crawler->filter($dateSelector)->count() > 0) {
                    $publishDate = trim($crawler->filter($dateSelector)->text());
                    $year = $this->extractYearFromDate($publishDate);
                    $this->info("Found date: $publishDate");
                }

                // Тип/категория
                $typeSelector = '#component > div > div.article_heading > div > span.category-name > a';
                if ($crawler->filter($typeSelector)->count() > 0) {
                    $articleType = trim($crawler->filter($typeSelector)->text());
                    $this->info("Found type: $articleType");
                }

                // Контент
                $contentSelector = '#component > div > p';
                if ($crawler->filter($contentSelector)->count() > 0) {
                    $crawler->filter($contentSelector)->each(function ($node) use (&$text) {
                        $paragraph = trim($node->text());
                        if (
                            !empty($paragraph) &&
                            strlen($paragraph) > 10 &&
                            !str_contains($paragraph, 'Категория:') &&
                            !str_contains($paragraph, 'Опубликовано:') &&
                            !str_contains($paragraph, 'Просмотров:')
                        ) {
                            $text .= $paragraph . PHP_EOL . PHP_EOL;
                        }
                    });
                    $this->info("Content length: " . strlen($text));
                }

                // Автор
                $author = $this->findAuthor($crawler);
                if ($author) {
                    $this->info("Found author: $author");
                }
            } catch (\Exception $e) {
                $this->error("Error parsing article: " . $e->getMessage());
            }

            // ПРОВЕРКА НА ЧУВАШСКИЙ ТЕКСТ И СОХРАНЕНИЕ
            $isChuvashText = $this->containsChuvashCharacters($text);
            $this->info("Chuvash text detected: " . ($isChuvashText ? 'YES' : 'NO'));

            if (!empty($text) && $isChuvashText) {
                try {
                    ScrapedData::create([
                        'url' => $url,
                        'page' => null,
                        'title' => $title,
                        'author' => $author,
                        'type' => $articleType,
                        'year' => $year ?? '',
                        'tags' => '',
                        'content' => $text
                    ]);
                    $this->info("✅ Saved to DATABASE: $url");
                } catch (\Exception $e) {
                    Log::error('Database Insert Error for Avangard', [
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                    $this->error("❌ Database insert error: " . $e->getMessage());
                }
            } else if (!empty($text)) {
                $this->info("❌ Text found but no Chuvash characters: $url");
            } else {
                $this->info("❌ No content found for article: $url");
            }
        }

        // ПОИСК ССЫЛОК - ТОЧНО ТАКОЙ ЖЕ КАК В crawlHypar
        $crawler->filter('a')->each(function ($node) {
            $href = $node->attr('href');

            if (!empty($href)) {
                // Пропускаем якоря и javascript
                if (strpos($href, '#') === 0 || strpos($href, 'javascript:') === 0) {
                    return;
                }

                // Создаем абсолютный URL
                $absoluteUrl = $this->makeAbsoluteUrl($href, $this->currentUrl);

                if (!$absoluteUrl || !$this->isSameDomain($absoluteUrl)) {
                    return;
                }

                // ТОЧНО ТАКАЯ ЖЕ ПРОВЕРКА КАК В crawlHypar
                if (!isset($this->visited[$absoluteUrl]) && !ScrapedData::where('url', $absoluteUrl)->exists()) {
                    $this->queue[] = $absoluteUrl;
                    $this->saveState();
                }
            }
        });
    }

    // НОВАЯ ФУНКЦИЯ: Проверка на наличие чувашских символов
    // private function containsChuvashCharacters($text)
    // {
    //     if (empty($text)) {
    //         return false;
    //     }

    //     // ОСНОВНЫЕ ЧУВАШСКИЕ СИМВОЛЫ (Unicode)
    //     $chuvashChars = [
    //         'ӗ',
    //         'Ӗ', // чувашская e с breve (U+04D7, U+04D6)
    //         'ӑ',
    //         'Ӑ', // чувашская a с breve (U+04D1, U+04D0)  
    //         'ҫ',
    //         'Ҫ', // чувашская c с cedilla (U+04AB, U+04AA)
    //         'ӳ',
    //         'Ӳ', // чувашская u с diaeresis (U+04F3, U+04F2)
    //         'ĕ',
    //         'Ĕ', // латинская e с breve (для обратной совместимости)
    //         'ă',
    //         'Ă', // латинская a с breve (для обратной совместимости)
    //         'ç',
    //         'Ç', // латинская c с cedilla (для обратной совместимости)
    //         'ÿ',
    //         'Ÿ'  // латинская y с diaeresis (для обратной совместимости)
    //     ];

    //     foreach ($chuvashChars as $char) {
    //         if (strpos($text, $char) !== false) {
    //             $this->info("✅ Found Chuvash character: '$char'");
    //             return true;
    //         }
    //     }

    //     // Дополнительная проверка по словам
    //     // return $this->containsChuvashWords($text);
    // }
    // // Вот что мне выдает в консоли. 



    private function extractYearFromDate($dateString)
    {
        // Очищаем дату от лишнего текста
        $dateString = preg_replace('/\s*\|\s*\d+\s+хут\s+пăхнă\s*/', '', $dateString);

        $patterns = [
            '/(\d{4})/',                          // 2025
            '/(\d{2})\.(\d{2})\.(\d{4})/',        // 20.10.2025
            '/(\d{4})-(\d{2})-(\d{2})/',          // 2025-10-20
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $dateString, $matches)) {
                return end($matches); // возвращаем последнюю группу (год)
            }
        }

        return '';
    }


    private function looksLikeAuthorName($text)
    {
        if (empty($text) || strlen($text) > 50) {
            return false;
        }

        $text = trim($text);

        // Проверяем паттерны имен
        $namePatterns = [
            '/^[А-ЯЁ][а-яё]+\s+[А-ЯЁ][а-яё]+$/u',
            '/^[А-ЯЁ][а-яё]+\s+[А-ЯЁ][а-яё]+\s+[А-ЯЁ][а-яё]+$/u',
        ];

        foreach ($namePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }


    private function makeAbsoluteUrl($url, $baseUrl)
    {
        if (empty($url)) {
            return '';
        }

        // Если URL уже абсолютный
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $parsedBase = parse_url($baseUrl);
        $baseScheme = $parsedBase['scheme'] ?? 'https';
        $baseHost = $parsedBase['host'];
        $basePath = $parsedBase['path'] ?? '';

        // Если ссылка начинается с //
        if (strpos($url, '//') === 0) {
            return $baseScheme . ':' . $url;
        }

        // Если ссылка абсолютная (начинается с /)
        if (strpos($url, '/') === 0) {
            return $baseScheme . '://' . $baseHost . $url;
        }

        // Если ссылка относительная
        // Убираем имя файла из базового пути
        if (substr($basePath, -1) !== '/') {
            $basePath = dirname($basePath) . '/';
        }

        // Убираем повторяющиеся слэши
        $basePath = rtrim($basePath, '/') . '/';
        $url = ltrim($url, './');

        return $baseScheme . '://' . $baseHost . $basePath . $url;
    }
}

//php artisan parse:website https://chuvash.org/lib/  chuvash

//cd domains\chuvkorpus
// php artisan parse:website http://hypar.ru/ hypar

// php artisan parse:website "https://avangard-21.ru/gazeta" avangard


// php artisan parse:website "https://chuvash.org/a/news/" chuvashnews 
// php artisan parse:website "https://chuvash.org/a/news/" chuvashnews 

// php artisan parse:website "https://www.chuvash.org/blogs/1.html" chuvashblogs

// php artisan parse:website https://cv.ruwiki.ru/ cvruwiki


// титул
// #firstHeading > span
// #firstHeading > span

#mw-content-text > div.mw-parser-output
// текст 

// #mw-content-text > div.mw-parser-output > p:nth-child(2)
// #mw-content-text > div.mw-parser-output > ul:nth-child(143)
// #mw-content-text > div.mw-parser-output > div:nth-child(138)

// У меня еще такая проблема. Не определяются тексты заколовков текста, который находится например в селекторе #mw-content-text > div.mw-parser-output > p:nth-child(3)
// У него заголовок может быть например в селекторе #Уявсем,#Пулса_иртнĕ,#Çуралнă и так далее, то есть название селектора может быть любым.
// И все они находятся в селекторе #mw-content-text > div.mw-parser-output. То есть и текст и его заголовки (#Уявсем,#Пулса_иртнĕ,#Çуралнă...) 
// находятся в селекторе  #mw-content-text > div.mw-parser-output. Как осуществить поиск этих уникальных селекторов? 
// #mw-content-text > div.mw-parser-output > div.columns > div > ol

#mw-content-text > div.mw-parser-output > div.columns > div  = не надо 

// Доклад: О вкладе сотрудников ЧГИГН в продвижение чувашского языка в Яндекс-сервисы 
//    В 2024 году компания Яндекс инициировала масштабный проект по внедрению чувашского языка в системы синтеза
//     (Text-to-Speech) и распознавания речи (Automatic Speech Recognition). Ключевым партнером в этой работе 
//     выступил Чувашский государственный институт гуманитарных наук.
//      Сотрудничество продолжалось более пяти месяцев и охватило все этапы — от начальной оценки
//       дикторов до финального запуска технологии.
// 1. Основные направления сотрудничества
//  Первым пластом работ института была помощь в создании синтезатора чувашской речи
// 1.1. Экспертная оценка дикторов для синтеза речи.
// Первой задачей была выбор эталонного голоса для системы синтеза чувашской речи. 
// По результатам анализа проведенного сотрудниками института для Яндекса были даны рекомендации
//  какой голос лучше подходит для синтеза, что помогло избежать ошибок на начальном этапе.
// 1.2 Лингвистическое консультирование и нормализация
// Сотрудники ЧГИГН предоставили Яндексу детальные консультации и отчеты по тонкостям чувашской грамматики и орфоэпии:
// •	Нормализация числительных: были  разработаны правила преобразования цифровых записей в буквенные.
// •	Произношение сложных конструкций: даны рекомендации по озвучиванию дробных числительных, дат, падежных форм.
//     Алгоритм простановки ударений: сотрудникам яндекса был предоставлен алгоритм простановки ударений в чувашских словах.
// •	Оценка синтезированной речи: Проходили видеосовещания  на которых  обсуждались вопросы,
//     связанные с качеством разработанного синтезатора чувашской речи, а именно приемлемость  представленных вариантов синтеза,
//      интонационные неточности, орфоэпические нормы и прочие узкие моменты. 
//    По итогам совещаний вырабатывались конкретные рекомендации для дальнейшего улучшения качества синтеза.
// Вставить сюда рисунок
//  Вторым пластом работ была помощь в сборе материалов для обучения нейросети для системы распознавания чувашской речи
// 2.1. Организация масштабного сбора данных для обучения нейросети.
// Для обучения модели распознавания речи требовалось собрать десятки тысяч записей коротких
//  фраз на чувашском языке. Институт взял на себя ключевую роль в организации этого процесса:
// • Была сформирована группы исполнителей:  привлечено к работе более 30 человек — научных сотрудников,
//  преподавателей, студентов, что обеспечило разнообразие голосов (разный возраст, пол, тембр).
// •	Создание инфраструктуры: для оперативного взаимодействия был создан отдельный чат в Telegram,
//  где участники могли задавать вопросы, получать консультации и координировать свои действия.
// •	Решение технических проблем: сотрудники института активно помогали участникам с регистрацией на рабочей 
// платформе , решали проблемы с доступом к заданиям, консультировали по использованию интерфейса, решали проблемы с блокировкой 
// на разных операционных системах.
// 2.2. Контроль и улучшение качества лингвистических данных для надиктовки акустических данных.
// В процессе работы выяснилось, что часть текстов, предоставленных для наговоров, содержала
//  ошибки, опечатки или были семантически некорректны. 
// • Была проведена выборочная проверка: была проведена ручная разметка определенного массива случайных фраз, классифицируя
//  их на «качественные», «сомнительные» и «нечитаемые».
// •	Анализ источников: было установлено, что основные проблемы возникают с текстами из новостных лент,
//  в то время как художественная литература демонстрировала высокое качество.
// 	В итоге в задания была добавлена опция «Ошибка в тексте»,
//      позволившая исполнителям маркировать проблемные фразы и тем самым постепенно очищать общий массив данных.
// 2.3. Техническая и коммуникационная поддержка
// •	Связующее звено: была обезспечена постоянная коммуникация между командой
//  Яндекса и исполнителями, оперативно решающая возникающие проблемы у каждого из рабочей группы. 
// •	Адаптация методик: сотрудники института помогали адаптировать технические задания под нужды и возможности 
// носителей языка, многие из которых не имели опыта работы с подобными платформами.
// 3 Ключевые достижения и результаты
// •	Запуск технологий: 18 декабря 2024 года синтез и распознавание чувашской речи стали доступны в основных
//  сервисах Яндекса — Переводчике, Поиске, Клавиатуре.
// •	Объем данных: при непосредственном участии ЧГИГН собрано более 100 000 фраз для обучения модели распознавания,
//  что превысило первоначальные планы и ожидания.
//  Заключение:  Благодаря экспертной поддержке, организационной работе и активному участию сотрудников 
//   института удалось не только реализовать сложный технологический проект, но и обеспечить
//    высочайшие стандарты лингвистического качества в системе синтеза и распознавания чувашской речи.
// Хочется закончить выступление словами заместитель руководителя проекта «Языки народов России» ООО «Яндекс» - 
// «Везде (где журналисты не забыли 🙂) подчеркивается, что ЧГИГН — главный наш партнер в этом запуске; без вас бы правда ничего не получилось!»
