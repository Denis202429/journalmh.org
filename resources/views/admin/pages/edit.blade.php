@extends('layouts.base')

@section('page.title', 'Редактирование страницы')

@section('content')
<div class="container mt-4" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="mb-0">Редактирование страницы</h2>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> К списку страниц
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" class="form-control" value="{{ $page->slug }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Заголовок страницы</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Содержимое</label>
            <textarea id="page-content-editor" name="content" rows="14" class="form-control">{{ old('content', $page->content) }}</textarea>
            <!-- <small class="text-muted">Доступен визуальный редактор: шрифты, размеры, заголовки, ссылки, таблицы и т.д.</small> -->
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1"
                {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Опубликовать страницу</label>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Сохранить
            </button>
            <a href="{{ route('journal.page', $page->slug) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-up-right"></i> Открыть страницу
            </a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>


<!-- <script src="{{ asset('js/tinymce_mod.js') }}">     </script> -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE script not loaded');
            return;
        }

        tinymce.init({

            selector: '#page-content-editor',
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


@endpush