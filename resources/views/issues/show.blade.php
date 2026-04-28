@extends('layouts.base')

@section('page.title', $issue->fullTitle ?? 'Выпуск')

@section('content')
<div class="container mt-4">
    <!-- Информация о выпуске -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h3 mb-0">{{ $issue->fullTitle }}</h1>
            <!-- Кнопка скачивания PDF выпуска -->
            @if($issue->pdf_file_path)
            <a href="{{ route('download.issue.pdf', $issue) }}" class="btn btn-success">
                <i class="bi bi-file-pdf"></i> Скачать весь выпуск PDF
            </a>
            @elseif($issue->pdf_url)
            <a href="{{ $issue->pdf_url }}" target="_blank" class="btn btn-success">
                <i class="bi bi-file-pdf"></i> Открыть PDF выпуска
            </a>
            @endif
        </div>

        <!-- ========== БЛОК С ОБЛОЖКОЙ ========== -->
        @if($issue->cover_image_path)
        <div class="text-center pt-3">
            <img src="{{ route('issue.cover', $issue) }}" alt="Обложка выпуска" class="img-fluid" style="max-width: 300px; max-height: 400px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
        @elseif($issue->cover_image)
        <div class="text-center pt-3">
            <img src="{{ $issue->cover_image }}" alt="Обложка выпуска" class="img-fluid" style="max-width: 300px; max-height: 400px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
        @endif
        <!-- ================================================ -->

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 150px;">Год:</th>
                            <td>{{ $issue->year }}
                        </tr>
                        @if($issue->volume || $issue->number)
                        <tr>
                            <th>Том/Номер:</th>
                            <td>Том {{ $issue->volume ?? '-' }} / № {{ $issue->number ?? '-' }}
                        </tr>
                        @endif
                        @if($issue->alt_number)<tr>
                            <th>Сквозной номер:</th>
                            <td>{{ $issue->alt_number }}
                        </tr>@endif
                        @if($issue->part)<tr>
                            <th>Часть:</th>
                            <td>{{ $issue->part }}
                        </tr>@endif
                        @if($issue->month)<tr>
                            <th>Месяц:</th>
                            <td>{{ $issue->month }}
                        </tr>@endif
                        <tr>
                            <th>Тип выпуска:</th>
                            <td><span class="badge bg-secondary">{{ $issue->issue_type_label }}</span>
                        </tr>
                        @if($issue->issue_pages)<tr>
                            <th>Страницы выпуска:</th>
                            <td>{{ $issue->issue_pages }}
                        </tr>@endif
                        @if($issue->published_at)<tr>
                            <th>Дата публикации:</th>
                            <td>{{ $issue->published_at->format('d.m.Y') }}
                        </tr>@endif
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        @if($issue->issn || $issue->eissn)
                        <tr>
                            <th style="width: 150px;">ISSN:</th>
                            <td>@if($issue->issn) print: {{ $issue->issn }}@endif @if($issue->eissn) online: {{ $issue->eissn }}@endif
                        </tr>
                        @endif
                        @if($issue->issue_doi)<tr>
                            <th>DOI выпуска:</th>
                            <td><code>{{ $issue->issue_doi }}</code>
                        </tr>@endif
                        @if($issue->edn)<tr>
                            <th>EDN:</th>
                            <td><code>{{ $issue->edn }}</code>
                        </tr>@endif
                        @if($issue->publisher)<tr>
                            <th>Издатель:</th>
                            <td>{{ $issue->publisher }}
                        </tr>@endif
                        <tr>
                            <th>Статей в выпуске:</th>
                            <td><span class="badge bg-info">{{ $issue->articles->count() }}</span>
                        </tr>
                    </table>
                </div>
            </div>

            @if($issue->title)
            <div class="mt-3">
                <h5>Название выпуска</h5>
                <p class="mb-0"><strong>Русский:</strong> {{ $issue->title }}</p>
                @if($issue->title_en)<p class="mb-0"><strong>English:</strong> {{ $issue->title_en }}</p>@endif
            </div>
            @endif

            @if($issue->description)
            <div class="mt-3">
                <h5>Описание выпуска</h5>
                <p class="mb-0">{{ $issue->description }}</p>
                @if($issue->description_en)<p class="mb-0 text-muted">{{ $issue->description_en }}</p>@endif
            </div>
            @endif
        </div>
    </div>

    <!-- Список статей в выпуске -->
    <div class="mt-4">
        <h2 class="h4 mb-3">Статьи выпуска</h2>

        @if($issue->articles->isEmpty())
        <div class="alert alert-info">В этом выпуске пока нет статей.</div>
        @else
        <div class="articles-list">
            @foreach($issue->articles as $index => $article)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="me-3 text-center" style="min-width: 50px;">
                            <span class="badge bg-secondary rounded-circle p-2">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="h5 mb-1">{{ $article->title_ru ?? $article->title }}</h3>

                            @if($article->title_en && $article->title_en != ($article->title_ru ?? $article->title))
                            <div class="text-muted small mb-2"><i>{{ $article->title_en }}</i></div>
                            @endif

                            <!-- Авторы -->
                            <div class="mb-2">
                                <strong>Авторы:</strong>
                                <span class="text-muted">
                                    @php
                                    $authorsString = '-';
                                    $authorsList = \App\Models\ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();
                                    if ($authorsList->count() > 0) {
                                    $authorNames = [];
                                    foreach ($authorsList as $author) {
                                    $name = $author->surname_ru ?? $author->surname_en ?? '';
                                    if ($name) $authorNames[] = $name;
                                    }
                                    $authorsString = implode(', ', $authorNames);
                                    }
                                    @endphp
                                    {{ $authorsString }}
                                </span>
                            </div>

                            <!-- Метаданные статьи -->
                            <div class="row small text-muted mt-2">
                                <div class="col-md-6">
                                    @if($article->pages)<span><i class="bi bi-file-text"></i> Страницы: {{ $article->pages }}</span><br>@endif
                                    @if($article->art_type)<span><i class="bi bi-tag"></i> Тип: @switch($article->art_type) @case('RAR') Научная статья @break @case('REV') Обзорная статья @break @case('SCO') Краткое сообщение @break @case('BRV') Рецензия @break @default {{ $article->art_type }} @endswitch</span><br>@endif
                                    @if($article->lang_publ)<span><i class="bi bi-globe"></i> Язык: @switch($article->lang_publ) @case('RUS') Русский @break @case('ENG') English @break @case('CHV') Чăваш @break @default {{ $article->lang_publ }} @endswitch</span>@endif
                                </div>
                                <div class="col-md-6">
                                    @if($article->doi)<span><i class="bi bi-link-45deg"></i> DOI: <code>{{ $article->doi }}</code></span><br>@endif
                                    @if($article->udk)<span><i class="bi bi-hash"></i> УДК: {{ is_array($article->udk) ? implode(', ', $article->udk) : $article->udk }}</span><br>@endif
                                    @if($article->keywords_ru)<span><i class="bi bi-tags"></i> Ключевые слова: {{ Str::limit($article->keywords_ru, 80) }}</span>@endif
                                </div>
                            </div>

                            <!-- Аннотация -->
                            @if($article->abstract_ru)
                            <div class="mt-2 small">
                                <strong>Аннотация:</strong>
                                <p class="text-muted mb-0">{{ Str::limit($article->abstract_ru, 200) }}</p>
                            </div>
                            @endif

                            <!-- Полный текст статьи -->
                            @if($article->text_ru)
                            <div class="mt-3">
                                <strong>Текст статьи:</strong>
                                <div class="article-text mt-2" style="max-height: 400px; overflow-y: auto;">
                                    {!! $article->text_ru !!}
                                </div>
                            </div>
                            @endif

                            <!-- Кнопка скачивания PDF статьи -->
                            @if($article->pdf_file_path)
                            <div class="mt-3">
                                <a href="{{ route('download.pdf', $article) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-file-pdf"></i> Скачать PDF статьи
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection