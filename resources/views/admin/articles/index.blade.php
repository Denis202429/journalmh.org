@extends('layouts.base')

@section('page.title', 'Статьи')

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h2 class="mb-0">Статьи (РИНЦ метаданные)</h2>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Добавить статью
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Расширенный фильтр -->
    <form method="GET" class="mt-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Фильтр по выпуску</label>
                <select class="form-select" name="issue_id">
                    <option value="">Все выпуски</option>
                    @foreach($issues as $issue)
                    <option value="{{ $issue->id }}" {{ request('issue_id') == $issue->id ? 'selected' : '' }}>
                        {{ $issue->year }}
                        @if($issue->volume) • Том {{ $issue->volume }}@endif
                        @if($issue->number) • № {{ $issue->number }}@endif
                        @if($issue->title) • {{ $issue->title }}@endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Тип статьи</label>
                <select class="form-select" name="art_type">
                    <option value="">Все типы</option>
                    <option value="RAR" {{ request('art_type') == 'RAR' ? 'selected' : '' }}>Научная статья</option>
                    <option value="REV" {{ request('art_type') == 'REV' ? 'selected' : '' }}>Обзорная статья</option>
                    <option value="SCO" {{ request('art_type') == 'SCO' ? 'selected' : '' }}>Краткое сообщение</option>
                    <option value="BRV" {{ request('art_type') == 'BRV' ? 'selected' : '' }}>Рецензия</option>
                    <option value="CNF" {{ request('art_type') == 'CNF' ? 'selected' : '' }}>Материалы конференции</option>
                    <option value="EDI" {{ request('art_type') == 'EDI' ? 'selected' : '' }}>Редакторская заметка</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select class="form-select" name="is_published">
                    <option value="">Все</option>
                    <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Опубликована</option>
                    <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Не опубликована</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Наличие DOI</label>
                <select class="form-select" name="has_doi">
                    <option value="">Все</option>
                    <option value="1" {{ request('has_doi') == '1' ? 'selected' : '' }}>Есть DOI</option>
                    <option value="0" {{ request('has_doi') == '0' ? 'selected' : '' }}>Нет DOI</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Поиск</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Название/авторы">
            </div>

            <div class="col-md-12 d-flex gap-2 mt-2">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-funnel"></i> Применить фильтры
                </button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.articles.index') }}">
                    Сбросить все
                </a>
            </div>
        </div>
    </form>

    @if($articles->isEmpty())
    <div class="alert alert-info mt-3">Пока нет ни одной статьи.</div>
    @else
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Выпуск</th>
                    <th>Название (RU/EN)</th>
                    <th>Авторы</th>
                    <th>Тип</th>
                    <th>Стр.</th>
                    <th>DOI</th>
                    <th>Язык</th>
                    <th>Статус</th>
                    <th>РИНЦ</th>
                    <th>PDF</th>
                    <th style="min-width: 180px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>
                        <strong>{{ $article->issue?->year }}</strong><br>
                        @if($article->issue?->volume) Том {{ $article->issue->volume }}@endif
                        @if($article->issue?->number) № {{ $article->issue->number }}@endif
                        <small class="text-muted d-block">ID: {{ $article->issue_id }}</small>
                    </td>
                    <td>
                        <strong title="{{ $article->title_ru ?? $article->title }}">{{ Str::limit($article->title_ru ?? $article->title, 50) }}</strong>
                        @if($article->title_en)
                        <br><small class="text-muted">{{ Str::limit($article->title_en, 50) }}</small>
                        @endif
                    </td>

                    <td style="max-width: 200px;">
                        @php
                        $authorsList = '';
                        // Проверяем, загружены ли авторы и есть ли они
                        if ($article->relationLoaded('authors') && $article->authors && $article->authors->count() > 0) {
                        $authorsList = $article->authors->map(function($author) {
                        return $author->surname_ru ?: $author->surname_en;
                        })->filter()->implode(', ');
                        } else {
                        // Если отношение не загружено, используем старое поле authors
                        $authorsList = $article->authors ?? '';
                        }
                        @endphp
                        {{ Str::limit($authorsList ?: '-', 60) }}
                        @if($article->relationLoaded('authors') && $article->authors && $article->authors->where('is_correspondent', true)->count())
                        <br><span class="badge bg-info">✉ Корреспондент</span>
                        @endif
                    </td>

                    <td>
                        @php
                        $artTypeLabels = [
                        'RAR' => 'Научная статья',
                        'REV' => 'Обзор',
                        'SCO' => 'Краткое сообщение',
                        'BRV' => 'Рецензия',
                        'CNF' => 'Материалы конф.',
                        'EDI' => 'Редакторская заметка'
                        ];
                        @endphp
                        <span class="badge bg-secondary" title="{{ $article->art_type }}">
                            {{ $artTypeLabels[$article->art_type] ?? $article->art_type }}
                        </span>
                    </td>
                    <td>{{ $article->pages ?? '-' }}</td>
                    <td>
                        @if($article->doi)
                        <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener" class="text-decoration-none">
                            <code class="small">{{ Str::limit($article->doi, 20) }}</code>
                        </a>
                        @if($article->edn)
                        <br><small class="text-muted">EDN: {{ $article->edn }}</small>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $article->lang_publ ?? 'RUS' }}</span>
                    </td>
                    <td>
                        @if($article->is_published)
                        <span class="badge bg-success">Опубликована</span>
                        @if($article->date_publication)
                        <br><small>{{ \Carbon\Carbon::parse($article->date_publication)->format('d.m.Y') }}</small>
                        @endif
                        @else
                        <span class="badge bg-secondary">Черновик</span>
                        @endif
                    </td>
                    <td>
                        @if($article->is_rinc ?? false)
                        <span class="badge bg-primary">В РИНЦ</span>
                        @if($article->rinc_id)
                        <br><small>ID: {{ $article->rinc_id }}</small>
                        @endif
                        @else
                        <span class="badge bg-warning text-dark">Не в РИНЦ</span>
                        @endif
                    </td>
                    <td>
                        @if($article->pdf_url)
                        <a href="{{ $article->pdf_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary" title="Редактировать">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if($article->doi)
                            <button type="button" class="btn btn-sm btn-outline-info"
                                onclick="copyToClipboard('{{ $article->doi }}')" title="Копировать DOI">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            @endif

                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                onsubmit="return confirm('Удалить статью «{{ $article->title_ru ?? $article->title }}»? Это действие необратимо.');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Показано {{ $articles->firstItem() ?? 0 }} - {{ $articles->lastItem() ?? 0 }} из {{ $articles->total() }} статей
        </div>
        <div>
            {{ $articles->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Показываем временное уведомление
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 m-3 alert alert-success';
            toast.style.zIndex = '9999';
            toast.innerHTML = 'DOI скопирован: ' + text;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }

    // Подсветка строки при успешном действии
    @if(session('highlight'))
    document.querySelector('tr[data-id="{{ session('
        highlight ') }}"]')?.classList.add('table-success');
    setTimeout(() => {
        document.querySelector('tr[data-id="{{ session('
            highlight ') }}"]')?.classList.remove('table-success');
    }, 3000);
    @endif
</script>
@endpush

<style>
    .table td {
        vertical-align: middle;
    }

    .table code {
        font-size: 0.75rem;
    }

    .badge {
        font-size: 0.7rem;
    }
</style>
@endsection