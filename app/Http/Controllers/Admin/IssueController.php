<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Добавьте этот импорт

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
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
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
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
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
        $issue->load(['articles', 'articles.authors']);

        // Генерируем XML по схеме journal.xsd
        $xml = $this->generateRincXml($issue);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', "attachment; filename=issue_{$issue->id}_rinc.xml");
    }

    private function generateRincXml(Issue $issue)
    {
        // Здесь будет генерация XML по схеме
        // Этот метод мы реализуем позже
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!-- XML generation will be implemented -->";
    }
}
