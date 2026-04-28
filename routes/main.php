<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BdController;
use App\Http\Controllers\FRController;
use App\Http\Controllers\ParallelController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\MorphController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GigachatController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\IssueController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\SitePageController;
use App\Http\Controllers\Admin\SiteDesignController;

Route::get('bd', [BdController::class, 'index'])->name('bd');
Route::get('bdlast', [BdController::class, 'index2'])->name('bd.last');
Route::get('bd/show', [BdController::class, 'show'])->name('bd.show');
Route::post('bd', [BdController::class, 'store'])->name('bd.store');

Route::get('bd/info_user', [BdController::class, 'info_user'])->name('bd.info_user');

Route::get('bd/info_admin', [BdController::class, 'info_admin'])->name('bd.info_admin');

Route::get('admin/BD_edit_index', [BdController::class, 'BD_edit_index'])->name('admin.BD_edit_index');

Route::get('admin/BD_edit/{id}', [BdController::class, 'BD_edit'])->name('admin.BD_edit');

Route::put('admin/Publication_update/{id}', [BdController::class, 'Publication_update_record'])->name('admin.Publication_update_record');

Route::post('bd/update_admin', [BdController::class, 'update_admin'])->name('bd.update_admin');


Route::get('bd/find', [BdController::class, 'find'])->name('bd.find');
Route::get('bd/find2', [BdController::class, 'find2'])->name('bd.find2');
Route::post('FR2', [FRController::class, 'store2'])->name('FR.store2');
Route::get('FR_parallel_index', [FRController::class, 'index_parallel'])->name('FR.index_parallel');
Route::post('FR_parallel', [FRController::class, 'store_parallel'])->name('FR.store_parallel');
Route::post('FR_parallel2', [FRController::class, 'store_parallel2'])->name('FR.store_parallel2');
Route::post('FR', [FRController::class, 'store'])->name('FR.store');
Route::get('Parallel', [ParallelController::class, 'index'])->name('Parallel.index');
Route::get('Parallel/last', [ParallelController::class, 'index2'])->name('Parallel.last');
Route::get('Parlalel/create', [ParallelController::class, 'create'])->name('Parallel.create');
Route::post('Parallel/store', [ParallelController::class, 'store'])->name('Parallel.store');
Route::get('Parallel/find', [ParallelController::class, 'find'])->name('Parallel.find');
Route::get('Parallel/find2', [ParallelController::class, 'find2'])->name('Parallel.find2');
Route::get('/change-locale/{locale}', [LocalizationController::class, 'changeLocale'])->name('change.locale');

Route::get('Bd/category/{category}', [BdController::class, 'category'])->name('Bd.category');
Route::get('Bd/genre/{genre}', [BdController::class, 'genre'])->name('Bd.genre');

Route::get('Morph', [MorphController::class, 'index'])->name('Morph.index');
Route::post('Morph', [MorphController::class, 'Analysis'])->name('Morph.Analysis');


Route::get('/test', [TestController::class, 'index'])->name('test');


Route::get('user', [AuthController::class, 'welcome_user'])->name('user');

Route::get('bd/create', [BdController::class, 'create'])->name('bd.create');
Route::get('FR', [FRController::class, 'index'])->name('FR.index');

Route::get('korr/main_edit_index', [BdController::class, 'korr_main_edit_index'])->name('korr.main_edit_index');

Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
Route::put('admin/users/{id}', [UserController::class, 'updateAdminStatus'])->name('admin.users.update');
Route::delete('admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

// Админка выпусков журнала (админ / суперадмин)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('issues', [IssueController::class, 'index'])->name('issues.index');
    Route::get('issues/create', [IssueController::class, 'create'])->name('issues.create');
    Route::post('issues', [IssueController::class, 'store'])->name('issues.store');
    Route::get('issues/{issue}/edit', [IssueController::class, 'edit'])->name('issues.edit');
    Route::put('issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
    Route::delete('issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
    Route::get('issues/{issue}', [IssueController::class, 'show'])->name('issues.show');

    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('content/edit', [SiteContentController::class, 'edit'])->name('content.edit');
    Route::put('content', [SiteContentController::class, 'update'])->name('content.update');

    Route::get('pages', [SitePageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page}/edit', [SitePageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [SitePageController::class, 'update'])->name('pages.update');

    Route::get('design', [SiteDesignController::class, 'edit'])->name('design.edit');
    Route::put('design', [SiteDesignController::class, 'update'])->name('design.update');
});

// Роуты для гигачата (только для корректоров)
Route::get('gigachat', [GigachatController::class, 'index'])->name('gigachat.index');
Route::get('gigachat/category/{category}', [GigachatController::class, 'category'])->name('gigachat.category');
Route::get('gigachat/create/{category}', [GigachatController::class, 'create'])->name('gigachat.create');
Route::post('gigachat/store', [GigachatController::class, 'store'])->name('gigachat.store');
Route::get('gigachat/edit/{id}', [GigachatController::class, 'edit'])->name('gigachat.edit');
Route::put('gigachat/update/{id}', [GigachatController::class, 'update'])->name('gigachat.update');

Route::delete('/gigachat/{id}', [GigachatController::class, 'destroy'])
    ->name('gigachat.destroy')
    ->middleware('auth');

// Общий рабочий чат (корректоры / админы / суперадмины)
Route::middleware(['auth', 'chat.access'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/messages/{id}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::get('/chat/attachment/{path}', [ChatController::class, 'attachment'])
        ->where('path', '.*')
        ->name('chat.attachment');
});
