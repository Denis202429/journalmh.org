<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Issue;
use App\Models\Article;
use App\Models\ArticleAuthor;

// Способ 1: через Issue с загрузкой
$issue = Issue::with(['articles', 'articles.authors'])->find(16);
if (!$issue) {
    echo "Issue 16 not found\n";
    exit;
}

echo "=== Способ 1: Issue::with ===\n";
echo "Issue: {$issue->id}, articles count: " . $issue->articles->count() . "\n\n";

foreach ($issue->articles as $article) {
    echo "Article ID: {$article->id}, Title: " . mb_substr($article->title_ru ?? 'N/A', 0, 50) . "...\n";
    $authorsRel = $article->authors;
    if ($authorsRel === null) {
        echo "  authors() returns NULL!\n";
    } else {
        echo "  Authors count: " . $authorsRel->count() . "\n";
        foreach ($authorsRel as $author) {
            echo "  - {$author->surname_ru} {$author->name_ru} {$author->patronymic_ru} (num: {$author->author_num})\n";
        }
    }
    echo "\n";
}

// Способ 2: прямой запрос
echo "=== Способ 2: ArticleAuthor::where ===\n";
$articles = $issue->articles;
foreach ($articles as $article) {
    $directAuthors = ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();
    echo "Article {$article->id}: direct authors count = " . $directAuthors->count() . "\n";
    foreach ($directAuthors as $author) {
        echo "  - {$author->surname_ru} {$author->name_ru} {$author->patronymic_ru}\n";
    }
}
