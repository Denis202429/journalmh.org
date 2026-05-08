<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IssuePublicController;
use App\Http\Controllers\ArticlePublicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitePageController;
use App\Models\Issue;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $issues = Issue::query()
        ->where('is_published', true)
        ->orderByDesc('year')
        ->orderByDesc('number')
        ->orderByDesc('published_at')
        ->limit(12)
        ->get();

    return view('home.index', compact('issues'));
})->name('home');

Route::get('/show_archiv', function () {
    $issues = Issue::query()
        ->where('is_published', true)
        ->orderByDesc('year')
        ->orderByDesc('number')
        ->orderByDesc('published_at')
        ->paginate(12);  // Изменили limit(12) на paginate(12)
    return view('pages.show_archiv', compact('issues'));
})->name('show_archiv');
Route::get('/issues', [IssuePublicController::class, 'index'])->name('issues.index');
Route::get('/issues/{issue}', [IssuePublicController::class, 'show'])->name('issues.show');
Route::get('/journal/{slug}', [SitePageController::class, 'show'])->name('journal.page');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::prefix('issues')->name('issues.')->group(function () {
    Route::get('/', [IssuePublicController::class, 'index'])->name('index');
    Route::get('/{issue}', [IssuePublicController::class, 'show'])->name('show');
});
// Добавьте этот блок для статей
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/{article}', [ArticlePublicController::class, 'show'])->name('show');
});
require __DIR__ . '/main.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты для переключения языка
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ru', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');
// Маршруты для скачивания PDF
Route::get('/download-pdf/{article}', [ArticlePublicController::class, 'downloadPdf'])->name('download.pdf');
Route::get('/download-issue-pdf/{issue}', [IssuePublicController::class, 'downloadIssuePdf'])->name('download.issue.pdf');
// Маршрут для отображения обложки
Route::get('/issue-cover/{issue}', [IssuePublicController::class, 'cover'])->name('issue.cover');
// Маршрут для текущего номера
Route::get('/current-issue', [IssuePublicController::class, 'current'])->name('current.issue');

require __DIR__ . '/auth.php';
