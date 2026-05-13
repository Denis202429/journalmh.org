<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\ArticleAuthor;

class IssuePublicController extends Controller
{
    public function index()
    {
        $issues = Issue::query()
            ->where('is_published', true)
            ->withCount(['articles' => function ($q) {
                $q->where('is_published', true);
            }])
            ->orderByDesc('year')
            ->orderByDesc('volume')
            ->orderByDesc('number')
            ->orderBy('sort_order')
            ->paginate(24);

        return view('issues.index', compact('issues'));
    }

    public function show(Issue $issue)
    {
        abort_unless($issue->is_published, 404);

        $issue->load(['articles' => function ($query) {
            $query->where('is_published', true)
                ->orderByRaw('sort_order is null, sort_order asc')
                ->orderBy('id');
        }]);

        foreach ($issue->articles as $article) {
            $authors = ArticleAuthor::where('article_id', $article->id)
                ->orderBy('author_num')
                ->get();
            $article->setRelation('authors', $authors);
        }

        return view('issues.show', compact('issue'));
    }

    // ДОБАВЬТЕ ЭТОТ МЕТОД ДЛЯ СКАЧИВАНИЯ PDF ВЫПУСКА


    // public function downloadPdf(Issue $issue)
    // {
    //     abort_unless($issue->is_published, 404);

    //     if (!$issue->pdf_file_path) {
    //         abort(404, 'PDF файл не найден');
    //     }

    //     $filePath = storage_path('app/public/' . $issue->pdf_file_path);

    //     if (!file_exists($filePath)) {
    //         abort(404, 'Файл не существует на сервере');
    //     }

    //     $downloadName = $issue->pdf_original_name;
    //     if (empty($downloadName)) {
    //         $serverFileName = basename($issue->pdf_file_path);
    //         $downloadName = preg_replace('/^\d+_/', '', $serverFileName);
    //     }

    //     if (empty($downloadName)) {
    //         $downloadName = 'issue_' . $issue->id . '.pdf';
    //     } elseif (!str_ends_with($downloadName, '.pdf')) {
    //         $downloadName .= '.pdf';
    //     }

    //     // ВАЖНО: Проверьте, что файл действительно PDF
    //     $mimeType = mime_content_type($filePath);
    //     \Log::info('File MIME type: ' . $mimeType);
    //     \Log::info('File path: ' . $filePath);
    //     \Log::info('File size: ' . filesize($filePath));

    //     return response()->download($filePath, $downloadName, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
    //     ]);
    // }

    public function downloadIssuePdf(Issue $issue)
    {
        if (!$issue->pdf_file_path) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $issue->pdf_file_path);

        if (!file_exists($filePath)) {
            abort(404);
        }

        // Формируем красивое имя для выпуска
        $downloadName = $issue->pdf_original_name;

        if (empty($downloadName)) {
            $downloadName = 'issue_' . $issue->year;
            if ($issue->volume) $downloadName .= '_vol' . $issue->volume;
            if ($issue->number) $downloadName .= '_no' . $issue->number;
            $downloadName .= '.pdf';
        }

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
        ]);
    }


    /**
     * Скачивание файла с инструкцией для авторов
     */
    public function downloadInstructions()
    {
        $filePath = storage_path('app/public/instructions/Научный журнал. Требования к статьям.pdf');

        if (!file_exists($filePath)) {
            abort(404, 'Файл инструкции не найден');
        }

        $downloadName = 'Требования_к_статьям.pdf';

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
        ]);
    }

    public function cover(Issue $issue)
    {
        try {
            // Проверяем, что выпуск опубликован
            if (!$issue->is_published) {
                abort(404, 'Выпуск не опубликован');
            }

            // Проверяем, есть ли путь к обложке
            if (empty($issue->cover_image_path)) {
                abort(404, 'Обложка не найдена');
            }

            // Полный путь к файлу
            $filePath = storage_path('app/public/' . $issue->cover_image_path);

            // Проверяем, существует ли файл
            if (!file_exists($filePath)) {
                abort(404, 'Файл обложки не существует на сервере');
            }

            // Определяем MIME тип
            $mimeType = mime_content_type($filePath);

            // Возвращаем файл
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=86400'
            ]);
        } catch (\Exception $e) {
            \Log::error('Cover error: ' . $e->getMessage());
            abort(404, 'Ошибка загрузки обложки');
        }
    }

    /**
     * Отображение текущего (последнего) выпуска
     */
    public function current()
    {
        // Находим последний опубликованный выпуск
        $currentIssue = Issue::where('is_published', true)
            ->orderByDesc('year')
            ->orderByDesc('volume')
            ->orderByDesc('number')
            ->orderByDesc('published_at')
            ->first();

        // Если выпуска нет, показываем 404 или редирект на архив
        if (!$currentIssue) {
            return redirect()->route('issues.index')->with('error', 'Нет опубликованных выпусков');
        }

        // Перенаправляем на страницу этого выпуска
        return redirect()->route('issues.show', $currentIssue);
    }
}
