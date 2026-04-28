@extends('layouts.base')
@section('page.title', 'Выпуск номер')
@section('content')

<section class="section" style="padding-top: 70px; padding-bottom: 40px; background: linear-gradient(135deg, var(--bg) 0%, var(--bg-light) 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- АДАПТИВНЫЙ ЗАГОЛОВОК -->
                <h1 style="font-size: clamp(2rem, 8vw, 4rem); line-height: 1.2; margin-bottom: 1.2rem;">
                    <span style="color: var(--accent);">Современная гуманитаристика</span>
                </h1>

                <!-- АДАПТИВНЫЙ АБЗАЦ -->
                <p style="font-size: clamp(1rem, 4vw, 1.3rem); color: var(--text-light); margin-bottom: 2rem; max-width: 640px; margin-left: 0;">
                    Периодическое рецензируемое научное издание, представляющее результаты исторических, филологических и искусствоведческих исследований на русском, английском и чувашском языках
                </p>

                <!-- АДАПТИВНЫЕ КНОПКИ -->
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <a href="#archive" class="btn btn-lg"
                        style="border-radius: 40px; padding: 0.7rem 1.5rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); font-size: clamp(0.8rem, 3vw, 1rem);">
                        <i class="bi bi-journal"></i> Текущий выпуск
                    </a>
                </div>
            </div>


            <div class="col-lg-4 mt-4 mt-lg-0">
                <div style="background: var(--bg); border: 2px solid var(--border); border-radius: 20px 20px 20px 0; padding: 1.5rem;">
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 0.2rem;">
                        Быстрые ссылки
                    </div>
                    <div class="d-grid gap-2">
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#about">
                            <i class="bi bi-info-circle"></i> Для авторов
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent; font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#editors">
                            <i class="bi-bookshelf"></i> Архив выпусков
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent; font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#contacts">
                            <i class="bi bi-people"></i> Контакты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


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

                            <!-- Обложка выпуска (уменьшенная) -->
                            @if($issue->cover_image_path)
                            <div class="text-center pt-3">
                                <img src="{{ route('issue.cover', $issue) }}" alt="Обложка выпуска" class="img-fluid" style="max-width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            </div>
                            @elseif($issue->cover_image)
                            <div class="text-center pt-3">
                                <img src="{{ $issue->cover_image }}" alt="Обложка выпуска" class="img-fluid" style="max-width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            </div>
                            @endif

                            <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list"></i> Содержание
                            </a>

                            <!-- PDF выпуска -->
                            @if($issue->pdf_file_path)
                            <a href="{{ route('download.issue.pdf', $issue) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> PDF
                            </a>
                            @elseif($issue->pdf_url)
                            <a href="{{ $issue->pdf_url }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> PDF
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Пока нет опубликованных выпусков.
        </div>
        @endif
    </div>
</section>






@endsection