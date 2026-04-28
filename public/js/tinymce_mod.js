// advtable
// autocorrect

// mergetags

// pageembed

// permanentpen

// powerpaste

// tableofcontents

// template

// tinycomments

// tinydrive

// tinymcespellchecker

// typography



document.addEventListener("DOMContentLoaded", function () {
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
        setup: function (editor) {

            editor.ui.registry.addButton('Img_transform', {
                text: 'IMG_TR',
                onAction: function () {
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
                onAction: function () {
                    editor.insertContent('Ҫ');
                }
            });

            editor.ui.registry.addButton('chuvash_c_', {
                text: 'ҫ',
                onAction: function () {
                    editor.insertContent('ҫ');
                }
            });

            editor.ui.registry.addButton('chuvash_AA_', {
                text: 'Ӑ',
                onAction: function () {
                    editor.insertContent('Ӑ');
                }
            });

            editor.ui.registry.addButton('chuvash_a_', {
                text: 'ӑ',
                onAction: function () {
                    editor.insertContent('ӑ');
                }
            });

            editor.ui.registry.addButton('chuvash_EE_', {
                text: 'Ӗ',
                onAction: function () {
                    editor.insertContent('Ӗ');
                }
            });

            editor.ui.registry.addButton('chuvash_e_', {
                text: 'ӗ',
                onAction: function () {
                    editor.insertContent('ӗ');
                }
            });

            editor.ui.registry.addButton('chuvash_UU_', {
                text: 'Ӳ',
                onAction: function () {
                    editor.insertContent('Ӳ');
                }
            });
            editor.ui.registry.addButton('chuvash_u_', {
                text: 'ӳ',
                onAction: function () {
                    editor.insertContent('ӳ');
                }
            });
            editor.ui.registry.addButton('S1', {
                text: '«',
                onAction: function () {
                    editor.insertContent('«');
                }
            });
            editor.ui.registry.addButton('S2', {
                text: '»',
                onAction: function () {
                    editor.insertContent('»');
                }
            });
            editor.ui.registry.addButton('S3', {
                text: '–',
                onAction: function () {
                    editor.insertContent('–');
                }
            });
            editor.ui.registry.addButton('S4', {
                text: '-',
                onAction: function () {
                    editor.insertContent('-');
                }
            });
        },
        images_upload_handler: function (blobInfo, success, failure) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/upload-image',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.location) {
                        success(response.location); // ✅ Теперь TinyMCE вставит ПРАВИЛЬНЫЙ путь
                    } else {
                        failure('Ошибка: пустой путь изображения.');
                    }
                },
                error: function (xhr, status, error) {
                    failure('Ошибка загрузки изображения.');
                }
            });
        },
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
            console.log("Выбор файла начался...");

            if (meta.filetype === 'image') {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function () {
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
                        success: function (response) {
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
                        error: function (xhr, status, error) {
                            console.error("Ошибка AJAX:", status, error);
                            console.error("Ответ сервера:", xhr.responseText);
                        }
                    });
                };
                input.click();
            }
        },
        // content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });



    // внизу вариант с рабочей кнопкой Paste
    // tinymce.init({
    //     selector: '#file-picker',
    //     plugins: "advlist anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
    //     toolbar: 'undo redo | cut copy | mypastebutton | formatselect | fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code fullscreen | chuvash_CС_ chuvash_c_ chuvash_AA_ chuvash_a_ chuvash_EE_ chuvash_e_ chuvash_UU_ chuvash_u_ Img_transform | S1 S2 S3 S4',
        
    //     setup: function(editor) {
    //         // Кастомная кнопка Paste
    //         editor.ui.registry.addButton('mypastebutton', {
    //             text: 'Paste',
    //             tooltip: 'Вставить из буфера',
    //             onAction: function() {
    //                 // Фокус на редактор
    //                 editor.focus();
                    
    //                 // Пытаемся использовать Clipboard API
    //                 if (navigator.clipboard && navigator.clipboard.readText) {
    //                     navigator.clipboard.readText()
    //                         .then(function(text) {
    //                             editor.insertContent(text);
    //                         })
    //                         .catch(function(err) {
    //                             console.error('Ошибка доступа к буферу:', err);
    //                             // Показываем пользовательское сообщение
    //                             editor.notificationManager.open({
    //                                 text: 'Разрешите доступ к буферу обмена в настройках браузера или используйте Ctrl+V',
    //                                 type: 'warning',
    //                                 timeout: 3000
    //                             });
    //                         });
    //                 } else {
    //                     // Для браузеров без поддержки Clipboard API
    //                     editor.notificationManager.open({
    //                         text: 'Используйте Ctrl+V для вставки',
    //                         type: 'info',
    //                         timeout: 2000
    //                     });
    //                 }
    //             }
    //         });
    //     },
        
    //     // Ваши существующие настройки
    //     image_title: true,
    //     automatic_uploads: true,
    //     images_upload_url: "/upload-image",
    //     convert_urls: false,
    //     remove_script_host: false,
    //     content_style: "body { font-family: 'Times New Roman', sans-serif; }",
    //     font_formats: "Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif;",
    //     fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
    // });

});
