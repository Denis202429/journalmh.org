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
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

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
                    'degree' => $authorData['degree'] ?? null,
                    'rank' => $authorData['rank'] ?? null,
                    'orcid' => isset($authorData['orcid']) ? substr($authorData['orcid'], 0, 19) : null,
                    'spin' => isset($authorData['spin']) ? substr($authorData['spin'], 0, 9) : null,
                    'email' => $authorData['email'] ?? null,
                    'is_correspondent' => isset($authorData['is_correspondent']),
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

    //     // Обязательно загружаем авторов
    //     $article->load('authors');

    //     // Отладка - проверяем, сколько авторов загружено
    //     \Log::info('Article ID: ' . $article->id);
    //     \Log::info('Authors count: ' . $article->authors->count());
    //     foreach ($article->authors as $author) {
    //         \Log::info('Author: ' . ($author->surname_ru ?? 'no surname'));
    //     }

    //     return view('admin.articles.edit', compact('article', 'issues'));
    // }
    public function edit(Article $article)
    {
        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByDesc('number')
            ->get();

        // Загружаем авторов через прямой запрос (гарантированно работает)
        $authors = ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();

        // Привязываем авторов к статье
        $article->setRelation('authors', $authors);

        return view('admin.articles.edit', compact('article', 'issues'));
    }


    public function update(Request $request, Article $article)
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
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Обработка удаления PDF
        // if ($request->boolean('delete_pdf') && $article->pdf_file_path) {
        //     Storage::disk('public')->delete($article->pdf_file_path);
        //     $validated['pdf_file_path'] = null;
        //     $validated['pdf_original_name'] = null;
        //     $validated['pdf_file_size'] = null;
        // }

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

                // Также очищаем pdf_url, если он был

                // if ($request->filled('pdf_url')) {
                //     $validated['pdf_url'] = $request->pdf_url;
                // } else {
                //     $validated['pdf_url'] = null;
                // }
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
                    'degree' => $authorData['degree'] ?? null,
                    'rank' => $authorData['rank'] ?? null,
                    'orcid' => isset($authorData['orcid']) ? substr($authorData['orcid'], 0, 19) : null,
                    'spin' => isset($authorData['spin']) ? substr($authorData['spin'], 0, 9) : null,
                    'email' => $authorData['email'] ?? null,
                    'is_correspondent' => isset($authorData['is_correspondent']),
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
