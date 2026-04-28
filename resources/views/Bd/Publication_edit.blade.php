@extends('layouts.base')

@section('page.title', 'Редактирование базы данных')

@section('content')


<div class="container">
    <!-- {{-- <x-form action="{{ route('register.store') }}" method="POST"> --}} -->
    <form action="{{ route('admin.Publication_update_record', $record->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <!-- {{-- <div class="row mt-3"> --}} -->
        <div class="container">

            <h4 class="m-0 mt-5">
                {{ __('Редактирование записи базы данных') }}
            </h4>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Название статьи</span>
                    <input type="text" name='title_article' class="form-control"
                        @if (isset($record)) value="{{ $record->title_article }}" @endif>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Автор</span>
                    <input type="text" name='Autor' class="form-control"
                        @if (isset($record)) value="{{ $record->Autor }}" @endif>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Год публикации</span>
                    <input type="text" name='year_publication' class="form-control"
                        @if (isset($record)) value="{{ $record->year_publication }}" @endif>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Содержание</span>
                    <textarea id="mytextarea" name="content" rows="10" class="form-control">
                      @if (isset($record))
                         {{ $record->content }}
                      @endif
                    </textarea>
                </div>
            </div>


 <!-- - вот у меня представление в которое я передаю запись из базы данных  {{ $record->content }}.
 Затем я редактирую эту запись и снова сохраняю в базе данных. Проблема такая. Если в $record->content большой 
 текст и я допустим не успел отредактировать весь текст и остановился где то в середине и при следующем запуске редактора этой статьи мне 
 надо переместится в ту позицию где я остновился. То есть в тексте моожет как то поставить метку какую то. Как вообще это 
 можно сделать в laravel и bootsrap 5? Как лучше всего?  -->


            <div class="mb-3">
                <button type="button" class="btn btn-success" onclick="addSymbol('Ҫ')">Ç</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('ҫ')">ҫ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('Ӑ')">Ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('ӑ')">ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('Ӗ')">Ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('ӗ')">ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('Ӳ')">Ӳ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol('ӳ')">ӳ</button>
            </div> 
            <!-- // ӐӑӖӗӲӳ -->

            


            <div class="input-group mt-3">
                <select name="genre" class="form-select" id="inputGroupSelect02">
                    @php
                    $genres = [
                    "Не определен", "Повесть", "Рассказ", "Роман", "Очерк", "Новелла", "Фельетон", "Юморезка",
                    "Поэма", "Стихотворение", "Пьеса", "Драма", "Комедия", "Трагедия", "Басня", "Сказка", "Песня",
                    "Частушка", "Предисловие", "Мемуарная и эпистолярная литература", "Баллада", "Сонет", "Быль",
                    "Эссе", "Рецензия,отзыв", "Романс", "Монолог", "Автобиография", "Сценка", "Инсценировка",
                    "Рапсодия", "Памфлет", "Трагикомедия", "Эскиз", "Зарисовка"
                    ];
                    @endphp

                    @foreach ($genres as $genre)
                    <option value="{{ $genre }}" {{ isset($record) && $record->genre == $genre ? 'selected' : '' }}>
                        {{ $genre }}
                    </option>
                    @endforeach
                </select>
                <label class="input-group-text" for="inputGroupSelect02">Жанр</label>
            </div>

            <div class="input-group mt-3">
                <select name="category" class="form-select" id="inputGroupSelect02">
                    @php
                    $categories = [
                    "Научно-популярные тексты", "Прозаические тексты", "Поэтические тексты", "Драматургия",
                    "Публицистические тексты", "Устное народное творечество", "Официально-деловые тексты",
                    "Интернет-ресурсы", "Христианство", "Мусульманство", "Буддизм", "Прочие религиозные течения",
                    "Словари", "Не определена"
                    ];
                    @endphp

                    @foreach ($categories as $category)
                    <option value="{{ $category }}" {{ isset($record) && $record->category == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                    @endforeach
                </select>
                <label class="input-group-text" for="inputGroupSelect02">Категория</label>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Ссылка</span>
                    <input type="text" name='url' class="form-control"
                        @if (isset($record)) value="{{ $record->url }}" @endif>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Ссылка 2</span>
                    <input type="text" name='help_url' class="form-control"
                        @if (isset($record)) value="{{ $record->help_url }}" @endif>
                </div>
            </div>

            <!-- <div class="row mt-3">
                <div class="col">
                    <span class="m-0 mt-5">Корректор</span>
                    <input type="text" name='corrector' class="form-control"
                        @if (isset($record)) value="{{ $record->corrector }}" @endif>
                </div>
            </div> -->

            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                    value="1" {{ isset($record) && $record->status == 1 ? 'checked' : '' }}>
                <label class="form-check-label" for="statusSwitch">
                    {{ isset($record) && $record->status == 1 ? 'Проверен' : 'Не проверен' }}
                </label>
            </div>
            <!-- <input type="hidden" name="cursor_position" id="cursor_position" value="{{ $record->cursor_position ?? 0 }}"> -->
            <input type="hidden" id="cursor_position" name="cursor_position" value="{{ old('cursor_position', $record->cursor_position ?? 0) }}">

            <div class="mt-3 mb-3">
                <button type="submit" class="btn btn-primary">{{ __('Сохранить изменения') }}</button>
            </div>
        </div>

    </form>




</div>

@endsection

<script>
    function addSymbol(symbol) {
        var inputText = document.getElementById('mytextarea');

        // Запоминаем позицию курсора
        var start = inputText.selectionStart;
        var end = inputText.selectionEnd;
        var text = inputText.value;

        // Вставляем символ в нужное место
        inputText.value = text.substring(0, start) + symbol + text.substring(end);

        // Устанавливаем курсор после вставленного символа
        inputText.selectionStart = inputText.selectionEnd = start + symbol.length;
        inputText.focus();
    }
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var textarea = document.getElementById("mytextarea");
        var cursorInput = document.getElementById("cursor_position");

        // Восстанавливаем позицию курсора при загрузке
        var savedCursor = parseInt(cursorInput.value, 10);
        if (!isNaN(savedCursor) && savedCursor >= 0) {
            setTimeout(function() {
                textarea.selectionStart = textarea.selectionEnd = savedCursor;
                textarea.focus();
                console.log("Курсор восстановлен на позиции:", savedCursor);
            }, 100);
        }

        // Перед отправкой формы обновляем значение cursor_position
        document.querySelector("form").addEventListener("submit", function() {
            cursorInput.value = textarea.selectionStart;
            console.log("Курсор перед отправкой формы:", cursorInput.value);
        });

        // Отслеживание изменений позиции курсора
        textarea.addEventListener("keyup", function() {
            cursorInput.value = textarea.selectionStart;
            console.log("Текущая позиция курсора (обновлена):", cursorInput.value);
        });

        textarea.addEventListener("click", function() {
            cursorInput.value = textarea.selectionStart;
            console.log("Текущая позиция курсора (обновлена):", cursorInput.value);
        });
    });
</script>