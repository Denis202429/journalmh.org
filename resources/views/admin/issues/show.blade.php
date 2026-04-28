@extends('layouts.base')

@section('page.title', 'Просмотр выпуска')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h2 class="mb-0">Выпуск: {{ $issue->fullTitle }}</h2>
            <div>
                <a href="{{ route('admin.issues.edit', $issue) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Редактировать
                </a>
                <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        </div>

        <!-- Информация о выпуске -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <strong>Метаданные выпуска (соответствие XSD РИНЦ)</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 150px;">Год:</th>
                                <td>{{ $issue->year }}</td>
                            </tr>
                            <tr>
                                <th>Том/Номер:</th>
                                <td>Том {{ $issue->volume ?? '-' }} / № {{ $issue->number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Сквозной номер:</th>
                                <td>{{ $issue->alt_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Часть:</th>
                                <td>{{ $issue->part ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Месяц:</th>
                                <td>{{ $issue->month ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Тип выпуска:</th>
                                <td>{{ $issue->issue_type_label }} ({{ $issue->issue_type }})</td>
                            </tr>
                            <tr>
                                <th>Страницы выпуска:</th>
                                <td>{{ $issue->issue_pages ?? $issue->calculateIssuePages() ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 150px;">ISSN:</th>
                                <td>{{ $issue->issn ?? '-' }} / {{ $issue->eissn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>DOI выпуска:</th>
                                <td>{{ $issue->issue_doi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>EDN:</th>
                                <td>{{ $issue->edn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Издатель:</th>
                                <td>{{ $issue->publisher ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Дата публикации:</th>
                                <td>{{ $issue->published_at?->format('d.m.Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Статус:</th>
                                <td>
                                    @if($issue->is_published)
                                        <span class="badge bg-success">Опубликован</span>
                                    @else
                                        <span class="badge bg-secondary">Черновик</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($issue->title)
                    <div class="mt-3">
                        <strong>Название (RU):</strong>
                        <p>{{ $issue->title }}</p>
                    </div>
                @endif
                
                @if($issue->title_en)
                    <div>
                        <strong>Название (EN):</strong>
                        <p>{{ $issue->title_en }}</p>
                    </div>
                @endif
                
                @if($issue->description)
                    <div>
                        <strong>Описание (RU):</strong>
                        <p>{{ $issue->description }}</p>
                    </div>
                @endif
                
                @if($issue->description_en)
                    <div>
                        <strong>Описание (EN):</strong>
                        <p>{{ $issue->description_en }}</p>
                    </div>
                @endif
                
                @if($issue->pdf_url || ($issue->issue_files['cover'] ?? false))
                    <div class="mt-3">
                        <strong>Файлы:</strong><br>
                        @if($issue->pdf_url)
                            <a href="{{ $issue->pdf_url }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> PDF выпуска
                            </a>
                        @endif
                        @if($issue->issue_files['cover'] ?? false)
                            <a href="{{ $issue->issue_files['cover'] }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="bi bi-image"></i> Обложка
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Список статей в выпуске -->
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Статьи в выпуске ({{ $issue->articles->count() }})</h4>
                <a href="{{ route('admin.articles.create', ['issue_id' => $issue->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Добавить статью
                </a>
            </div>
            
            @if($issue->articles->isEmpty())
                <div class="alert alert-info">В этом выпуске пока нет статей.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Название (RU)</th>
                                <th>Авторы</th>
                                <th>Страницы</th>
                                <th>DOI</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issue->articles as $index => $article)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ Str::limit($article->title_ru ?? $article->title, 60) }}</strong>
                                        @if($article->title_en)
                                            <br><small class="text-muted">{{ Str::limit($article->title_en, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $authorsList = $article->authors->pluck('surname_ru')->filter()->implode(', ');
                                            if (!$authorsList) {
                                                $authorsList = $article->authors->pluck('surname_en')->filter()->implode(', ');
                                            }
                                        @endphp
                                        {{ Str::limit($authorsList ?: $article->authors ?? '-', 50) }}
                                    </td>
                                    <td>{{ $article->pages ?? '-' }}</td>
                                    <td>
                                        @if($article->doi)
                                            <code class="small">{{ Str::limit($article->doi, 20) }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection