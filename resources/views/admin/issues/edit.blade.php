@extends('layouts.base')

@section('page.title', 'Редактировать выпуск')

@section('content')
<div class="container mt-4" style="max-width: 1200px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h2 class="mb-0">Редактировать выпуск: {{ $issue->fullTitle }}</h2>
        <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mt-3">
        <div class="fw-bold mb-2">Проверьте поля формы</div>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.issues.update', $issue) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <!-- ===== СЕКЦИЯ 1: ОСНОВНАЯ ИНФОРМАЦИЯ ===== -->
                <h5 class="mb-3 text-primary">Основная информация</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Год <span class="text-danger">*</span></label>
                        <input type="number" name="year" class="form-control" value="{{ old('year', $issue->year) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Том</label>
                        <input type="text" name="volume" class="form-control" value="{{ old('volume', $issue->volume) }}" placeholder="15">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Номер</label>
                        <input type="text" name="number" class="form-control" value="{{ old('number', $issue->number) }}" placeholder="3">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Сквозной номер</label>
                        <input type="text" name="alt_number" class="form-control" value="{{ old('alt_number', $issue->alt_number) }}" placeholder="45">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Часть</label>
                        <input type="number" name="part" class="form-control" value="{{ old('part', $issue->part) }}" placeholder="1">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Месяц / период</label>
                        <input type="text" name="month" class="form-control" value="{{ old('month', $issue->month) }}" placeholder="Сентябрь">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Тип выпуска</label>
                        <select name="issue_type" class="form-select">
                            <option value="ISS" {{ old('issue_type', $issue->issue_type) == 'ISS' ? 'selected' : '' }}>ISS - Обычный выпуск</option>
                            <option value="OFI" {{ old('issue_type', $issue->issue_type) == 'OFI' ? 'selected' : '' }}>OFI - Online First</option>
                            <option value="SPI" {{ old('issue_type', $issue->issue_type) == 'SPI' ? 'selected' : '' }}>SPI - Специальный выпуск</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Дата публикации</label>
                        <input type="date" name="published_at" class="form-control" value="{{ old('published_at', optional($issue->published_at)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Страницы выпуска</label>
                        <input type="text" name="issue_pages" class="form-control" value="{{ old('issue_pages', $issue->issue_pages) }}" placeholder="1-100">
                        <small class="text-muted">Диапазон страниц всего выпуска</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Издатель</label>
                        <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $issue->publisher) }}" placeholder="Издательство">
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" {{ old('is_published', $issue->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Опубликован</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Порядок сортировки</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $issue->sort_order) }}">
                    </div>
                </div>

                <hr class="my-4">

                <!-- ===== СЕКЦИЯ 2: ИДЕНТИФИКАТОРЫ ===== -->
                <h5 class="mb-3 text-primary">Идентификаторы</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ISSN журнала</label>
                        <input type="text" name="issn" class="form-control" value="{{ old('issn', $issue->issn) }}" placeholder="1234-5678">
                        <small class="text-muted">Формат: XXXX-XXXX</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">eISSN журнала</label>
                        <input type="text" name="eissn" class="form-control" value="{{ old('eissn', $issue->eissn) }}" placeholder="1234-5678">
                        <small class="text-muted">Электронный ISSN</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">DOI выпуска</label>
                        <input type="text" name="issue_doi" class="form-control" value="{{ old('issue_doi', $issue->issue_doi) }}" placeholder="10.12345/issue.2024.001">
                        <small class="text-muted">DOI для всего выпуска</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">EDN</label>
                        <input type="text" name="edn" class="form-control" value="{{ old('edn', $issue->edn) }}" placeholder="ABCDEF" maxlength="6">
                        <small class="text-muted">6 латинских символов</small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- ===== СЕКЦИЯ 3: НАЗВАНИЯ И ОПИСАНИЯ ===== -->
                <h5 class="mb-3 text-primary">Названия и описания</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Название выпуска / тема (RU)</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $issue->title) }}" placeholder="Цифровые методы в науке">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Название выпуска / тема (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $issue->title_en) }}" placeholder="Digital methods in science">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Описание выпуска (RU)</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Краткое описание выпуска">{{ old('description', $issue->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Описание выпуска (EN)</label>
                        <textarea name="description_en" class="form-control" rows="4" placeholder="Issue description in English">{{ old('description_en', $issue->description_en) }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <!-- ===== СЕКЦИЯ 4: ФАЙЛЫ ===== -->
                <!-- <h5 class="mb-3 text-primary">Файлы выпуска</h5>
                <div class="row g-3">
                    @if($issue->pdf_file_path)
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-file-pdf"></i>
                            <strong>Текущий PDF файл:</strong> {{ $issue->pdf_original_name ?? 'PDF файл' }}
                            ({{ number_format($issue->pdf_file_size / 1024, 2) }} КБ)
                            <div class="form-check mt-2">
                                <input type="checkbox" name="delete_pdf" value="1" class="form-check-input" id="delete_pdf">
                                <label class="form-check-label text-danger" for="delete_pdf">Удалить текущий PDF файл</label>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Загрузить PDF файл выпуска</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        <small class="text-muted">Поддерживаются файлы в формате PDF (макс. 10 МБ)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Или ссылка на PDF выпуска</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url', $issue->pdf_url) }}" placeholder="https://example.com/issue.pdf">
                        <small class="text-muted">Если загружаете файл, ссылка будет проигнорирована</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ссылка на обложку выпуска</label>
                        <input type="url" name="cover_image" class="form-control" value="{{ old('cover_image') }}" placeholder="https://example.com/cover.jpg">
                        <small class="text-muted">Изображение обложки для отображения</small>
                    </div>

                </div> -->


                <h5 class="mb-3">Файл журнала (PDF)</h5>
                <div class="row g-3">
                    @if($issue->pdf_file_path)
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="bi bi-file-pdf"></i>
                            <strong>Текущий файл:</strong> {{ $issue->pdf_original_name ?? 'PDF файл' }}
                            ({{ number_format($issue->pdf_file_size / 1024, 2) }} КБ)
                            <div class="form-check mt-2">
                                <input type="checkbox" name="delete_pdf" value="1" class="form-check-input" id="delete_pdf">
                                <label class="form-check-label text-danger" for="delete_pdf">Удалить текущий PDF файл</label>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Загрузить новый PDF файл</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        <small class="text-muted">Поддерживаются файлы в формате PDF (макс. 10 МБ)</small>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Или ссылка на PDF</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url', $issue->pdf_url) }}" placeholder="https://example.com/article.pdf">
                        <small class="text-muted">Если загружаете файл, ссылка будет проигнорирована</small>
                    </div>


                </div>

                <!-- Обложка -->
                @if($issue->cover_image_path)
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-image"></i>
                        <strong>Текущая обложка:</strong> {{ $issue->cover_original_name ?? 'Файл обложки' }}
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $issue->cover_image_path) }}" alt="Обложка" style="max-width: 150px; max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="delete_cover" value="1" class="form-check-input" id="delete_cover">
                            <label class="form-check-label text-danger" for="delete_cover">Удалить текущую обложку</label>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Загрузить обложку выпуска</label>
                    <input type="file" name="cover_image_file" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="text-muted">Поддерживаются форматы: JPG, PNG, GIF, WEBP (макс. 5 МБ)</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Или ссылка на обложку выпуска</label>
                    <input type="url" name="cover_image" class="form-control" value="{{ old('cover_image', $issue->cover_image) }}" placeholder="https://example.com/cover.jpg">
                    <small class="text-muted">Если загружаете файл, ссылка будет проигнорирована</small>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Сохранить изменения
            </button>
            <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary">Отмена</a>
        </div>
</div>
</form>
</div>
@endsection