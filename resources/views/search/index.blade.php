@extends('layouts.base')

@section('page.title', 'Поиск по сайту')

@section('content')
    <section class="section">
        <div class="container">
            <h2 style="font-size: 2.4rem; margin-bottom: 1rem;">Поиск по сайту</h2>

            <form action="{{ route('search.index') }}" method="GET" class="mb-4">
                <div class="input-group input-group-lg">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        class="form-control"
                        placeholder="Введите запрос: название статьи, автора, выпуск, страницу..."
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Найти
                    </button>
                </div>
                <div class="mt-4 p-3 border rounded-4" style="background: var(--bg-light);">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Где искать</label>
                            <select name="type" class="form-select">
                                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Везде</option>
                                <option value="articles" {{ $type === 'articles' ? 'selected' : '' }}>Только статьи</option>
                                <option value="issues" {{ $type === 'issues' ? 'selected' : '' }}>Только выпуски</option>
                                <option value="pages" {{ $type === 'pages' ? 'selected' : '' }}>Только страницы</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Название</label>
                            <input type="text" name="title" value="{{ $title }}" class="form-control" placeholder="Название статьи/страницы">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Автор</label>
                            <input type="text" name="author" value="{{ $author }}" class="form-control" placeholder="ФИО автора">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Год</label>
                            <input type="text" name="year" value="{{ $year }}" class="form-control" placeholder="Например 2024">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Выпуск</label>
                            <select name="issue_id" class="form-select">
                                <option value="">Все выпуски</option>
                                @foreach($availableIssues as $issueOption)
                                    <option value="{{ $issueOption->id }}" {{ (string) $issueId === (string) $issueOption->id ? 'selected' : '' }}>
                                        {{ $issueOption->year }}
                                        @if($issueOption->volume) • Том {{ $issueOption->volume }}@endif
                                        @if($issueOption->number) • № {{ $issueOption->number }}@endif
                                        @if($issueOption->title) • {{ $issueOption->title }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Страница журнала</label>
                            <select name="page_slug" class="form-select">
                                <option value="">Все страницы</option>
                                @foreach($availablePages as $pageOption)
                                    <option value="{{ $pageOption->slug }}" {{ $pageSlug === $pageOption->slug ? 'selected' : '' }}>
                                        {{ $pageOption->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-funnel"></i> Применить расширенный поиск
                        </button>
                        <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">
                            Сбросить
                        </a>
                    </div>
                </div>
            </form>

            @if(!$hasFilters)
                <p style="color: var(--text-light);">Введите запрос или используйте расширенные фильтры.</p>
            @else
                <div class="mb-3" style="color: var(--text-light);">
                    @if($q !== '')
                        Результаты по запросу: <strong>{{ $q }}</strong>
                    @else
                        Результаты расширенного поиска
                    @endif
                </div>

                @php
                    $total = $articles->count() + $issues->count() + $pages->count();
                @endphp

                <div class="mb-4">
                    <span class="badge bg-primary">Статьи: {{ $articles->count() }}</span>
                    <span class="badge bg-success">Выпуски: {{ $issues->count() }}</span>
                    <span class="badge bg-secondary">Страницы: {{ $pages->count() }}</span>
                    <span class="badge bg-dark">Всего: {{ $total }}</span>
                </div>

                @if($total === 0)
                    <div class="alert alert-warning">По вашему запросу ничего не найдено.</div>
                @endif

                @if($articles->isNotEmpty())
                    <h3 class="h4 mb-3">Статьи</h3>
                    <div class="row g-3 mb-4">
                        @foreach($articles as $article)
                            <div class="col-12">
                                <div class="p-3 border rounded-4" style="background: var(--bg-light);">
                                    <div class="fw-bold mb-1">{{ $article->title }}</div>
                                    @if($article->authors)
                                        <div class="text-muted mb-2">{{ $article->authors }}</div>
                                    @endif
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($article->issue)
                                            <a href="{{ route('issues.show', $article->issue) }}" class="btn btn-sm btn-outline-primary">
                                                Перейти к выпуску
                                            </a>
                                        @endif
                                        @if($article->pdf_url)
                                            <a href="{{ $article->pdf_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                PDF
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($issues->isNotEmpty())
                    <h3 class="h4 mb-3">Выпуски</h3>
                    <div class="row g-3 mb-4">
                        @foreach($issues as $issue)
                            <div class="col-md-6">
                                <div class="p-3 border rounded-4" style="background: var(--bg-light);">
                                    <div class="fw-bold mb-1">
                                        @if($issue->volume && $issue->number)
                                            Том {{ $issue->volume }}, № {{ $issue->number }}
                                        @elseif($issue->number)
                                            № {{ $issue->number }}
                                        @else
                                            Выпуск
                                        @endif
                                    </div>
                                    <div class="text-muted mb-2">
                                        {{ $issue->month ? $issue->month . ' ' : '' }}{{ $issue->year }}
                                        @if($issue->title) • {{ $issue->title }} @endif
                                    </div>
                                    <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-primary">Открыть выпуск</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($pages->isNotEmpty())
                    <h3 class="h4 mb-3">Страницы</h3>
                    <div class="row g-3">
                        @foreach($pages as $page)
                            <div class="col-md-6">
                                <div class="p-3 border rounded-4" style="background: var(--bg-light);">
                                    <div class="fw-bold mb-2">{{ $page->title }}</div>
                                    <a href="{{ route('journal.page', $page->slug) }}" class="btn btn-sm btn-outline-primary">Открыть страницу</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection

