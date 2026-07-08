<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\ArticleAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $query = Issue::query()->withCount('articles');

        // Фильтр по году
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        // Фильтр по типу выпуска
        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->input('issue_type'));
        }

        // Фильтр по статусу
        if ($request->filled('is_published')) {
            $query->where('is_published', (bool) $request->input('is_published'));
        }

        // Поиск
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('volume', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $issues = $query->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->orderBy('sort_order')
            ->paginate(30)
            ->appends($request->query());

        // Статистика по годам для фильтра
        $years = Issue::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('admin.issues.index', compact('issues', 'years'));
    }

    public function create()
    {
        return view('admin.issues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'issn' => 'nullable|string|max:9',
            'eissn' => 'nullable|string|max:9',
            'volume' => 'nullable|string|max:50',
            'number' => 'nullable|string|max:50',
            'alt_number' => 'nullable|string|max:50',
            'part' => 'nullable|integer|min:1|max:999',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'month' => 'nullable|string|max:255',
            'issue_pages' => 'nullable|string|max:50',
            'issue_type' => 'required|string|in:ISS,OFI,SPI',
            'title' => 'nullable|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'doi' => 'nullable|string|max:100',
            'issue_doi' => 'nullable|string|max:100',
            'edn' => 'nullable|string|max:6',
            'published_at' => 'nullable|date',
            'pdf_url' => 'nullable|url|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:102400',
            'cover_image' => 'nullable|url|max:2048',
            'cover_image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120', // Добавьте
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Обработка загрузки PDF файла выпуска
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $originalName = $file->getClientOriginalName();
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $filePath = $file->storeAs('issue_pdfs', $newFileName, 'public');

            $validated['pdf_file_path'] = $filePath;
            $validated['pdf_original_name'] = $originalName;
            $validated['pdf_file_size'] = $file->getSize();
            $validated['pdf_url'] = null;
        }

        // Обработка загрузки обложки выпуска (НОВОЕ)
        if ($request->hasFile('cover_image_file')) {
            $file = $request->file('cover_image_file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $newFileName = time() . '_cover_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            $filePath = $file->storeAs('issue_covers', $newFileName, 'public');

            $validated['cover_image_path'] = $filePath;
            $validated['cover_original_name'] = $originalName;
            $validated['cover_image'] = null; // Очищаем внешнюю ссылку
        }

        // Обработка файлов выпуска (обложка) - для обратной совместимости
        $issueFiles = [];
        if ($request->filled('cover_image') && !$request->hasFile('cover_image_file')) {
            $issueFiles['cover'] = $request->input('cover_image');
        }
        $validated['issue_files'] = !empty($issueFiles) ? $issueFiles : null;

        Issue::create($validated);

        return redirect()->route('admin.issues.index')->with('success', 'Выпуск успешно создан');
    }


    public function show(Issue $issue)
    {
        $issue->load(['articles' => function ($query) {
            $query->orderBy('sort_order')->orderBy('id');
        }, 'articles.authors']);

        // Рассчитываем страницы выпуска, если не заданы
        if (!$issue->issue_pages) {
            $issue->issue_pages = $issue->calculateIssuePages();
        }

        return view('admin.issues.show', compact('issue'));
    }

    public function edit(Issue $issue)
    {
        return view('admin.issues.edit', compact('issue'));
    }


    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'issn' => 'nullable|string|max:9',
            'eissn' => 'nullable|string|max:9',
            'volume' => 'nullable|string|max:50',
            'number' => 'nullable|string|max:50',
            'alt_number' => 'nullable|string|max:50',
            'part' => 'nullable|integer|min:1|max:999',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'month' => 'nullable|string|max:255',
            'issue_pages' => 'nullable|string|max:50',
            'issue_type' => 'required|string|in:ISS,OFI,SPI',
            'title' => 'nullable|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'doi' => 'nullable|string|max:100',
            'issue_doi' => 'nullable|string|max:100',
            'edn' => 'nullable|string|max:6',
            'published_at' => 'nullable|date',
            'pdf_url' => 'nullable|url|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:102400',
            'delete_pdf' => 'nullable|boolean',
            'cover_image' => 'nullable|url|max:2048',
            'cover_image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'delete_cover' => 'nullable|boolean', // Добавьте для удаления обложки
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Обработка удаления PDF
        if ($request->boolean('delete_pdf') && $issue->pdf_file_path) {
            Storage::disk('public')->delete($issue->pdf_file_path);
            $validated['pdf_file_path'] = null;
            $validated['pdf_original_name'] = null;
            $validated['pdf_file_size'] = null;
        }

        // Обработка загрузки нового PDF файла
        if ($request->hasFile('pdf_file')) {
            if ($issue->pdf_file_path) {
                Storage::disk('public')->delete($issue->pdf_file_path);
            }
            $file = $request->file('pdf_file');
            $originalName = $file->getClientOriginalName();
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $filePath = $file->storeAs('issue_pdfs', $newFileName, 'public');

            $validated['pdf_file_path'] = $filePath;
            $validated['pdf_original_name'] = $originalName;
            $validated['pdf_file_size'] = $file->getSize();
            $validated['pdf_url'] = null;
        }

        // Обработка удаления обложки (НОВОЕ)
        if ($request->boolean('delete_cover') && $issue->cover_image_path) {
            Storage::disk('public')->delete($issue->cover_image_path);
            $validated['cover_image_path'] = null;
            $validated['cover_original_name'] = null;
        }

        // Обработка загрузки новой обложки (НОВОЕ)
        if ($request->hasFile('cover_image_file')) {
            if ($issue->cover_image_path) {
                Storage::disk('public')->delete($issue->cover_image_path);
            }
            $file = $request->file('cover_image_file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $newFileName = time() . '_cover_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            $filePath = $file->storeAs('issue_covers', $newFileName, 'public');

            $validated['cover_image_path'] = $filePath;
            $validated['cover_original_name'] = $originalName;
            $validated['cover_image'] = null;
        }

        // Обработка файлов выпуска
        $issueFiles = $issue->issue_files ?? [];
        if ($request->filled('cover_image') && !$request->hasFile('cover_image_file')) {
            $issueFiles['cover'] = $request->input('cover_image');
            $validated['cover_image_path'] = null; // Очищаем загруженный файл
        }
        $validated['issue_files'] = !empty($issueFiles) ? $issueFiles : null;

        $issue->update($validated);

        return redirect()->route('admin.issues.index')->with('success', 'Выпуск успешно обновлен');
    }

    
    public function destroy(Issue $issue)
    {
        // Проверяем, есть ли статьи в выпуске
        if ($issue->articles()->count() > 0) {
            return back()->with('error', 'Нельзя удалить выпуск, в котором есть статьи. Сначала удалите или переместите статьи.');
        }

        // Удаляем PDF файл, если он есть
        if ($issue->pdf_file_path) {
            Storage::disk('public')->delete($issue->pdf_file_path);
        }

        $issue->delete();

        return redirect()->route('admin.issues.index')->with('success', 'Выпуск удален');
    }

    // Экспорт выпуска в XML формате РИНЦ
    public function exportXml(Issue $issue)
    {
        $issue->load([
            'articles' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }
        ]);

        // Загружаем авторов для каждой статьи прямым запросом
        $articleIds = $issue->articles->pluck('id');
        $authors = \App\Models\ArticleAuthor::whereIn('article_id', $articleIds)
            ->orderBy('article_id')
            ->orderBy('author_num')
            ->get()
            ->groupBy('article_id');

        // Прикрепляем авторов к каждой статье
        foreach ($issue->articles as $article) {
            $article->setRelation('authors', $authors->get($article->id, collect()));
        }

        // Генерируем XML по схеме journal.xsd
        $xml = $this->generateRincXml($issue);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=issue_{$issue->id}_rinc.xml");
    }

    // Экспорт последнего выпуска в XML формате РИНЦ
    public function exportLatestXml()
    {
        $issue = Issue::query()
            ->where('is_published', true)
            ->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->firstOrFail();

        return redirect()->route('admin.issues.export.xml', $issue);
    }

    private function generateRincXml(Issue $issue)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->setIndentString('  ');

        // Корневой элемент journal
        $xml->startElement('journal');

        // titleid - идентификатор журнала в системе РИНЦ (обязательный)
        $xml->writeElement('titleid', '91713817');

        // issn (опциональный)
        if ($issue->issn) {
            $xml->writeElement('issn', $issue->issn);
        }

        // eissn (опциональный)
        if ($issue->eissn) {
            $xml->writeElement('eissn', $issue->eissn);
        }

        // journalInfo (обязательный) - сведения о журнале
        $xml->startElement('journalInfo');
        $xml->writeElement('title', 'Современная гуманитаристика');
        $xml->endElement(); // journalInfo

        // issue (обязательный) - данные о выпуске
        $xml->startElement('issue');

        // volume (опциональный)
        if ($issue->volume) {
            $xml->writeElement('volume', (string) $issue->volume);
        }

        // number (опциональный)
        if ($issue->number) {
            $xml->writeElement('number', (string) $issue->number);
        }

        // altNumber (опциональный)
        if ($issue->alt_number) {
            $xml->writeElement('altNumber', (string) $issue->alt_number);
        }

        // part (опциональный)
        if ($issue->part) {
            $xml->writeElement('part', (string) $issue->part);
        }

        // pages (опциональный) - диапазон страниц выпуска
        if ($issue->issue_pages) {
            $xml->writeElement('pages', $issue->issue_pages);
        }

        // dateUni (обязательный) - год издания
        $xml->writeElement('dateUni', (string) $issue->year);

        // issTitle (опциональный) - название/тема выпуска
        if ($issue->title) {
            $xml->startElement('issTitle');
            $xml->writeAttribute('lang', 'RUS');
            $xml->text($issue->title);
            $xml->endElement();
        }

        // codes (опциональный) - коды выпуска
        if ($issue->issue_doi || $issue->edn) {
            $xml->startElement('codes');
            
            if ($issue->issue_doi) {
                $xml->writeElement('doi', $issue->issue_doi);
            }
            
            if ($issue->edn) {
                $xml->writeElement('edn', $issue->edn);
            }
            
            $xml->endElement(); // codes
        }

        // Загружаем авторов для всех статей одним запросом
        $articleIds = $issue->articles->pluck('id');
        $allAuthors = ArticleAuthor::whereIn('article_id', $articleIds)
            ->orderBy('article_id')
            ->orderBy('author_num')
            ->get()
            ->groupBy('article_id');

        // articles (обязательный) - список статей
        $xml->startElement('articles');

        foreach ($issue->articles as $article) {
            $authorsForArticle = isset($allAuthors[$article->id]) ? $allAuthors[$article->id] : collect();
            $this->generateArticleXml($xml, $article, $authorsForArticle);
        }

        $xml->endElement(); // articles
        $xml->endElement(); // issue
        $xml->endElement(); // journal

        return $xml->outputMemory();
    }

    private function generateArticleXml(\XMLWriter $xml, $article, $authors = null)
    {
        // article (обязательный)
        $xml->startElement('article');

        // pages (обязательный) - диапазон страниц статьи
        if ($article->pages) {
            $xml->writeElement('pages', $article->pages);
        }

        // artType (обязательный) - тип статьи
        $artType = $article->art_type ?? 'RAR';
        $xml->writeElement('artType', $artType);

        // langPubl (опциональный) - язык публикации
        if ($article->lang_publ) {
            $xml->writeElement('langPubl', $article->lang_publ);
        }

        // authors (опциональный) - список авторов
        if ($authors && $authors->isNotEmpty()) {
            $xml->startElement('authors');
            
            foreach ($authors as $author) {
                $this->generateAuthorXml($xml, $author);
            }
            
            $xml->endElement(); // authors
        }

        // artTitles (обязательный) - названия статьи на разных языках
        $xml->startElement('artTitles');
        
        if ($article->title_ru) {
            $xml->startElement('artTitle');
            $xml->writeAttribute('lang', 'RUS');
            $xml->text($article->title_ru);
            $xml->endElement();
        }
        
        if ($article->title_en) {
            $xml->startElement('artTitle');
            $xml->writeAttribute('lang', 'ENG');
            $xml->text($article->title_en);
            $xml->endElement();
        }
        
        if ($article->title_cv) {
            $xml->startElement('artTitle');
            $xml->writeAttribute('lang', 'CHV');
            $xml->text($article->title_cv);
            $xml->endElement();
        }
        
        $xml->endElement(); // artTitles

        // abstracts (опциональный) - аннотации
        if ($article->abstract_ru || $article->abstract_en || $article->abstract_cv) {
            $xml->startElement('abstracts');
            
                if ($article->abstract_ru) {
                    $xml->startElement('abstract');
                    $xml->writeAttribute('lang', 'RUS');
                    $xml->text(strip_tags($article->abstract_ru));
                    $xml->endElement();
                }
                
                if ($article->abstract_en) {
                    $xml->startElement('abstract');
                    $xml->writeAttribute('lang', 'ENG');
                    $xml->text(strip_tags($article->abstract_en));
                    $xml->endElement();
                }
                
                if ($article->abstract_cv) {
                    $xml->startElement('abstract');
                    $xml->writeAttribute('lang', 'CHV');
                    $xml->text(strip_tags($article->abstract_cv));
                    $xml->endElement();
                }
            
            $xml->endElement(); // abstracts
        }

        // text (обязательный) - текст статьи на разных языках
        if ($article->text_ru || $article->text_en || $article->text_cv) {
            if ($article->text_ru) {
                    $xml->startElement('text');
                    $xml->writeAttribute('lang', 'RUS');
                    $xml->text(strip_tags($article->text_ru));
                    $xml->endElement();
                }
                
                if ($article->text_en) {
                    $xml->startElement('text');
                    $xml->writeAttribute('lang', 'ENG');
                    $xml->text(strip_tags($article->text_en));
                    $xml->endElement();
                }
                
                if ($article->text_cv) {
                    $xml->startElement('text');
                    $xml->writeAttribute('lang', 'CHV');
                    $xml->text(strip_tags($article->text_cv));
                    $xml->endElement();
                }
        }

        // codes (опциональный) - коды статьи
        if ($article->doi || $article->edn || $article->udk || $article->bbk || 
            $article->vak || $article->vak21 || $article->jel || $article->msc || 
            $article->pacs || $article->anycode) {
            
            $xml->startElement('codes');
            
            if ($article->doi) {
                $xml->writeElement('doi', $article->doi);
            }
            if ($article->edn) {
                $xml->writeElement('edn', $article->edn);
            }
            if ($article->udk) {
                foreach ((array) $article->udk as $udk) {
                    $xml->writeElement('udk', $udk);
                }
            }
            if ($article->bbk) {
                foreach ((array) $article->bbk as $bbk) {
                    $xml->writeElement('bbk', $bbk);
                }
            }
            if ($article->vak) {
                $xml->writeElement('vak', $article->vak);
            }
            if ($article->vak21) {
                $xml->writeElement('vak21', $article->vak21);
            }
            if ($article->jel) {
                foreach ((array) $article->jel as $jel) {
                    $xml->writeElement('jel', $jel);
                }
            }
            if ($article->msc) {
                foreach ((array) $article->msc as $msc) {
                    $xml->writeElement('msc', $msc);
                }
            }
            if ($article->pacs) {
                foreach ((array) $article->pacs as $pacs) {
                    $xml->writeElement('pacs', $pacs);
                }
            }
            if ($article->anycode) {
                foreach ((array) $article->anycode as $anycode) {
                    $xml->writeElement('anycode', $anycode);
                }
            }
            
            $xml->endElement(); // codes
        }

        // keywords (опциональный) - ключевые слова
        if ($article->keywords_ru || $article->keywords_en || $article->keywords_cv) {
            $xml->startElement('keywords');
            
            if ($article->keywords_ru) {
                $xml->startElement('kwdGroup');
                $xml->writeAttribute('lang', 'RUS');
                foreach (explode(',', $article->keywords_ru) as $keyword) {
                    $keyword = trim($keyword);
                    if ($keyword) {
                        $xml->startElement('keyword');
                        $xml->text($keyword);
                        $xml->endElement();
                    }
                }
                $xml->endElement(); // kwdGroup
            }
            
            if ($article->keywords_en) {
                $xml->startElement('kwdGroup');
                $xml->writeAttribute('lang', 'ENG');
                foreach (explode(',', $article->keywords_en) as $keyword) {
                    $keyword = trim($keyword);
                    if ($keyword) {
                        $xml->startElement('keyword');
                        $xml->text($keyword);
                        $xml->endElement();
                    }
                }
                $xml->endElement(); // kwdGroup
            }
            
            if ($article->keywords_cv) {
                $xml->startElement('kwdGroup');
                $xml->writeAttribute('lang', 'CHV');
                foreach (explode(',', $article->keywords_cv) as $keyword) {
                    $keyword = trim($keyword);
                    if ($keyword) {
                        $xml->startElement('keyword');
                        $xml->text($keyword);
                        $xml->endElement();
                    }
                }
                $xml->endElement(); // kwdGroup
            }
            
            $xml->endElement(); // keywords
        }

        // references (опциональный) - список литературы
        if ($article->references_ru || $article->references_en) {
            $xml->startElement('references');
            
            $references = [];
            if ($article->references_ru && is_array($article->references_ru)) {
                $references = array_merge($references, $article->references_ru);
            }
            if ($article->references_en && is_array($article->references_en)) {
                $references = array_merge($references, $article->references_en);
            }
            
            foreach ($references as $index => $refText) {
                if (empty($refText)) continue;
                $xml->startElement('reference');
                $xml->startElement('refInfo');
                $xml->writeAttribute('lang', 'RUS');
                $xml->startElement('text');
                $xml->text($refText);
                $xml->endElement(); // text
                $xml->endElement(); // refInfo
                $xml->endElement(); // reference
            }
            
            $xml->endElement(); // references
        }

        // dates (опциональный) - даты статьи
        if ($article->date_received || $article->date_accepted || $article->date_publication) {
            $xml->startElement('dates');
            
            if ($article->date_received) {
                $xml->writeElement('dateReceived', $article->date_received->format('d.m.Y'));
            }
            if ($article->date_accepted) {
                $xml->writeElement('dateAccepted', $article->date_accepted->format('d.m.Y'));
            }
            if ($article->date_publication) {
                $xml->writeElement('datePublication', $article->date_publication->format('d.m.Y'));
            }
            
            $xml->endElement(); // dates
        }

        $xml->endElement(); // article
    }

    private function generateAuthorXml(\XMLWriter $xml, $author)
    {
        $xml->startElement('author');
        
        // num (обязательный) - порядковый номер автора
        $xml->writeAttribute('num', (string) $author->author_num);
        
        // id (опциональный) - идентификатор в elibrary
        if ($author->author_id) {
            $xml->writeAttribute('id', (string) $author->author_id);
        }

        // role (опциональный) - роль автора
        if ($author->role) {
            $xml->writeElement('role', (string) $author->role);
        }

        // correspondent (опциональный) - автор-корреспондент
        if ($author->is_correspondent) {
            $xml->writeElement('correspondent', '1');
        }

        // authorCodes (опциональный) - идентификационные коды
        if ($author->researcherid || $author->spin || $author->scopusid || $author->orcid) {
            $xml->startElement('authorCodes');
            
            if ($author->researcherid) {
                $xml->writeElement('researcherid', $author->researcherid);
            }
            if ($author->spin) {
                $xml->writeElement('spin', $author->spin);
            }
            if ($author->scopusid) {
                $xml->writeElement('scopusid', $author->scopusid);
            }
            if ($author->orcid) {
                $xml->writeElement('orcid', $author->orcid);
            }
            
            $xml->endElement(); // authorCodes
        }

        // individInfo (1-3 раза) - индивидуальные сведения об авторе
        $locales = ['ru' => 'RUS', 'en' => 'ENG', 'cv' => 'CHV'];
        
        foreach ($locales as $locale => $langCode) {
            $surname = $author->{"surname_{$locale}"};
            $name = $author->{"name_{$locale}"};
            $patronymic = $author->{"patronymic_{$locale}"};
            
            // Пропускаем, если нет данных на этом языке
            if (!$surname && !$name && !$patronymic) {
                continue;
            }
            
            $xml->startElement('individInfo');
            $xml->writeAttribute('lang', $langCode);
            
            // surname (обязательный)
            if ($surname) {
                $xml->startElement('surname');
                $xml->text($surname);
                $xml->endElement();
            }
            
            // initials (опциональный)
            $initials = $author->{"initials_{$locale}"};
            if ($initials) {
                $xml->startElement('initials');
                $xml->text($initials);
                $xml->endElement();
            }
            
            // address (опциональный)
            $address = $author->{"address_{$locale}"};
            if ($address) {
                $xml->startElement('address');
                $xml->text($address);
                $xml->endElement();
            }
            
            // town (опциональный)
            $town = $author->{"town_{$locale}"};
            if ($town) {
                $xml->startElement('town');
                $xml->text($town);
                $xml->endElement();
            }
            
            // country (опциональный)
            $country = $author->{"country_{$locale}"};
            if ($country) {
                $xml->startElement('country');
                $xml->text($country);
                $xml->endElement();
            }
            
            // otherInfo (опциональный)
            $otherInfo = $author->{"other_info_{$locale}"};
            if ($otherInfo) {
                $xml->startElement('otherInfo');
                $xml->text($otherInfo);
                $xml->endElement();
            }
            
            // orgName (опциональный)
            $orgName = $author->{"org_name_{$locale}"};
            if ($orgName) {
                $xml->startElement('orgName');
                $xml->text($orgName);
                $xml->endElement();
            }
            
            // email (опциональный)
            if ($author->email) {
                $xml->startElement('email');
                $xml->text($author->email);
                $xml->endElement();
            }
            
            $xml->endElement(); // individInfo
        }

        $xml->endElement(); // author
    }
}
