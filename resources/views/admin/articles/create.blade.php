@extends('layouts.base')

@section('page.title', 'Добавить статью')

@section('content')
<div class="container mt-4" style="max-width: 1200px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <h2 class="mb-0">Добавить статью</h2>
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

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">
                <!-- Обязательные поля -->
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Выпуск <span class="text-danger">*</span></label>
                        <select name="issue_id" class="form-select" required>
                            <option value="">Выберите выпуск</option>
                            @foreach($issues as $issue)
                            <option value="{{ $issue->id }}" {{ old('issue_id') == $issue->id ? 'selected' : '' }}>
                                {{ $issue->year }} - Том {{ $issue->volume ?? '-' }}, № {{ $issue->number ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (RU) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru') }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Название статьи (CV)</label>
                        <input type="text" name="title_cv" class="form-control" value="{{ old('title_cv') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Страницы</label>
                        <input type="text" name="pages" class="form-control" value="{{ old('pages') }}" placeholder="12-25">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Тип статьи</label>
                        <select name="art_type" class="form-select">

                            <option value="RAR" {{ old('art_type', 'RAR') == 'RAR' ? 'selected' : '' }}>научная статья</option>
                            <option value="EDI" {{ old('art_type') == 'EDI' ? 'selected' : '' }}>редакторская заметка</option>
                            <option value="BRV" {{ old('art_type') == 'BRV' ? 'selected' : '' }}>рецензия</option>
                            <option value="CNF" {{ old('art_type') == 'CNF' ? 'selected' : '' }}>материалы конференции</option>
                            <option value="SCO" {{ old('art_type') == 'SCO' ? 'selected' : '' }}>краткое сообщение</option>
                            <option value="REV" {{ old('art_type') == 'REV' ? 'selected' : '' }}>обзорная статья</option>
                            <option value="ABS" {{ old('art_type') == 'ABS' ? 'selected' : '' }}>аннотация</option>
                            <option value="REP" {{ old('art_type') == 'REP' ? 'selected' : '' }}>научный отчет</option>
                            <option value="RPR" {{ old('art_type') == 'RPR' ? 'selected' : '' }}>репринт</option>
                            <option value="COR" {{ old('art_type') == 'COR' ? 'selected' : '' }}>переписка</option>
                            <option value="PER" {{ old('art_type') == 'PER' ? 'selected' : '' }}>персоналии</option>
                            <option value="MIS" {{ old('art_type') == 'MIS' ? 'selected' : '' }}>разное</option>

                            <!-- RAR  EDI BRV CNF SCO REV  ABS  REP RPR COR PER MIS -->
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Язык статьи</label>
                        <select name="lang_publ" class="form-select">
                            <option value="RUS" {{ old('lang_publ', 'RUS') == 'RUS' ? 'selected' : '' }}>Русский</option>
                            <option value="ENG" {{ old('lang_publ') == 'ENG' ? 'selected' : '' }}>English</option>
                            <option value="CHV" {{ old('lang_publ') == 'CHV' ? 'selected' : '' }}>Чăваш (Чувашский)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Опубликована</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Авторы статьи -->
                <h5 class="mb-3">Авторы статьи</h5>
                <div id="authors-container">
                    <div class="author-item card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <strong>Автор 1</strong>
                            <button type="button" class="btn btn-sm btn-danger remove-author" style="display: none;">Удалить</button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Фамилия (RU)</label>
                                    <input type="text" name="authors[0][surname_ru]" class="form-control" value="{{ old('authors.0.surname_ru') }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Фамилия (EN)</label>
                                    <input type="text" name="authors[0][surname_en]" class="form-control" value="{{ old('authors.0.surname_en') }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Фамилия (CV)</label>
                                    <input type="text" name="authors[0][surname_cv]" class="form-control" value="{{ old('authors.0.surname_cv') }}">
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label>Имя (RU)</label>
                                    <input type="text" name="authors[0][name_ru]" class="form-control" value="{{ old('authors.0.name_ru') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Имя (EN)</label>
                                    <input type="text" name="authors[0][name_en]" class="form-control" value="{{ old('authors.0.name_en') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Имя (CV)</label>
                                    <input type="text" name="authors[0][name_cv]" class="form-control" value="{{ old('authors.0.name_cv') }}">
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label>Отчество (RU)</label>
                                    <input type="text" name="authors[0][patronymic_ru]" class="form-control" value="{{ old('authors.0.patronymic_ru') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Отчество (EN)</label>
                                    <input type="text" name="authors[0][patronymic_en]" class="form-control" value="{{ old('authors.0.patronymic_en') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Отчество (CV)</label>
                                    <input type="text" name="authors[0][patronymic_cv]" class="form-control" value="{{ old('authors.0.patronymic_cv') }}">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Организация (RU)</label>
                                    <input type="text" name="authors[0][org_name_ru]" class="form-control" value="{{ old('authors.0.org_name_ru') }}">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label>Организация (EN)</label>
                                    <input type="text" name="authors[0][org_name_en]" class="form-control" value="{{ old('authors.0.org_name_en') }}">
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label>Город (RU)</label>
                                    <input type="text" name="authors[0][town_ru]" class="form-control" value="{{ old('authors.0.town_ru') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Страна (RU)</label>
                                    <input type="text" name="authors[0][country_ru]" class="form-control" value="{{ old('authors.0.country_ru') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="authors[0][is_correspondent]" value="1" class="form-check-input" {{ old('authors.0.is_correspondent') ? 'checked' : '' }}>
                                        <label class="form-check-label">Автор-корреспондент</label>
                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label>Должность (RU)</label>
                                    <input type="text" name="authors[0][position_ru]" class="form-control" value="{{ old('authors.0.position_ru') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Ученая степень</label>
                                    <input type="text" name="authors[0][degree]" class="form-control" placeholder="кандидат наук, доктор наук" value="{{ old('authors.0.degree') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Звание</label>
                                    <input type="text" name="authors[0][rank]" class="form-control" placeholder="доцент, профессор" value="{{ old('authors.0.rank') }}">
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label>ORCID</label>
                                    <input type="text" name="authors[0][orcid]" class="form-control" placeholder="0000-0000-0000-0000" value="{{ old('authors.0.orcid') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>SPIN</label>
                                    <input type="text" name="authors[0][spin]" class="form-control" placeholder="1234-5678" value="{{ old('authors.0.spin') }}">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label>Email</label>
                                    <input type="email" name="authors[0][email]" class="form-control" value="{{ old('authors.0.email') }}">
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <textarea name="abstract_ru" class="form-control" rows="4">{{ old('abstract_ru') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Аннотация (EN)</label>
                        <textarea name="abstract_en" class="form-control" rows="4">{{ old('abstract_en') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Аннотация (CV)</label>
                        <textarea name="abstract_cv" class="form-control" rows="4">{{ old('abstract_cv') }}</textarea>
                    </div>
                </div>
                                    <!-- abstract_ru abstract_en abstract_cv -->
                <hr class="my-4">

                <!-- Ключевые слова -->
                <h5 class="mb-3">Ключевые слова</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (RU)</label>
                        <input type="text" name="keywords_ru" class="form-control" value="{{ old('keywords_ru') }}" placeholder="слово1, слово2, слово3">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (EN)</label>
                        <input type="text" name="keywords_en" class="form-control" value="{{ old('keywords_en') }}" placeholder="keyword1, keyword2, keyword3">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Ключевые слова (CV)</label>
                        <input type="text" name="keywords_cv" class="form-control" value="{{ old('keywords_cv') }}" placeholder="сăмах1, сăмах2, сăмах3">
                    </div>
                </div>

                <hr class="my-4">
                <!-- Полный текст статьи -->
                <h5 class="mb-3">Полный текст статьи</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (RU)</label>
                        <textarea name="text_ru" class="form-control" rows="15" placeholder="Введите полный текст статьи на русском языке...">{{ old('text_ru') }}</textarea>
                        <small class="text-muted">Поддерживается HTML форматирование</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (EN)</label>
                        <textarea name="text_en" class="form-control" rows="15" placeholder="Enter the full text of the article in English...">{{ old('text_en') }}</textarea>
                        <small class="text-muted">HTML formatting is supported</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Текст статьи (CV)</label>
                        <textarea name="text_cv" class="form-control" rows="15" placeholder="Чăвашла статья тулли текст...">{{ old('text_cv') }}</textarea>
                        <small class="text-muted">HTML форматлани пултарать</small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Идентификаторы -->
                <h5 class="mb-3">Идентификаторы</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">DOI</label>
                        <input type="text" name="doi" class="form-control" value="{{ old('doi') }}" placeholder="10.12345/example">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">EDN</label>
                        <input type="text" name="edn" class="form-control" value="{{ old('edn') }}" placeholder="ABCDEF" maxlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">УДК</label>
                        <input type="text" name="udk" class="form-control" value="{{ old('udk') }}" placeholder="004.89">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ББК</label>
                        <input type="text" name="bbk" class="form-control" value="{{ old('bbk') }}" placeholder="32.81">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Даты -->
                <h5 class="mb-3">Даты</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Дата поступления</label>
                        <input type="date" name="date_received" class="form-control" value="{{ old('date_received') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Дата принятия</label>
                        <input type="date" name="date_accepted" class="form-control" value="{{ old('date_accepted') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Дата публикации</label>
                        <input type="date" name="date_publication" class="form-control" value="{{ old('date_publication') }}">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Файл -->
                <!-- <h5 class="mb-3">Файл статьи</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Ссылка на PDF</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url') }}" placeholder="https://example.com/article.pdf">
                        <small class="text-muted">Ссылка на полный текст статьи в формате PDF</small>
                    </div>
                </div> -->


                <!-- Файл статьи -->
                <h5 class="mb-3">Файл статьи (PDF)</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Загрузить PDF файл</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        <small class="text-muted">Поддерживаются файлы в формате PDF (макс. 10 МБ)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Или ссылка на PDF</label>
                        <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url') }}" placeholder="https://example.com/article.pdf">
                        <small class="text-muted">Если загружаете файл, ссылка будет проигнорирована</small>
                    </div>
                </div>



                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Порядок сортировки</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order') }}" placeholder="чем меньше - тем выше">
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Сохранить
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Отмена</a>
            </div>
        </div>
    </form>
</div>


@push('js')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>




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
    // Ждем полной загрузки страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Считаем количество существующих авторов
        let authorIndex = document.querySelectorAll('.author-item').length;

        console.log('Script loaded, authorIndex:', authorIndex);

        // Функция для удаления автора
        function removeAuthor(button) {
            const authorItem = button.closest('.author-item');
            if (authorItem && document.querySelectorAll('.author-item').length > 1) {
                authorItem.remove();
            } else {
                alert('Нельзя удалить единственного автора');
            }
        }

        // Находим кнопку
        const addButton = document.getElementById('add-author-btn');

        if (addButton) {
            console.log('Button found, attaching event listener');

            // Очищаем предыдущие обработчики
            addButton.removeEventListener('click', addButton.clickHandler);

            // Создаем обработчик
            addButton.clickHandler = function() {
                console.log('Button clicked, adding author', authorIndex);

                const container = document.getElementById('authors-container');
                if (!container) {
                    console.error('Container not found');
                    return;
                }

                // Создаем нового автора
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

                            <div class="col-md-6 mt-2">
                                <label>Организация (RU)</label>
                                <input type="text" name="authors[${authorIndex}][org_name_ru]" class="form-control">
                            </div>
                            <div class="col-md-6 mt-2">
                                <label>Организация (EN)</label>
                                <input type="text" name="authors[${authorIndex}][org_name_en]" class="form-control">
                            </div>

                            <div class="col-md-4 mt-2">
                                <label>Город (RU)</label>
                                <input type="text" name="authors[${authorIndex}][town_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Страна (RU)</label>
                                <input type="text" name="authors[${authorIndex}][country_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="authors[${authorIndex}][is_correspondent]" value="1" class="form-check-input">
                                    <label class="form-check-label">Автор-корреспондент</label>
                                </div>
                            </div>

                            <div class="col-md-4 mt-2">
                                <label>Должность (RU)</label>
                                <input type="text" name="authors[${authorIndex}][position_ru]" class="form-control">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Ученая степень</label>
                                <input type="text" name="authors[${authorIndex}][degree]" class="form-control" placeholder="кандидат наук, доктор наук">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Звание</label>
                                <input type="text" name="authors[${authorIndex}][rank]" class="form-control" placeholder="доцент, профессор">
                            </div>

                            <div class="col-md-4 mt-2">
                                <label>ORCID</label>
                                <input type="text" name="authors[${authorIndex}][orcid]" class="form-control" placeholder="0000-0000-0000-0000">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>SPIN</label>
                                <input type="text" name="authors[${authorIndex}][spin]" class="form-control" placeholder="1234-5678">
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

                // Добавляем обработчик для новой кнопки удаления
                const removeBtn = newAuthor.querySelector('.remove-author');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        removeAuthor(this);
                    });
                }

                console.log('Author added, new index:', authorIndex);
            };

            addButton.addEventListener('click', addButton.clickHandler);
        } else {
            console.error('Button #add-author-btn not found!');
        }

        // Обработчики для существующих кнопок удаления
        document.querySelectorAll('.remove-author').forEach(btn => {
            btn.addEventListener('click', function() {
                removeAuthor(this);
            });
        });
    });
</script>
@endpush

@endsection
