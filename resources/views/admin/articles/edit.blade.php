@extends('layouts.base')

@section('page.title', 'Редактировать статью')

@section('content')
<div class="container mt-4" style="max-width: 1200px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <h2 class="mb-0">Редактировать статью</h2>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
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

    <!-- <form action="{{ route('admin.articles.update', $article) }}" method="POST"> -->
    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <!-- Обязательные поля -->
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Выпуск <span class="text-danger">*</span></label>
                        <select name="issue_id" class="form-select" required>
                            <option value="">Выберите выпуск</option>
                            @foreach($issues as $issue)
                            <option value="{{ $issue->id }}" {{ old('issue_id', $article->issue_id) == $issue->id ? 'selected' : '' }}>
                                {{ $issue->year }} - Том {{ $issue->volume ?? '-' }}, № {{ $issue->number ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (RU) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $article->title_ru) }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $article->title_en) }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (CV)</label>
                        <input type="text" name="title_cv" class="form-control" value="{{ old('title_cv', $article->title_cv) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Страницы</label>
                        <input type="text" name="pages" class="form-control" value="{{ old('pages', $article->pages) }}" placeholder="12-25">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Тип статьи</label>
                        <select name="art_type" class="form-select">
                            <option value="RAR" {{ old('art_type', $article->art_type ?? 'RAR') == 'RAR' ? 'selected' : '' }}>научная статья</option>
                            <option value="EDI" {{ old('art_type', $article->art_type) == 'EDI' ? 'selected' : '' }}>редакторская заметка</option>
                            <option value="BRV" {{ old('art_type', $article->art_type) == 'BRV' ? 'selected' : '' }}>рецензия</option>
                            <option value="CNF" {{ old('art_type', $article->art_type) == 'CNF' ? 'selected' : '' }}>материалы конференции</option>
                            <option value="SCO" {{ old('art_type', $article->art_type) == 'SCO' ? 'selected' : '' }}>краткое сообщение</option>
                            <option value="REV" {{ old('art_type', $article->art_type) == 'REV' ? 'selected' : '' }}>обзорная статья</option>
                            <option value="ABS" {{ old('art_type', $article->art_type) == 'ABS' ? 'selected' : '' }}>аннотация</option>
                            <option value="REP" {{ old('art_type', $article->art_type) == 'REP' ? 'selected' : '' }}>научный отчет</option>
                            <option value="RPR" {{ old('art_type', $article->art_type) == 'RPR' ? 'selected' : '' }}>репринт</option>
                            <option value="COR" {{ old('art_type', $article->art_type) == 'COR' ? 'selected' : '' }}>переписка</option>
                            <option value="PER" {{ old('art_type', $article->art_type) == 'PER' ? 'selected' : '' }}>персоналии</option>
                            <option value="MIS" {{ old('art_type', $article->art_type) == 'MIS' ? 'selected' : '' }}>разное</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Язык статьи</label>
                        <select name="lang_publ" class="form-select">
                            <option value="RUS" {{ old('lang_publ', $article->lang_publ ?? 'RUS') == 'RUS' ? 'selected' : '' }}>Русский</option>
                            <option value="ENG" {{ old('lang_publ', $article->lang_publ) == 'ENG' ? 'selected' : '' }}>English</option>
                            <option value="CHV" {{ old('lang_publ', $article->lang_publ) == 'CHV' ? 'selected' : '' }}>Чăваш (Чувашский)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Опубликована</label>
                        </div>
                    </div>
                </div>

                <!-- Раздел (после чекбокса Опубликована) -->
                <!-- <div class="row g-3 mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Раздел (RU)</label>
                        <input type="text" name="section_ru" class="form-control" value="{{ old('section_ru', $article->section_ru) }}" placeholder="Например: Исторические науки">
                        <small class="text-muted">Название тематического раздела на русском языке</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Раздел (EN)</label>
                        <input type="text" name="section_en" class="form-control" value="{{ old('section_en', $article->section_en) }}" placeholder="For example: Historical Sciences">
                        <small class="text-muted">Название раздела на английском языке</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Раздел (CV)</label>
                        <input type="text" name="section_cv" class="form-control" value="{{ old('section_cv', $article->section_cv) }}" placeholder="Сăмахран: Истори ăслăхĕсем">
                        <small class="text-muted">Название раздела на чувашском языке</small>
                    </div>
                </div> -->

                <!-- Раздел (выпадающий список) -->
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Раздел</label>
                        <select name="section_select" class="form-select" id="section_select">
                            <option value="">-- Выберите раздел --</option>
                            <option value="historical" {{ old('section_select', $selectedSection) == 'historical' ? 'selected' : '' }}>Исторические науки</option>
                            <option value="philological" {{ old('section_select', $selectedSection) == 'philological' ? 'selected' : '' }}>Филологические науки</option>
                            <option value="art" {{ old('section_select', $selectedSection) == 'art' ? 'selected' : '' }}>Виды искусств</option>
                            <option value="reviews" {{ old('section_select', $selectedSection) == 'reviews' ? 'selected' : '' }}>Рецензии</option>
                            <option value="personalia" {{ old('section_select', $selectedSection) == 'personalia' ? 'selected' : '' }}>Персоналии</option>
                            <option value="scientific_life" {{ old('section_select', $selectedSection) == 'scientific_life' ? 'selected' : '' }}>Научная жизнь</option>
                        </select>
                        <small class="text-muted">Выберите тематический раздел</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Раздел (RU)</label>
                        <input type="text" name="section_ru" id="section_ru" class="form-control" value="{{ old('section_ru', $article->section_ru) }}">
                        <small class="text-muted">Название раздела на русском языке</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Раздел (EN)</label>
                        <input type="text" name="section_en" id="section_en" class="form-control" value="{{ old('section_en', $article->section_en) }}">
                        <small class="text-muted">Название раздела на английском языке</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Раздел (CV)</label>
                        <input type="text" name="section_cv" id="section_cv" class="form-control" value="{{ old('section_cv', $article->section_cv) }}">
                        <small class="text-muted">Название раздела на чувашском языке</small>
                    </div>
                </div>




                <hr class="my-4">

                <!-- Авторы статьи -->
                <h5 class="mb-3">Авторы статьи</h5>
                <div id="authors-container">
                    @php
                    // Прямой запрос к БД (гарантированно работает)
                    $authors = \App\Models\ArticleAuthor::where('article_id', $article->id)->orderBy('author_num')->get();

                    if ($authors->isEmpty()) {
                    $authors = collect([null]);
                    }
                    @endphp

                    @foreach($authors as $index => $author)
                    <div class="author-item card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <strong>Автор {{ $loop->iteration }}</strong>
                            <button type="button" class="btn btn-sm btn-danger remove-author" style="display: {{ $loop->first && $authors->count() == 1 ? 'none' : 'block' }};">Удалить</button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Фамилия (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][surname_ru]" class="form-control" value="{{ old("authors.{$index}.surname_ru", $author->surname_ru ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Фамилия (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][surname_en]" class="form-control" value="{{ old("authors.{$index}.surname_en", $author->surname_en ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Фамилия (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][surname_cv]" class="form-control" value="{{ old("authors.{$index}.surname_cv", $author->surname_cv ?? '') }}">
                                </div>

                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Имя (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][name_ru]" class="form-control" value="{{ old("authors.{$index}.name_ru", $author->name_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Имя (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][name_en]" class="form-control" value="{{ old("authors.{$index}.name_en", $author->name_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Имя (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][name_cv]" class="form-control" value="{{ old("authors.{$index}.name_cv", $author->name_cv ?? '') }}">
                                </div>
                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Отчество (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][patronymic_ru]" class="form-control" value="{{ old("authors.{$index}.patronymic_ru", $author->patronymic_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Отчество (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][patronymic_en]" class="form-control" value="{{ old("authors.{$index}.patronymic_en", $author->patronymic_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Отчество (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][patronymic_cv]" class="form-control" value="{{ old("authors.{$index}.patronymic_cv", $author->patronymic_cv ?? '') }}">
                                </div>

                                <hr class="mt-3 mb-3">

                                <div class="col-md-6 mt-2">
                                    <label>Организация (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][org_name_ru]" class="form-control" value="{{ old("authors.{$index}.org_name_ru", $author->org_name_ru ?? '') }}">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label>Организация (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][org_name_en]" class="form-control" value="{{ old("authors.{$index}.org_name_en", $author->org_name_en ?? '') }}">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label>Организация (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][org_name_cv]" class="form-control" value="{{ old("authors.{$index}.org_name_cv", $author->org_name_cv ?? '') }}">
                                </div>


                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Город (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][town_ru]" class="form-control" value="{{ old("authors.{$index}.town_ru", $author->town_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Город (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][town_en]" class="form-control" value="{{ old("authors.{$index}.town_en", $author->town_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Город (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][town_cv]" class="form-control" value="{{ old("authors.{$index}.town_cv", $author->town_cv ?? '') }}">
                                </div>
                                <hr class="mt-3 mb-3">


                                <div class="col-md-4 mt-2">
                                    <label>Страна (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][country_ru]" class="form-control" value="{{ old("authors.{$index}.country_ru", $author->country_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Страна (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][country_en]" class="form-control" value="{{ old("authors.{$index}.country_en", $author->country_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Страна (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][country_cv]" class="form-control" value="{{ old("authors.{$index}.country_cv", $author->country_cv ?? '') }}">
                                </div>


                                <div class="col-md-4 mt-2">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="authors[{{ $index }}][is_correspondent]" value="1" class="form-check-input" {{ old("authors.{$index}.is_correspondent", $author->is_correspondent ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">Автор-корреспондент</label>
                                    </div>
                                </div>

                                <!-- НОВЫЙ БЛОК - РОЛЬ АВТОРА -->
                                <div class="col-md-4 mt-2">
                                    <label>Роль автора</label>
                                    <select name="authors[{{ $index }}][role]" class="form-select">
                                        <option value="" {{ old("authors.{$index}.role", $author->role ?? '') == '' ? 'selected' : '' }}>Автор</option>
                                        <option value="0" {{ old("authors.{$index}.role", $author->role ?? '') == '0' ? 'selected' : '' }}>Редактор</option>
                                        <option value="1" {{ old("authors.{$index}.role", $author->role ?? '') == '1' ? 'selected' : '' }}>Ответственный редактор</option>
                                        <option value="2" {{ old("authors.{$index}.role", $author->role ?? '') == '2' ? 'selected' : '' }}>Научный редактор</option>
                                        <option value="3" {{ old("authors.{$index}.role", $author->role ?? '') == '3' ? 'selected' : '' }}>Переводчик</option>
                                        <option value="4" {{ old("authors.{$index}.role", $author->role ?? '') == '4' ? 'selected' : '' }}>Составитель</option>
                                        <option value="5" {{ old("authors.{$index}.role", $author->role ?? '') == '5' ? 'selected' : '' }}>Фотограф</option>
                                        <option value="6" {{ old("authors.{$index}.role", $author->role ?? '') == '6' ? 'selected' : '' }}>Художник</option>
                                        <option value="9" {{ old("authors.{$index}.role", $author->role ?? '') == '9' ? 'selected' : '' }}>Иллюстратор</option>
                                        <option value="10" {{ old("authors.{$index}.role", $author->role ?? '') == '10' ? 'selected' : '' }}>Автор комментария</option>
                                        <option value="20" {{ old("authors.{$index}.role", $author->role ?? '') == '20' ? 'selected' : '' }}>Автор вступительной статьи</option>
                                        <option value="23" {{ old("authors.{$index}.role", $author->role ?? '') == '23' ? 'selected' : '' }}>Рецензент</option>
                                        <option value="24" {{ old("authors.{$index}.role", $author->role ?? '') == '24' ? 'selected' : '' }}>Автор предисловия</option>
                                        <option value="25" {{ old("authors.{$index}.role", $author->role ?? '') == '25' ? 'selected' : '' }}>Автор послесловия</option>
                                        <option value="26" {{ old("authors.{$index}.role", $author->role ?? '') == '26' ? 'selected' : '' }}>Научный руководитель</option>
                                        <option value="48" {{ old("authors.{$index}.role", $author->role ?? '') == '48' ? 'selected' : '' }}>Редактор перевода</option>
                                    </select>
                                    <small class="text-muted">Если не выбрано - автор</small>
                                </div>

                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Должность (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][position_ru]" class="form-control" value="{{ old("authors.{$index}.position_ru", $author->position_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Должность (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][position_en]" class="form-control" value="{{ old("authors.{$index}.position_en", $author->position_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Должность (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][position_cv]" class="form-control" value="{{ old("authors.{$index}.position_cv", $author->position_cv ?? '') }}">
                                </div>


                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Ученая степень (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][degree_ru]" class="form-control" placeholder="кандидат наук, доктор наук" value="{{ old("authors.{$index}.degree_ru", $author->degree_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Ученая степень (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][degree_en]" class="form-control" placeholder="PhD, Doctor of Sciences" value="{{ old("authors.{$index}.degree_en", $author->degree_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Ученая степень (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][degree_cv]" class="form-control" placeholder="ăслăхсен кандидачĕ, ăслăхсен докторĕ" value="{{ old("authors.{$index}.degree_cv", $author->degree_cv ?? '') }}">
                                </div>


                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>Звание (RU)</label>
                                    <input type="text" name="authors[{{ $index }}][rank_ru]" class="form-control" placeholder="доцент, профессор" value="{{ old("authors.{$index}.rank_ru", $author->rank_ru ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Звание (EN)</label>
                                    <input type="text" name="authors[{{ $index }}][rank_en]" class="form-control" placeholder="Associate Professor, Professor" value="{{ old("authors.{$index}.rank_en", $author->rank_en ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Звание (CV)</label>
                                    <input type="text" name="authors[{{ $index }}][rank_cv]" class="form-control" placeholder="доцент, профессор" value="{{ old("authors.{$index}.rank_cv", $author->rank_cv ?? '') }}">
                                </div>



                                <hr class="mt-3 mb-3">

                                <div class="col-md-4 mt-2">
                                    <label>ORCID</label>
                                    <input type="text" name="authors[{{ $index }}][orcid]" class="form-control" placeholder="0000-0000-0000-0000" value="{{ old("authors.{$index}.orcid", $author->orcid ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>SPIN</label>
                                    <input type="text" name="authors[{{ $index }}][spin]" class="form-control" placeholder="1234-5678" value="{{ old("authors.{$index}.spin", $author->spin ?? '') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Email</label>
                                    <input type="email" name="authors[{{ $index }}][email]" class="form-control" value="{{ old("authors.{$index}.email", $author->email ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>


                <button type="button" class="btn btn-secondary mb-4" id="add-author-btn">
                    <i class="bi bi-plus-circle"></i> Добавить автора
                </button>



                <hr class="my-4">

                <!-- Аннотации -->
                <h5 class="mb-3">Аннотация</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Аннотация (RU)</label>
                        <textarea name="abstract_ru" class="form-control" rows="4">{{ old('abstract_ru', $article->abstract_ru) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Аннотация (EN)</label>
                        <textarea name="abstract_en" class="form-control" rows="4">{{ old('abstract_en', $article->abstract_en) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Аннотация (CV)</label>
                        <textarea name="abstract_cv" class="form-control" rows="4">{{ old('abstract_cv', $article->abstract_cv) }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Ключевые слова -->
                <h5 class="mb-3">Ключевые слова</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (RU)</label>
                        <input type="text" name="keywords_ru" class="form-control" value="{{ old('keywords_ru', $article->keywords_ru) }}" placeholder="слово1, слово2, слово3">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (EN)</label>
                        <input type="text" name="keywords_en" class="form-control" value="{{ old('keywords_en', $article->keywords_en) }}" placeholder="keyword1, keyword2, keyword3">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (CV)</label>
                        <input type="text" name="keywords_cv" class="form-control" value="{{ old('keywords_cv', $article->keywords_cv) }}" placeholder="сăмах1, сăмах2, сăмах3">
                    </div>
                </div>


                <hr class="my-4">

                <h5 class="mb-3">Финансирование</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Финансирование (RU)</label>
                        <textarea name="fundings_ru" class="form-control" rows="3" placeholder="Каждая строка - отдельный источник финансирования">{{ old('fundings_ru', is_array($article->fundings_ru) ? implode("\n", $article->fundings_ru) : $article->fundings_ru) }}</textarea>
                        <small class="text-muted">Каждая строка - отдельный грант или источник финансирования</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Финансирование (EN)</label>
                        <textarea name="fundings_en" class="form-control" rows="3" placeholder="Each line is a separate grant or funding source">{{ old('fundings_en', is_array($article->fundings_en) ? implode("\n", $article->fundings_en) : $article->fundings_en) }}</textarea>
                        <small class="text-muted">Each line is a separate grant or funding source</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Финансирование (CV)</label>
                        <textarea name="fundings_cv" class="form-control" rows="3" placeholder="Кашни йĕрке - уйрăм грант е финансăлав çăлкуçĕ">{{ old('fundings_cv', is_array($article->fundings_cv) ? implode("\n", $article->fundings_cv) : $article->fundings_cv) }}</textarea>
                        <small class="text-muted">Кашни йĕрке - уйрăм грант е финансăлав çăлкуçĕ</small>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Список литературы</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Литература (RU)</label>
                        <textarea name="references_ru" class="form-control" rows="8" placeholder="Каждая ссылка с новой строки">{{ old('references_ru', is_array($article->references_ru) ? implode("\n", $article->references_ru) : $article->references_ru) }}</textarea>
                        <small class="text-muted">Оформление по ГОСТ Р 7.0.5-2008, каждая ссылка с новой строки</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Литература (EN)</label>
                        <textarea name="references_en" class="form-control" rows="8" placeholder="Each reference on a new line">{{ old('references_en', is_array($article->references_en) ? implode("\n", $article->references_en) : $article->references_en) }}</textarea>
                        <small class="text-muted">Each reference on a new line</small>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Формат цитирования</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Как цитировать (RU)</label>
                        <textarea name="citation_ru" class="form-control" rows="2" placeholder="Рекомендуемый формат цитирования">{{ old('citation_ru', $article->citation_ru) }}</textarea>
                        <small class="text-muted">Рекомендуемый формат цитирования статьи на русском языке</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Как цитировать (EN)</label>
                        <textarea name="citation_en" class="form-control" rows="2" placeholder="Recommended citation format">{{ old('citation_en', $article->citation_en) }}</textarea>
                        <small class="text-muted">Recommended citation format in English</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Как цитировать (CV)</label>
                        <textarea name="citation_cv" class="form-control" rows="2" placeholder="Чăвашла цитировани форматĕ">{{ old('citation_cv', $article->citation_cv) }}</textarea>
                        <small class="text-muted">Статьяна чӑвашла цитатӑламалли сӗнекен формат</small>
                    </div>
                </div>


                <!-- Полный текст статьи -->
                <h5 class="mb-3">Полный текст статьи</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (RU)</label>
                        <textarea name="text_ru" class="form-control" rows="15" placeholder="Введите полный текст статьи на русском языке...">{{ old('text_ru', $article->text_ru) }}</textarea>
                        <small class="text-muted">Поддерживается HTML форматирование</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (EN)</label>
                        <textarea name="text_en" class="form-control" rows="15" placeholder="Enter the full text of the article in English...">{{ old('text_en', $article->text_en) }}</textarea>
                        <small class="text-muted">HTML formatting is supported</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (CV)</label>
                        <textarea name="text_cv" class="form-control" rows="15" placeholder="Чăвашла статья тулли текст...">{{ old('text_cv', $article->text_cv) }}</textarea>
                        <small class="text-muted">HTML форматлани пултарать</small>
                    </div>
                </div>


                <hr class="my-4">

                <!-- Идентификаторы -->
                <h5 class="mb-3">Идентификаторы</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">DOI</label>
                        <input type="text" name="doi" class="form-control" value="{{ old('doi', $article->doi) }}" placeholder="10.12345/example">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">EDN</label>
                        <input type="text" name="edn" class="form-control" value="{{ old('edn', $article->edn) }}" placeholder="ABCDEF" maxlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">УДК</label>
                        <input type="text" name="udk" class="form-control" value="{{ old('udk', $article->udk) }}" placeholder="004.89">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ББК</label>
                        <input type="text" name="bbk" class="form-control" value="{{ old('bbk', $article->bbk) }}" placeholder="32.81">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Даты -->
                <h5 class="mb-3">Даты</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Дата поступления</label>
                        <input type="date" name="date_received" class="form-control" value="{{ old('date_received', optional($article->date_received)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Дата принятия</label>
                        <input type="date" name="date_accepted" class="form-control" value="{{ old('date_accepted', optional($article->date_accepted)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Дата публикации</label>
                        <input type="date" name="date_publication" class="form-control" value="{{ old('date_publication', optional($article->date_publication)->format('Y-m-d')) }}">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Файл -->
                <!-- <h5 class="mb-3">Файл статьи</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Ссылка на PDF</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url', $article->pdf_url) }}" placeholder="https://example.com/article.pdf">
                        <small class="text-muted">Ссылка на полный текст статьи в формате PDF</small>
                    </div>
                </div> -->


                <!-- Файл статьи -->
                <!-- Файл статьи -->
                <h5 class="mb-3">Файл статьи (PDF)</h5>
                <div class="row g-3">
                    @php
                    $hasPhysicalFile = $article->pdf_file_path && \Storage::disk('public')->exists($article->pdf_file_path);
                    @endphp

                    @if($hasPhysicalFile)
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="bi bi-file-pdf"></i>
                            <strong>Текущий файл:</strong> {{ $article->pdf_original_name ?? 'PDF файл' }}
                            ({{ number_format($article->pdf_file_size / 1024, 2) }} КБ)
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
                    <!-- <div class="col-md-6">
                        <label class="form-label">Или ссылка на PDF</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url', $article->pdf_url) }}" placeholder="https://example.com/article.pdf">
                        <small class="text-muted">Если загружаете файл, ссылка будет проигнорирована</small>
                    </div> -->
                </div>


                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Порядок сортировки</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $article->sort_order) }}" placeholder="чем меньше - тем выше">
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Сохранить изменения
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('js')

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sectionSelect = document.getElementById('section_select');
        const sectionRu = document.getElementById('section_ru');
        const sectionEn = document.getElementById('section_en');
        const sectionCv = document.getElementById('section_cv');

        // Соответствие разделов
        const sections = {
            'historical': {
                ru: 'Исторические науки',
                en: 'Historical Sciences',
                cv: 'Истори ăслăхĕсем'
            },
            'philological': {
                ru: 'Филологические науки',
                en: 'Philological Sciences',
                cv: 'Филологи ăслăхĕсем'
            },
            'art': {
                ru: 'Виды искусств',
                en: 'Arts',
                cv: 'Искусство тĕсĕсем'
            },
            'reviews': {
                ru: 'Рецензии',
                en: 'Reviews',
                cv: 'Рецензисем'
            },
            'personalia': {
                ru: 'Персоналии',
                en: 'Personalia',
                cv: 'Персоналисем'
            },
            'scientific_life': {
                ru: 'Научная жизнь',
                en: 'Scientific Life',
                cv: 'Ăслăх пурнăçĕ'
            }
        };

        // Функция обновления полей при выборе из списка
        function updateSectionFields() {
            const selectedValue = sectionSelect.value;

            if (selectedValue && sections[selectedValue]) {
                sectionRu.value = sections[selectedValue].ru;
                sectionEn.value = sections[selectedValue].en;
                sectionCv.value = sections[selectedValue].cv;
            }
        }

        // Сохраняем исходные значения (если пользователь передумал)
        let originalRu = sectionRu.value;
        let originalEn = sectionEn.value;
        let originalCv = sectionCv.value;

        // При изменении выпадающего списка
        sectionSelect.addEventListener('change', function() {
            if (this.value) {
                updateSectionFields();
            } else {
                // Если выбрано "-- Выберите раздел --", возвращаем исходные значения
                sectionRu.value = originalRu;
                sectionEn.value = originalEn;
                sectionCv.value = originalCv;
            }
        });

        // Если пользователь вручную меняет текстовые поля, обновляем исходные значения
        sectionRu.addEventListener('input', function() {
            originalRu = this.value;
        });
        sectionEn.addEventListener('input', function() {
            originalEn = this.value;
        });
        sectionCv.addEventListener('input', function() {
            originalCv = this.value;
        });
    });
</script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE script not loaded');
            return;
        }

        tinymce.init({

            selector: 'textarea[name="text_ru"], textarea[name="text_en"], textarea[name="text_cv"], textarea[name="abstract_ru"], textarea[name="abstract_en"],  textarea[name="abstract_cv"]',
            height: 720,
            plugins: "advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
            toolbar: 'undo redo | cut copy paste | formatselect | fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code fullscreen | chuvash_CС_ chuvash_c_ chuvash_AA_ chuvash_a_ chuvash_EE_ chuvash_e_ chuvash_UU_ chuvash_u_ Img_transform | S1 S2 S3 S4',

            image_title: true,
            automatic_uploads: true,
            images_upload_url: "/upload-image",
            convert_urls: false,
            remove_script_host: false,
            content_style: "body { font-family: 'Times New Roman', sans-serif; }",
            font_formats: "Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif;",
            fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
            setup: function(editor) {

                editor.ui.registry.addButton('Img_transform', {
                    text: 'IMG_TR',
                    onAction: function() {
                        let editor = tinymce.activeEditor;
                        if (!editor) {
                            console.error("❌ TinyMCE не инициализирован!");
                            return;
                        }

                        let imgs = editor.dom.select('img');

                        if (imgs.length === 0) {
                            console.warn("⚠ Нет изображений для преобразования.");
                            return;
                        }

                        console.log(`✅ Найдено ${imgs.length} изображений.`);

                        imgs.forEach(img => {
                            console.log("🔍 Обрабатываем изображение: ", img.src);

                            // 🔹 Извлекаем имя файла из URL
                            let fileName = img.src.split('/').pop();

                            // 🔹 Формируем новый путь
                            let newSrc = `../storage/photos/pics/${fileName}`;
                            let figureHTML = ` <figure class="figure">
                             <a href="${newSrc}" target="_blank" rel="noopener">
                                   <img class="figure-img img-fluid rounded" style="max-width: 500px; height: auto; cursor: pointer;" src="${newSrc}" alt="${img.alt}">
                             </a>
                             <figcaption class="figure-caption">${img.title || ''}</figcaption>
                             </figure>`;
                            // editor.dom.replace(editor.dom.create('div', {}, figureHTML), img);
                            editor.dom.setOuterHTML(img, figureHTML);
                        });

                        console.log("✅ Все изображения преобразованы!");
                    }
                });
                editor.ui.registry.addButton('chuvash_CС_', {
                    text: 'Ҫ',
                    onAction: function() {
                        editor.insertContent('Ҫ');
                    }
                });

                editor.ui.registry.addButton('chuvash_c_', {
                    text: 'ҫ',
                    onAction: function() {
                        editor.insertContent('ҫ');
                    }
                });

                editor.ui.registry.addButton('chuvash_AA_', {
                    text: 'Ӑ',
                    onAction: function() {
                        editor.insertContent('Ӑ');
                    }
                });

                editor.ui.registry.addButton('chuvash_a_', {
                    text: 'ӑ',
                    onAction: function() {
                        editor.insertContent('ӑ');
                    }
                });

                editor.ui.registry.addButton('chuvash_EE_', {
                    text: 'Ӗ',
                    onAction: function() {
                        editor.insertContent('Ӗ');
                    }
                });

                editor.ui.registry.addButton('chuvash_e_', {
                    text: 'ӗ',
                    onAction: function() {
                        editor.insertContent('ӗ');
                    }
                });

                editor.ui.registry.addButton('chuvash_UU_', {
                    text: 'Ӳ',
                    onAction: function() {
                        editor.insertContent('Ӳ');
                    }
                });
                editor.ui.registry.addButton('chuvash_u_', {
                    text: 'ӳ',
                    onAction: function() {
                        editor.insertContent('ӳ');
                    }
                });
                editor.ui.registry.addButton('S1', {
                    text: '«',
                    onAction: function() {
                        editor.insertContent('«');
                    }
                });
                editor.ui.registry.addButton('S2', {
                    text: '»',
                    onAction: function() {
                        editor.insertContent('»');
                    }
                });
                editor.ui.registry.addButton('S3', {
                    text: '–',
                    onAction: function() {
                        editor.insertContent('–');
                    }
                });
                editor.ui.registry.addButton('S4', {
                    text: '-',
                    onAction: function() {
                        editor.insertContent('-');
                    }
                });
            },
            images_upload_handler: function(blobInfo, success, failure) {
                var formData = new FormData();
                formData.append('file', blobInfo.blob());
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: '/upload-image',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.location) {
                            success(response.location); // ✅ Теперь TinyMCE вставит ПРАВИЛЬНЫЙ путь
                        } else {
                            failure('Ошибка: пустой путь изображения.');
                        }
                    },
                    error: function(xhr, status, error) {
                        failure('Ошибка загрузки изображения.');
                    }
                });
            },
            file_picker_types: 'image',
            file_picker_callback: function(cb, value, meta) {
                console.log("Выбор файла начался...");

                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function() {
                        var file = this.files[0];
                        console.log("Файл выбран:", file.name);

                        var formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        console.log("Отправка AJAX-запроса...");

                        $.ajax({
                            url: '/upload-image',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.location) {
                                    let imageTitle = file.name;
                                    let altText = file.name;
                                    let imageUrl = response.location;
                                    console.error("success");
                                    // Вставляем изображение
                                    cb(imageUrl, {
                                        title: imageTitle,
                                        alt: altText
                                    });

                                } else {
                                    console.error("Ошибка: пустой путь изображения.");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Ошибка AJAX:", status, error);
                                console.error("Ответ сервера:", xhr.responseText);
                            }
                        });
                    };
                    input.click();
                }
            },

        });



    });
</script>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        let authorIndex = document.querySelectorAll('.author-item').length;

        console.log('Script loaded, authorIndex:', authorIndex);

        function removeAuthor(button) {
            const authorItem = button.closest('.author-item');
            if (authorItem && document.querySelectorAll('.author-item').length > 1) {
                authorItem.remove();
            } else {
                alert('Нельзя удалить единственного автора');
            }
        }

        const addButton = document.getElementById('add-author-btn');

        if (addButton) {
            addButton.addEventListener('click', function() {
                const container = document.getElementById('authors-container');
                if (!container) return;

                const newAuthor = document.createElement('div');
                newAuthor.className = 'author-item card mb-3';
                newAuthor.innerHTML = `
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong>Автор ${authorIndex + 1}</strong>
                        <button type="button" class="btn btn-sm btn-danger remove-author">Удалить</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Фамилия (RU)</label>
                                <input type="text" name="authors[${authorIndex}][surname_ru]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Фамилия (EN)</label>
                                <input type="text" name="authors[${authorIndex}][surname_en]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Фамилия (CV)</label>
                                <input type="text" name="authors[${authorIndex}][surname_cv]" class="form-control">
                            </div>

                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Имя (RU)</label>
                                <input type="text" name="authors[${authorIndex}][name_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Имя (EN)</label>
                                <input type="text" name="authors[${authorIndex}][name_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Имя (CV)</label>
                                <input type="text" name="authors[${authorIndex}][name_cv]" class="form-control">
                            </div>

                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Отчество (RU)</label>
                                <input type="text" name="authors[${authorIndex}][patronymic_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Отчество (EN)</label>
                                <input type="text" name="authors[${authorIndex}][patronymic_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Отчество (CV)</label>
                                <input type="text" name="authors[${authorIndex}][patronymic_cv]" class="form-control">
                            </div>
                            <hr class="my-4">

                            <div class="col-md-6 mt-2">
                                <label>Организация (RU)</label>
                                <input type="text" name="authors[${authorIndex}][org_name_ru]" class="form-control">
                            </div>
                            <div class="col-md-6 mt-2">
                                <label>Организация (EN)</label>
                                <input type="text" name="authors[${authorIndex}][org_name_en]" class="form-control">
                            </div>
                                <div class="col-md-4 mt-2">
                                <label>Организация (CV)</label>
                                <input type="text" name="authors[${authorIndex}][org_name_cv]" class="form-control">
                            </div>
                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Город (RU)</label>
                                <input type="text" name="authors[${authorIndex}][town_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Город (EN)</label>
                                <input type="text" name="authors[${authorIndex}][town_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Город (CV)</label>
                                <input type="text" name="authors[${authorIndex}][town_cv]" class="form-control">
                            </div>
                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Страна (RU)</label>
                                <input type="text" name="authors[${authorIndex}][country_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Страна (EN)</label>
                                <input type="text" name="authors[${authorIndex}][country_en]" class="form-control">
                           </div>
                           <div class="col-md-4 mt-2">
                               <label>Страна (CV)</label>
                               <input type="text" name="authors[${authorIndex}][country_cv]" class="form-control">
                           </div>
                           <hr class="my-4">                           
                           <div class="col-md-4 mt-2">
                               <div class="form-check mt-4">
                               <input type="checkbox" name="authors[${authorIndex}][is_correspondent]" value="1" class="form-check-input">
                               <label class="form-check-label">Автор-корреспондент</label>
                               </div>
                           </div>
                           <div class="col-md-4 mt-2">
                               <label>Роль автора</label>
                               <select name="authors[${authorIndex}][role]" class="form-select">
                               <option value="">Автор</option>
                               <option value="0">Редактор</option>
                               <option value="1">Ответственный редактор</option>
                               <option value="2">Научный редактор</option>
                               <option value="3">Переводчик</option>
                               <option value="4">Составитель</option>
                               <option value="5">Фотограф</option>
                               <option value="6">Художник</option>
                               <option value="9">Иллюстратор</option>
                               <option value="10">Автор комментария</option>
                               <option value="20">Автор вступительной статьи</option>
                               <option value="23">Рецензент</option>
                               <option value="24">Автор предисловия</option>
                               <option value="25">Автор послесловия</option>
                               <option value="26">Научный руководитель</option>
                               <option value="48">Редактор перевода</option>
                               </select>
                            <small class="text-muted">Если не выбрано - автор</small>
                            </div>
                            <hr class="my-4">                            
                            <div class="col-md-4 mt-2">
                                <label>Должность (RU)</label>
                                <input type="text" name="authors[${authorIndex}][position_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Должность (EN)</label>
                                <input type="text" name="authors[${authorIndex}][position_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Должность (CV)</label>
                                <input type="text" name="authors[${authorIndex}][position_cv]" class="form-control">
                            </div>

                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Ученая степень (RU)</label>
                                <input type="text" name="authors[${authorIndex}][degree_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Ученая степень (EN)</label>
                                <input type="text" name="authors[${authorIndex}][degree_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Ученая степень (CV)</label>
                                <input type="text" name="authors[${authorIndex}][degree_cv]" class="form-control">
                            </div>
                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>Звание (RU)</label>
                                <input type="text" name="authors[${authorIndex}][rank_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Звание (EN)</label>
                                <input type="text" name="authors[${authorIndex}][rank_en]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Звание (CV)</label>
                                <input type="text" name="authors[${authorIndex}][rank_cv]" class="form-control">
                            </div>
                            
                            <hr class="my-4">
                            <div class="col-md-4 mt-2">
                                <label>ORCID</label>
                                <input type="text" name="authors[${authorIndex}][orcid]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>SPIN</label>
                                <input type="text" name="authors[${authorIndex}][spin]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Email</label>
                                <input type="email" name="authors[${authorIndex}][email]" class="form-control">
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(newAuthor);
                authorIndex++;

                const removeBtn = newAuthor.querySelector('.remove-author');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        removeAuthor(this);
                    });
                }
            });
        }

        document.querySelectorAll('.remove-author').forEach(btn => {
            btn.addEventListener('click', function() {
                removeAuthor(this);
            });
        });
    });
</script>



@endpush