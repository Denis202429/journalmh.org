@php
    // function highlightSearchTerm($context, $searchTerm)
    // {
    //     //   dump($context, $searchTerm);
    //     return str_replace($searchTerm, '<span class="highlighted">' . $searchTerm . '</span>', $context);
    // }

    function highlightSearchTerm($text, $term, $highlightClass)
    {
        if (!$term) {
            return $text;
        }

        $term = preg_quote($term, '/');
        return preg_replace("/($term)/iu", "<span class=\"$highlightClass\">$1</span>", $text);
    }

@endphp
{{-- Теперь слово $wordsInContent будет выделено с использованием классов Bootstrap 5, и вы можете настроить стили highlight в вашем CSS-файле по вашему усмотрению. --}}

@extends('layouts.base')

@section('page.title', 'Поиск')

@section('content')

    <div class="container">

        <h1 class="mt-5 mb-3">Введите данные для поиска</h1>
        <form action="{{ route('bd.find2') }}" method="GET">
            @csrf
            {{-- <div class="form-check">
                <input class="form-check-input" type="radio" name="search_operator" id="andOperator" value="AND" checked>
                <label class="form-check-label" for="andOperator">
                    При поиске используется логическое 'И' для полей ввода
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="search_operator" id="orOperator" value="OR">
                <label class="form-check-label" for="orOperator">
                    При поиске используется логическое 'ИЛИ' для полей ввода
                </label>
            </div> --}}


            <div class="mb-3">
                <label for="inputText" class="form-label">Автор</label>
                <input name="autor" value="{{ old('autor') }}" type="text" class="form-control" id="inputText"  placeholder="Введите автора">
            </div>
            {{-- //   'Ç', 'ç', 'Ӑ', 'ӑ', 'Ӗ', 'ӗ', 'Ӳ', 'ӳ' --}}
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

            <div class="mb-3">
                <label for="inputText2" class="form-label">Название</label>
                <input name="title" value="{{ old('title') }}" type="text" class="form-control" id="inputText2" placeholder="Введите название">
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-success" onclick="addSymbol2('Ҫ')">Ç</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('ҫ')">ç</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('Ӑ')">Ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('ӑ')">ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('Ӗ')">Ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('ӗ')">ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('Ӳ')">Ӳ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol2('ӳ')">ӳ</button>
            </div>

            <div class="mb-3">
                <label for="inputText3" class="form-label">Слово или выражение</label>
                <input name="wordsInContent" type="text" class="form-control" id="inputText3"
                    placeholder="Введите текст">
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-success" onclick="addSymbol3('Ҫ')">Ç</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('ҫ')">ç</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('Ӑ')">Ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('ӑ')">ӑ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('Ӗ')">Ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('ӗ')">ӗ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('Ӳ')">Ӳ</button>
                <button type="button" class="btn btn-success" onclick="addSymbol3('ӳ')">ӳ</button>
            </div>

            <div class="mb-3">
                <label for="inputText3" class="form-label">Год</label>
                <input name="year" type="text" class="form-control" id="inputText3" placeholder="Введите год">
            </div>

            <div class="mb-3">
                <label for="inputText3" class="form-label">Место</label>
                <input name="place" type="text" class="form-control" id="inputText3" placeholder="Введите место">
            </div>

            <div class="input-group mt-3">
                <select name="genre" class="form-select" id="inputGroupSelect02">
                    <option value="">Выберите жанр</option>
                    <option value="Не определен">Не определен</option>
                    <option value="Повесть">Повесть</option>
                    <option value="Рассказ">Рассказ</option>
                    <option value="Роман">Роман</option>
                    <option value="Очерк">Очерк</option>
                    <option value="Новелла">Новелла</option>
                    <option value="Фельетон">Фельетон</option>
                    <option value="Юморезка">Юморезка</option>
                    <option value="Поэма">Поэма</option>
                    <option value="Стихотворение">Стихотворение</option>
                    <option value="Пьеса">Пьеса</option>
                    <option value="Драма">Драма</option>
                    <option value="Комедия">Комедия</option>
                    <option value="Трагедия">Трагедия</option>
                    <option value="Басня">Басня</option>
                    <option value="Сказка">Сказка</option>
                    <option value="Песня">Песня</option>
                    <option value="Частушка">Частушка</option>
                    <option value="Предисловие">Предисловие</option>
                    <option value="Мемуарная и эпистолярная литература">Мемуарная и эпистолярная литература</option>
                    <option value="Баллада">Баллада</option>
                    <option value="Сонет">Сонет</option>
                    <option value="Быль">Быль</option>
                    <option value="Эссе">Эссе</option>
                    <option value="Рецензия,отзыв">Рецензия,отзыв</option>
                    <option value="Романс">Романс</option>
                    <option value="Монолог">Монолог</option>
                    <option value="Автобиография">Автобиография</option>
                    <option value="Сценка">Сценка</option>
                    <option value="Инсценировка">Инсценировка</option>
                    <option value="Рапсодия">Рапсодия</option>
                    <option value="Памфлет">Памфлет</option>
                    <option value="Трагикомедия">Трагикомедия</option>
                    <option value="Эскиз">Эскиз</option>
                    <option value="Зарисовка">Зарисовка</option>
                </select>
                <label class="input-group-text" for="inputGroupSelect02">Жанр</label>
            </div>
            <div class="input-group mt-3">
                <select name="category" class="form-select" id="inputGroupSelect02">
                    <option value="">Выберите категорию</option>
                    <option value="Научно-популярные тексты">Научно-популярные тексты</option>
                    <option value="Прозаические тексты">Прозаические тексты</option>
                    <option value="Поэтические тексты">Поэтические тексты</option>
                    <option value="Драматургия">Драматургия</option>
                    <option value="Публицистические тексты">Публицистические тексты</option>
                    <option value="Устное народное творечество">Устное народное творчество</option>
                    <option value="Официально-деловые тексты">Официально-деловые тексты</option>
                    <option value="Интернет-ресурсы">Интернет-ресурсы</option>
                    <option value="Христианство">Христианство</option>
                    <option value="Мусульманство">Мусульманство</option>
                    <option value="Буддизм">Буддизм</option>
                    <option value="Прочие религиозные течения">Прочие религиозные течения</option>
                    <option value="Словари">Словари</option>
                    <option value="Не определена">Не определена</option>
                </select>
                <label class="input-group-text" for="inputGroupSelect02">Категория</label>
            </div>
 

            <button type="submit" class="btn btn-primary mt-3">Поиск</button>
        </form>
  


        @if (isset($results))
            <h1>Результаты поиска</h1>
            <ul>
                @foreach ($results as $key => $row)
                    <li>

                        @if (auth()->check() && auth()->user()->isAdmin())
                           <strong>Редактировать</strong> <a href="{{ route('admin.BD_edit', $row->id) }}" class="btn btn-primary">Редактировать</a> <br>
                           <!-- <strong>Удалить</strong> <a href="{{ route('admin.BD_edit', $row->id) }}" class="btn btn-danger">Удалить</a> <br> -->

                        @endif    
                        <strong>Автор:</strong> {{ $row->Autor }}<br>
                        <strong>Название:</strong> {{ $row->title_article }}<br>
                        <strong>Год:</strong> {{ $row->year_publication }}<br>
                        <strong>Место:</strong> {{ $row->place_publication }}<br>
                        <strong>Категория:</strong> {{ $row->category }}<br>
                        <strong>URL:</strong>   <a href="{{ $row->url }}" target="_blank">{{ $row->url }}</a> <br>
                        

                        @if (isset($GLcontexts[$key]) && is_array($GLcontexts[$key]) && count($GLcontexts[$key]) > 0)
                            <strong>Контексты:</strong>
                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-link" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $key }}">
                                        Показать больше
                                    </button>
                                </div>
                                <div id="collapse{{ $key }}" class="collapse">
                                    <div class="card-body">
                                        @foreach ($GLcontexts[$key] as $context)
                                            {!! highlightSearchTerm($context, request()->wordsInContent, 'highlighted') !!}<br>
                                            <hr class="dropdown-divider" style="border-color: blue;">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <p>Контексты не найдены</p>
                        @endif
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                @endforeach
            </ul>
            {{ $results->appends(request()->except('page'))->links() }}
        @else
            <p>Введите поисковый запрос и нажмите "Поиск" для получения результатов.</p>
        @endif


    </div>

@endsection

<script>
    function addSymbol(symbol) {
        var inputText = document.getElementById('inputText');
        inputText.value += symbol;
    }

    function addSymbol2(symbol) {
        var inputText = document.getElementById('inputText2');
        inputText.value += symbol;
    }

    function addSymbol3(symbol) {
        var inputText = document.getElementById('inputText3');
        inputText.value += symbol;
    }


    $(document).ready(function() {
        // Инициализация Bootstrap collapse
        $('.collapse').collapse();
    });
    // src = "https://cdn.jsdelivr.net/npm/bootstrap@5.7.0/dist/js/bootstrap.min.js">
</script>
