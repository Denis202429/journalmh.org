<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByDesc('number')
            ->get();

        $query = Article::query()->with('issue');

        if ($request->filled('issue_id')) {
            $query->where('issue_id', (int) $request->input('issue_id'));
        }

        $articles = $query
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->appends($request->query());

        return view('admin.articles.index', compact('articles', 'issues'));
    }

    public function create()
    {
        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByDesc('number')
            ->get();

        return view('admin.articles.create', compact('issues'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'issue_id' => 'required|exists:issues,id',
            'title_ru' => 'nullable|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'title_cv' => 'nullable|string|max:500',
            'pages' => 'nullable|string|max:50',
            'art_type' => 'nullable|string|in:RAR,REV,SCO,BRV,CNF,EDI,ABS,REP,RPR,COR,PER,MIS',
            'lang_publ' => 'nullable|string|in:RUS,ENG,CHV',
            'abstract_ru' => 'nullable|string',
            'abstract_en' => 'nullable|string',
            'abstract_cv' => 'nullable|string',
            'keywords_ru' => 'nullable|string',
            'keywords_en' => 'nullable|string',
            'keywords_cv' => 'nullable|string',
            'text_ru' => 'nullable|string',
            'text_en' => 'nullable|string',
            'text_cv' => 'nullable|string',
            'doi' => 'nullable|string|max:100',
            'edn' => 'nullable|string|max:6',
            'udk' => 'nullable|string|max:100',
            'bbk' => 'nullable|string|max:100',
            'date_received' => 'nullable|date',
            'date_accepted' => 'nullable|date',
            'date_publication' => 'nullable|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',

            // НОВЫЕ ПОЛЯ
            'section_ru' => 'nullable|string|max:255',
            'section_en' => 'nullable|string|max:255',
            'section_cv' => 'nullable|string|max:255',
            'fundings_ru' => 'nullable|string',
            'fundings_en' => 'nullable|string',
            'fundings_cv' => 'nullable|string',
            'references_ru' => 'nullable|string',
            'references_en' => 'nullable|string',
            'citation_ru' => 'nullable|string',
            'citation_en' => 'nullable|string',
            'citation_cv' => 'nullable|string',

            // Поля авторов
            'authors.*.role' => 'nullable|string|max:2',
            'authors.*.degree_ru' => 'nullable|string|max:255',
            'authors.*.degree_en' => 'nullable|string|max:255',
            'authors.*.degree_cv' => 'nullable|string|max:255',
            'authors.*.rank_ru' => 'nullable|string|max:255',
            'authors.*.rank_en' => 'nullable|string|max:255',
            'authors.*.rank_cv' => 'nullable|string|max:255',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // ПРЕОБРАЗОВАНИЕ СТРОК В МАССИВЫ
        // Финансирование
        if ($request->has('fundings_ru')) {
            $validated['fundings_ru'] = $request->fundings_ru ? explode("\n", trim($request->fundings_ru)) : [];
        }
        if ($request->has('fundings_en')) {
            $validated['fundings_en'] = $request->fundings_en ? explode("\n", trim($request->fundings_en)) : [];
        }
        if ($request->has('fundings_cv')) {
            $validated['fundings_cv'] = $request->fundings_cv ? explode("\n", trim($request->fundings_cv)) : [];
        }

        // Список литературы
        if ($request->has('references_ru')) {
            $validated['references_ru'] = $request->references_ru ? explode("\n", trim($request->references_ru)) : [];
        }
        if ($request->has('references_en')) {
            $validated['references_en'] = $request->references_en ? explode("\n", trim($request->references_en)) : [];
        }

        // Обработка загрузки PDF файла
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            // $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $fileName = time() . '_' . uniqid() . '.pdf';
            $filePath = $file->storeAs('pdfs', $fileName, 'public');

            $validated['pdf_file_path'] = $filePath;
            $validated['pdf_original_name'] = $file->getClientOriginalName();
            $validated['pdf_file_size'] = $file->getSize();
            // $validated['pdf_url'] = null; // Очищаем внешнюю ссылку, если загружен файл
        }

        // Создаем статью
        $article = Article::create($validated);

        // Сохраняем авторов
        if ($request->has('authors') && is_array($request->authors)) {
            foreach ($request->authors as $num => $authorData) {
                if (empty($authorData['surname_ru']) && empty($authorData['surname_en']) && empty($authorData['name_ru'])) {
                    continue;
                }

                $article->authors()->create([
                    'author_num' => $num + 1,
                    'surname_ru' => $authorData['surname_ru'] ?? null,
                    'surname_en' => $authorData['surname_en'] ?? null,
                    'surname_cv' => $authorData['surname_cv'] ?? null,
                    'name_ru' => $authorData['name_ru'] ?? null,
                    'name_en' => $authorData['name_en'] ?? null,
                    'name_cv' => $authorData['name_cv'] ?? null,
                    'patronymic_ru' => $authorData['patronymic_ru'] ?? null,
                    'patronymic_en' => $authorData['patronymic_en'] ?? null,
                    'patronymic_cv' => $authorData['patronymic_cv'] ?? null,
                    'org_name_ru' => $authorData['org_name_ru'] ?? null,
                    'org_name_en' => $authorData['org_name_en'] ?? null,
                    'town_ru' => $authorData['town_ru'] ?? null,
                    'town_en' => $authorData['town_en'] ?? null,
                    'town_cv' => $authorData['town_cv'] ?? null,
                    'country_ru' => $authorData['country_ru'] ?? null,
                    'country_en' => $authorData['country_en'] ?? null,
                    'country_cv' => $authorData['country_cv'] ?? null,
                    'position_ru' => $authorData['position_ru'] ?? null,
                    'position_en' => $authorData['position_en'] ?? null,
                    'position_cv' => $authorData['position_cv'] ?? null,

                    'degree_ru' => $authorData['degree_ru'] ?? null,
                    'degree_en' => $authorData['degree_en'] ?? null,
                    'degree_cv' => $authorData['degree_cv'] ?? null,
                    'rank_ru' => $authorData['rank_ru'] ?? null,
                    'rank_en' => $authorData['rank_en'] ?? null,
                    'rank_cv' => $authorData['rank_cv'] ?? null,


                    'orcid' => isset($authorData['orcid']) ? substr($authorData['orcid'], 0, 19) : null,
                    'spin' => isset($authorData['spin']) ? substr($authorData['spin'], 0, 9) : null,
                    'email' => $authorData['email'] ?? null,
                    'is_correspondent' => isset($authorData['is_correspondent']),
                    'role' => $authorData['role'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.articles.index')->with('success', 'Статья добавлена');
    }

    // public function edit(Article $article)
    // {
    //     $issues = Issue::query()
    //         ->orderByDesc('year')
    //         ->orderByDesc('number')
    //         ->get();

    //     // Загружаем авторов через прямой запрос (гарантированно работает)
    //     $authors = ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();

    //     // Привязываем авторов к статье
    //     $article->setRelation('authors', $authors);

    //     return view('admin.articles.edit', compact('article', 'issues'));
    // }

    public function edit(Article $article)
    {
        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByDesc('number')
            ->get();

        // Загружаем авторов
        $authors = ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();
        $article->setRelation('authors', $authors);

        // Определяем, какой раздел выбран на основе сохраненных значений
        $selectedSection = '';

        $sectionsMap = [
            'historical' => ['ru' => 'Исторические науки', 'en' => 'Historical Sciences', 'cv' => 'Истори ăслăхĕсем'],
            'philological' => ['ru' => 'Филологические науки', 'en' => 'Philological Sciences', 'cv' => 'Филологи ăслăхĕсем'],
            'art' => ['ru' => 'Виды искусств', 'en' => 'Arts', 'cv' => 'Искусство тĕсĕсем'],
            'reviews' => ['ru' => 'Рецензии', 'en' => 'Reviews', 'cv' => 'Рецензисем'],
            'personalia' => ['ru' => 'Персоналии', 'en' => 'Personalia', 'cv' => 'Персоналисем'],
            'scientific_life' => ['ru' => 'Научная жизнь', 'en' => 'Scientific Life', 'cv' => 'Ăслăх пурнăçĕ'],
        ];

        foreach ($sectionsMap as $key => $values) {
            if (
                $article->section_ru == $values['ru'] ||
                $article->section_en == $values['en'] ||
                $article->section_cv == $values['cv']
            ) {
                $selectedSection = $key;
                break;
            }
        }

        return view('admin.articles.edit', compact('article', 'issues', 'selectedSection'));
    }




    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'issue_id' => 'required|exists:issues,id',
            'title_ru' => 'nullable|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'title_cv' => 'nullable|string|max:500',
            'pages' => 'nullable|string|max:50',

            'section_ru' => 'nullable|string|max:255',
            'section_en' => 'nullable|string|max:255',
            'section_cv' => 'nullable|string|max:255',
            'fundings_ru' => 'nullable|string',
            'fundings_en' => 'nullable|string',
            'fundings_cv' => 'nullable|string',
            'references_ru' => 'nullable|string',
            'references_en' => 'nullable|string',
            'citation_ru' => 'nullable|string',
            'citation_en' => 'nullable|string',
            'citation_cv' => 'nullable|string',

            'art_type' => 'nullable|string|in:RAR,REV,SCO,BRV,CNF,EDI,ABS,REP,RPR,COR,PER,MIS',
            'lang_publ' => 'nullable|string|in:RUS,ENG,CHV',
            'abstract_ru' => 'nullable|string',
            'abstract_en' => 'nullable|string',
            'abstract_cv' => 'nullable|string',
            'keywords_ru' => 'nullable|string',
            'keywords_en' => 'nullable|string',
            'keywords_cv' => 'nullable|string',
            'text_ru' => 'nullable|string',      // Добавьте
            'text_en' => 'nullable|string',      // Добавьте
            'text_cv' => 'nullable|string',      // Добавьте            
            'doi' => 'nullable|string|max:100',
            'edn' => 'nullable|string|max:6',
            'udk' => 'nullable|string|max:100',
            'bbk' => 'nullable|string|max:100',
            'date_received' => 'nullable|date',
            'date_accepted' => 'nullable|date',
            'date_publication' => 'nullable|date',
            // 'pdf_url' => 'nullable|url|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'delete_pdf' => 'nullable|boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
            'authors.*.role' => 'nullable|string|max:2', // Добавьте эту строку
            'authors.*.degree_ru' => 'nullable|string|max:255',
            'authors.*.degree_en' => 'nullable|string|max:255',
            'authors.*.degree_cv' => 'nullable|string|max:255',
            'authors.*.rank_ru' => 'nullable|string|max:255',
            'authors.*.rank_en' => 'nullable|string|max:255',
            'authors.*.rank_cv' => 'nullable|string|max:255',

        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // ПРЕОБРАЗОВАНИЕ СТРОК В МАССИВЫ (те же, что и в store)
        if ($request->has('fundings_ru')) {
            $validated['fundings_ru'] = $request->fundings_ru ? explode("\n", trim($request->fundings_ru)) : [];
        }
        if ($request->has('fundings_en')) {
            $validated['fundings_en'] = $request->fundings_en ? explode("\n", trim($request->fundings_en)) : [];
        }
        if ($request->has('fundings_cv')) {
            $validated['fundings_cv'] = $request->fundings_cv ? explode("\n", trim($request->fundings_cv)) : [];
        }

        if ($request->has('references_ru')) {
            $validated['references_ru'] = $request->references_ru ? explode("\n", trim($request->references_ru)) : [];
        }
        if ($request->has('references_en')) {
            $validated['references_en'] = $request->references_en ? explode("\n", trim($request->references_en)) : [];
        }

        // Обработка удаления PDF
        if ($request->boolean('delete_pdf')) {
            if ($article->pdf_file_path) {
                // Удаляем физический файл
                if (Storage::disk('public')->exists($article->pdf_file_path)) {
                    Storage::disk('public')->delete($article->pdf_file_path);
                }

                // ОЧИЩАЕМ ВСЕ ПОЛЯ, связанные с файлом
                $validated['pdf_file_path'] = null;
                $validated['pdf_original_name'] = null;
                $validated['pdf_file_size'] = null;
            }
        }

        // Обработка загрузки нового PDF файла
        if ($request->hasFile('pdf_file')) {
            // Удаляем старый файл, если есть
            if ($article->pdf_file_path) {
                Storage::disk('public')->delete($article->pdf_file_path);
            }

            $file = $request->file('pdf_file');
            // $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $fileName = time() . '_' . uniqid() . '.pdf';
            $filePath = $file->storeAs('pdfs', $fileName, 'public');
            $validated['pdf_file_path'] = $filePath;
            $validated['pdf_original_name'] = $file->getClientOriginalName();
            $validated['pdf_file_size'] = $file->getSize();
            // $validated['pdf_url'] = null;
        }

        // Обновляем статью
        $article->update($validated);

        // Обновляем авторов
        $article->authors()->delete();

        if ($request->has('authors') && is_array($request->authors)) {
            foreach ($request->authors as $num => $authorData) {
                if (empty($authorData['surname_ru']) && empty($authorData['surname_en']) && empty($authorData['name_ru'])) {
                    continue;
                }

                $article->authors()->create([
                    'author_num' => $num + 1,
                    'surname_ru' => $authorData['surname_ru'] ?? null,
                    'surname_en' => $authorData['surname_en'] ?? null,
                    'surname_cv' => $authorData['surname_cv'] ?? null,
                    'name_ru' => $authorData['name_ru'] ?? null,
                    'name_en' => $authorData['name_en'] ?? null,
                    'name_cv' => $authorData['name_cv'] ?? null,
                    'patronymic_ru' => $authorData['patronymic_ru'] ?? null,
                    'patronymic_en' => $authorData['patronymic_en'] ?? null,
                    'patronymic_cv' => $authorData['patronymic_cv'] ?? null,
                    'org_name_ru' => $authorData['org_name_ru'] ?? null,
                    'org_name_en' => $authorData['org_name_en'] ?? null,
                    'town_ru' => $authorData['town_ru'] ?? null,
                    'town_en' => $authorData['town_en'] ?? null,
                    'town_cv' => $authorData['town_cv'] ?? null,
                    'country_ru' => $authorData['country_ru'] ?? null,
                    'country_en' => $authorData['country_en'] ?? null,
                    'country_cv' => $authorData['country_cv'] ?? null,
                    'position_ru' => $authorData['position_ru'] ?? null,
                    'position_en' => $authorData['position_en'] ?? null,
                    'position_cv' => $authorData['position_cv'] ?? null,

                    'degree_ru' => $authorData['degree_ru'] ?? null,
                    'degree_en' => $authorData['degree_en'] ?? null,
                    'degree_cv' => $authorData['degree_cv'] ?? null,
                    'rank_ru' => $authorData['rank_ru'] ?? null,
                    'rank_en' => $authorData['rank_en'] ?? null,
                    'rank_cv' => $authorData['rank_cv'] ?? null,

                    'orcid' => isset($authorData['orcid']) ? substr($authorData['orcid'], 0, 19) : null,
                    'spin' => isset($authorData['spin']) ? substr($authorData['spin'], 0, 9) : null,
                    'email' => $authorData['email'] ?? null,
                    'is_correspondent' => isset($authorData['is_correspondent']),
                    'role' => $authorData['role'] ?? null, // Добавьте эту строку
                ]);
            }
        }

        return redirect()->route('admin.articles.index')->with('success', 'Статья обновлена');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return back()->with('success', 'Статья удалена');
    }
}
