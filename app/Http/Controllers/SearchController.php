<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Issue;
use App\Models\SitePage;
use Illuminate\Http\Request;

class SearchController extends Controller
{
   

    /**
     * Контроллер для поиска по сайту
     * Обрабатывает поиск по статьям, выпускам и страницам
     */
    public function index(Request $request)
    {
    // ========== 1. ПОЛУЧЕНИЕ И ОЧИСТКА ПАРАМЕТРОВ ПОИСКА ==========

        /**
         * Основной поисковый запрос (общий для всех типов контента)
         * query('q', '') - получает параметр 'q' из URL, если его нет - возвращает пустую строку
         * trim() - удаляет лишние пробелы в начале и конце строки
         */
        $q = trim((string) $request->query('q', ''));

        /**
         * Тип поиска: 'all' (всё), 'articles' (только статьи), 'issues' (только выпуски), 'pages' (только страницы)
         * По умолчанию 'all'
         */
        $type = trim((string) $request->query('type', 'all'));

        /**
         * Специализированные поля для более точного поиска
         * Позволяют искать конкретно по названию, автору, году и т.д.
         */
        $title = trim((string) $request->query('title', ''));      // Поиск по названию
        $author = trim((string) $request->query('author', ''));    // Поиск по автору (только для статей)
        $year = trim((string) $request->query('year', ''));        // Поиск по году издания
        $issueId = trim((string) $request->query('issue_id', '')); // Поиск по ID выпуска (точное совпадение)
        $pageSlug = trim((string) $request->query('page_slug', '')); // Поиск по slug страницы (точное совпадение)
    
    // ========== 2. ИНИЦИАЛИЗАЦИЯ КОЛЛЕКЦИЙ ДЛЯ РЕЗУЛЬТАТОВ ==========

        /**
         * Инициализируем пустые коллекции для результатов
         * Используем collect() вместо null, чтобы избежать проверок на существование в шаблоне
         */
        $articles = collect();   // Коллекция для найденных статей
        $issues = collect();     // Коллекция для найденных выпусков
        $pages = collect();      // Коллекция для найденных страниц

        /**
         * Получаем списки доступных вариантов для фильтров в форме
         * Нужны для отображения выпадающих списков в интерфейсе поиска
         */

        // Список опубликованных выпусков (сортировка: сначала новые, затем по номеру)
        $availableIssues = Issue::query()
            ->where('is_published', true)
            ->orderByDesc('year')    // Сначала более новые годы
            ->orderByDesc('number')  // В пределах года - сначала большие номера
            ->get();

        // Список опубликованных страниц (сортировка по алфавиту)
        $availablePages = SitePage::query()
            ->where('is_published', true)
            ->orderBy('title')
            ->get();
    
    // ========== 3. ПРОВЕРКА НАЛИЧИЯ ФИЛЬТРОВ ==========

        /**
         * Определяем, есть ли активные фильтры поиска
         * Если все параметры пустые - поиск не выполняется, показываем пустые результаты
         * Это позволяет не выполнять тяжелые запросы при первом открытии страницы поиска
         */
        $hasFilters = $q !== ''
            || $title !== ''
            || $author !== ''
            || $year !== ''
            || $issueId !== ''
            || $pageSlug !== '';

        // ========== 4. ВЫПОЛНЕНИЕ ПОИСКА (если есть фильтры) ==========

        if ($hasFilters) {
        
        // ---------- 4.1 ПОИСК ПО СТАТЬЯМ ----------
            /**
             * Поиск статей выполняется если:
             * - type = 'all' (ищем везде)
             * - type = 'articles' (ищем только статьи)
             */
            if (in_array($type, ['all', 'articles'], true)) {

                // Базовый запрос: только опубликованные статьи, подгружаем связанный выпуск
                $articlesQuery = Article::query()
                    ->with('issue')  // Жадная загрузка, чтобы избежать N+1 запросов
                    ->where('is_published', true);

                // 4.1.1 Поиск по общему запросу (q)
                if ($q !== '') {
                    $articlesQuery->where(function ($query) use ($q) {
                        // Ищем совпадения в нескольких полях (OR)
                        $query->where('title', 'like', "%{$q}%")      // По названию статьи
                            ->orWhere('authors', 'like', "%{$q}%")    // По авторам
                            ->orWhere('abstract', 'like', "%{$q}%")   // По аннотации
                            ->orWhere('doi', 'like', "%{$q}%");       // По DOI (идентификатору)
                    });
                }

                // 4.1.2 Фильтр по названию (если указан отдельно)
                if ($title !== '') {
                    $articlesQuery->where('title', 'like', "%{$title}%");
                }

                // 4.1.3 Фильтр по автору
                if ($author !== '') {
                    $articlesQuery->where('authors', 'like', "%{$author}%");
                }

                // 4.1.4 Фильтр по году (ищем через связанную таблицу issues)
                if ($year !== '') {
                    $articlesQuery->whereHas('issue', function ($query) use ($year) {
                        // whereHas - проверяет существование связанной модели с условием
                        $query->where('year', 'like', "%{$year}%");
                    });
                }

                // 4.1.5 Точный фильтр по ID выпуска
                if ($issueId !== '') {
                    $articlesQuery->where('issue_id', (int) $issueId);
                }

                // Выполняем запрос: сортируем по дате публикации (сначала новые), лимит 50 результатов
                $articles = $articlesQuery
                    ->orderByDesc('published_at')
                    ->limit(50)  // Ограничиваем количество, чтобы не перегружать страницу
                    ->get();
            }
        
        // ---------- 4.2 ПОИСК ПО ВЫПУСКАМ ----------
            /**
             * Поиск выпусков выполняется если:
             * - type = 'all' (ищем везде)
             * - type = 'issues' (ищем только выпуски)
             */
            if (in_array($type, ['all', 'issues'], true)) {

                $issuesQuery = Issue::query()
                    ->where('is_published', true);

                // 4.2.1 Поиск по общему запросу (q)
                if ($q !== '') {
                    $issuesQuery->where(function ($query) use ($q) {
                        $query->where('title', 'like', "%{$q}%")      // По названию выпуска
                            ->orWhere('month', 'like', "%{$q}%")     // По месяцу
                            ->orWhere('year', 'like', "%{$q}%")      // По году
                            ->orWhere('volume', 'like', "%{$q}%")    // По тому
                            ->orWhere('number', 'like', "%{$q}%");   // По номеру
                    });
                }

                // 4.2.2 Фильтр по названию
                if ($title !== '') {
                    $issuesQuery->where('title', 'like', "%{$title}%");
                }

                // 4.2.3 Фильтр по году
                if ($year !== '') {
                    $issuesQuery->where('year', 'like', "%{$year}%");
                }

                // 4.2.4 Точный фильтр по ID выпуска
                if ($issueId !== '') {
                    $issuesQuery->where('id', (int) $issueId);
                }

                // Выполняем запрос: сортируем по году и номеру, лимит 50
                $issues = $issuesQuery
                    ->orderByDesc('year')
                    ->orderByDesc('number')
                    ->limit(50)
                    ->get();
            }
        
        // ---------- 4.3 ПОИСК ПО СТАТИЧЕСКИМ СТРАНИЦАМ ----------
            /**
             * Поиск страниц выполняется если:
             * - type = 'all' (ищем везде)
             * - type = 'pages' (ищем только страницы)
             */
            if (in_array($type, ['all', 'pages'], true)) {

                $pagesQuery = SitePage::query()
                    ->where('is_published', true);

                // 4.3.1 Поиск по общему запросу (q)
                if ($q !== '') {
                    $pagesQuery->where(function ($query) use ($q) {
                        $query->where('title', 'like', "%{$q}%")      // По заголовку страницы
                            ->orWhere('content', 'like', "%{$q}%");   // По содержимому страницы
                    });
                }

                // 4.3.2 Фильтр по названию
                if ($title !== '') {
                    $pagesQuery->where('title', 'like', "%{$title}%");
                }

                // 4.3.3 Точный фильтр по slug (уникальному идентификатору страницы в URL)
                if ($pageSlug !== '') {
                    $pagesQuery->where('slug', $pageSlug);
                }

                // Выполняем запрос: сортировка по названию, лимит 50
                $pages = $pagesQuery
                    ->orderBy('title')
                    ->limit(50)
                    ->get();
            }
        }

        /**
         * Возвращаем представление с результатами поиска
         * В шаблон передаются:
         * - articles, issues, pages - результаты поиска
         * - availableIssues, availablePages - данные для фильтров в форме
         * - request - объект запроса (чтобы подставить значения в поля формы)
         * - hasFilters - флаг наличия активных фильтров
         */
        return view('search.index', compact(
            'q',              // Поисковый запрос
            'type',           // Тип поиска (all/articles/issues/pages)
            'title',          // Фильтр по названию
            'author',         // Фильтр по автору
            'year',           // Фильтр по году
            'issueId',        // Фильтр по ID выпуска
            'pageSlug',       // Фильтр по slug страницы
            'articles',       // Результаты поиска статей
            'issues',         // Результаты поиска выпусков
            'pages',          // Результаты поиска страниц
            'availableIssues', // Список выпусков для выпадающего списка
            'availablePages',  // Список страниц для выпадающего списка
            'hasFilters'      // Флаг наличия активных фильтров
        ));
    }
}
