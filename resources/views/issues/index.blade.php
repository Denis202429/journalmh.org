@extends('layouts.base')

@section('page.title', 'Архив выпусков')

@section('content')
<section class="section" id="archive">
    <div class="container">
        <h1 style="font-size: 2.6rem; margin-bottom: 1.2rem;">Архив выпусков</h1>
        <p style="color: var(--text-light); margin-bottom: 2rem;">Все опубликованные выпуски журнала.</p>

        @if($issues->count())
        <div class="row g-4">
            @foreach($issues as $issue)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm" style="border: 1px solid var(--border); border-radius: 16px; overflow: hidden;">
                    <div class="card-body">
                        <!-- Заголовок выпуска -->
                        <div class="mb-3">
                            <span class="badge bg-primary mb-2">{{ $issue->issue_type_label }}</span>
                            <h3 class="card-title h5 mb-1">
                                @if($issue->volume && $issue->number)
                                Том {{ $issue->volume }}, № {{ $issue->number }}
                                @elseif($issue->volume)
                                Том {{ $issue->volume }}
                                @elseif($issue->number)
                                № {{ $issue->number }}
                                @else
                                Выпуск {{ $issue->year }}
                                @endif
                            </h3>
                            <div class="text-muted small">
                                <i class="bi bi-calendar"></i> {{ $issue->month ? $issue->month . ' ' : '' }}{{ $issue->year }}
                                @if($issue->alt_number)
                                <br><i class="bi bi-hash"></i> Сквозной номер: {{ $issue->alt_number }}
                                @endif
                            </div>
                        </div>

                        <!-- Название выпуска -->
                        @if($issue->title)
                        <p class="card-text text-muted fst-italic small">
                            «{{ Str::limit($issue->title, 80) }}»
                        </p>
                        @endif

                        <!-- Метаданные выпуска -->
                        <div class="mb-3 small text-muted">
                            @if($issue->issue_pages)
                            <div><i class="bi bi-file-text"></i> Страницы: {{ $issue->issue_pages }}</div>
                            @endif
                            @if($issue->articles_count)
                            <div><i class="bi bi-journal"></i> Статей: {{ $issue->articles_count }}</div>
                            @endif
                            @if($issue->issue_doi)
                            <div><i class="bi bi-link-45deg"></i> DOI: <code>{{ $issue->issue_doi }}</code></div>
                            @endif
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list"></i> Содержание
                            </a>

                            @if($issue->pdf_file_path)
                            <a href="{{ route('download.issue.pdf', $issue) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> Скачать PDF
                            </a>
                            @elseif($issue->pdf_url)
                            <a href="{{ $issue->pdf_url }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> Открыть PDF
                            </a>
                            @endif
                            
                        </div>


                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $issues->links() }}
        </div>

        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Пока нет опубликованных выпусков.
        </div>
        @endif
    </div>
</section>
@endsection