@extends('layouts.base')

@section('page.title', 'Выпуски')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h2 class="mb-0">Выпуски журнала</h2>
            <a href="{{ route('admin.issues.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить выпуск
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Фильтры -->
        <form method="GET" class="mt-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Год</label>
                    <select class="form-select" name="year">
                        <option value="">Все годы</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Тип выпуска</label>
                    <select class="form-select" name="issue_type">
                        <option value="">Все типы</option>
                        <option value="ISS" {{ request('issue_type') == 'ISS' ? 'selected' : '' }}>Обычный</option>
                        <option value="OFI" {{ request('issue_type') == 'OFI' ? 'selected' : '' }}>Online First</option>
                        <option value="SPI" {{ request('issue_type') == 'SPI' ? 'selected' : '' }}>Специальный</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select" name="is_published">
                        <option value="">Все</option>
                        <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Опубликован</option>
                        <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Не опубликован</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Поиск</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Название, том, номер...">
                </div>
                
                <div class="col-md-12 d-flex gap-2 mt-2">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.issues.index') }}">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>

        @if($issues->isEmpty())
            <div class="alert alert-info mt-3">Пока нет ни одного выпуска.</div>
        @else
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Выпуск</th>
                            <th>Название</th>
                            <th>Тип</th>
                            <!-- <th>Страницы</th> -->
                            <th>DOI/EDN</th>
                            <th>Статей</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issues as $issue)
                            <tr>
                                <td>{{ $issue->id }}</td>
                                <td>
                                    <strong>{{ $issue->fullTitle }}</strong>
                                    @if($issue->month)
                                        <br><small class="text-muted">{{ $issue->month }}</small>
                                    @endif
                                    @if($issue->alt_number)
                                        <br><small class="text-muted">Скв.№: {{ $issue->alt_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($issue->title)
                                        <strong>{{ Str::limit($issue->title, 50) }}</strong>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    @if($issue->title_en)
                                        <br><small class="text-muted">{{ Str::limit($issue->title_en, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary" title="{{ $issue->issue_type }}">
                                        {{ $issue->issue_type_label }}
                                    </span>
                                </td>
                               <td>
                                    @if($issue->issue_doi)
                                        <code class="small">{{ Str::limit($issue->issue_doi, 20) }}</code>
                                    @elseif($issue->edn)
                                        <code>EDN: {{ $issue->edn }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $issue->articles_count }}</span>
                                </td>
                                <td>
                                    @if($issue->is_published)
                                        <span class="badge bg-success">Опубликован</span>
                                        @if($issue->published_at)
                                            <br><small>{{ $issue->published_at->format('d.m.Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Черновик</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.issues.show', $issue) }}" class="btn btn-sm btn-outline-info" title="Просмотр">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.issues.edit', $issue) }}" class="btn btn-sm btn-outline-primary" title="Редактировать">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.issues.destroy', $issue) }}" method="POST"
                                            onsubmit="return confirm('Удалить выпуск «{{ $issue->fullTitle }}»? Статьи должны быть предварительно удалены.');">
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
                    Показано {{ $issues->firstItem() ?? 0 }} - {{ $issues->lastItem() ?? 0 }} из {{ $issues->total() }} выпусков
                </div>
                <div>
                    {{ $issues->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection