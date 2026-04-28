<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticlePublicController extends Controller
{
    public function show(Article $article)
    {
        // Проверяем, что статья опубликована
        abort_unless($article->is_published, 404);

        // Загружаем связанные данные
        $article->load(['issue', 'authors']);

        return view('articles.show', compact('article'));
    }

    public function downloadPdf(Article $article)
    {
        if (!$article->pdf_file_path) {
            abort(404, 'PDF файл не найден');
        }

        $filePath = storage_path('app/public/' . $article->pdf_file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Файл не существует на сервере');
        }

        // Оригинальное имя для скачивания (извлекаем из пути, убирая временную метку)
        $serverFileName = basename($article->pdf_file_path); // 1776951610_prep2018_187.pdf
        $downloadName = preg_replace('/^\d+_/', '', $serverFileName); // prep2018_187.pdf

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
        ]);
    }
}
