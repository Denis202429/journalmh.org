{{-- @extends('layouts.main')

@section('page.title', 'Заполнение базы данных')

@section('main.content')
    <x-form-item>
        <x-label required>{{ __('Содержание статьи') }}</x-label>
        {{-- <x-trix name="content" value="{{ $post->content ?? '' }}" /> --}}
{{-- <x-trix name="content" /> --}}
{{-- <x-error name="content" />
    </x-form-item>
    <x-form action="{{ route('bd.store') }}" method="post">
        <div class="input-group"  >
            <span class="input-group-text">Автор</span>
            <textarea name='author' class="form-control"  aria-label="With textarea"></textarea>
        </div>
        <x-button type="submit">
            {{ __('Запись в базу данных') }}
        </x-button>
    </x-form>

@endsection --}}
@extends('layouts.base')

@section('page.title', 'Заполнение базы данных')

@section('content')

    {{-- <x-form action="{{ route('register.store') }}" method="POST"> --}}
    <form action="{{ route('bd.store') }}" method="POST">
        @csrf
        {{-- <div class="row mt-3"> --}}
        <div class="container">
            <h4 class="m-0 mt-5">
                {{ __('Заполнение базы данных') }}
            </h4>

            <div class="row mt-3">
                <div class="col">
                    {{-- <label class="form-label">Год издания</label> --}}
                    <span class="input-group-text">Автор. Например: Петров.П.Т. или Петров.П.Т.,Иванов А.И. </span>
                    <textarea name='Autor' class="form-control" aria-label="With textarea" value="{{ old('Autor') }}"></textarea>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    {{-- <label class="form-label">Год издания</label> --}}
                    <span class="input-group-text">Название. Например: Сборник стихов</span>
                    <textarea name='title_article' class="form-control" aria-label="With textarea"value="{{ old('title_article') }}"></textarea>
                </div>
            </div>



            <div class="row mt-3">
                <div class="col">
                    {{-- <label class="form-label">Год издания</label> --}}
                    <span class="input-group-text">Год создания. Например:2007</span>
                    <textarea name='year_creation' class="form-control" aria-label="With textarea"></textarea>
                </div>
            </div>


            <div class="row mt-3">
                <div class="col">
                    {{-- <label class="form-label">Год издания</label> --}}
                    <span class="input-group-text">Год издания. Например:2007</span>
                    <textarea name='year_publication' class="form-control" aria-label="With textarea"></textarea>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    {{-- <label class="form-label">Год издания</label> --}}
                    <span class="input-group-text">Место издания</span>
                    <textarea name='place_publication' class="form-control" aria-label="With textarea"></textarea>
                </div>
            </div>



      
        <div class="input-group mt-3">
            <select name="genre" class="form-select" id="inputGroupSelect02">
                {{-- <option selected>Выберите жанр</option> --}}

                {{-- <option value="">Выберите жанр</option> --}}
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
                <option value="Научно-популярные тексты">Научно-популярные тексты</option>
                <option value="Прозаические тексты">Прозаические тексты</option>
                <option value="Поэтические тексты">Поэтические тексты</option>
                <option value="Драматургия">Драматургия</option>
                <option value="Публицистические тексты">Публицистические тексты</option>
                <option value="Устное народное творечество">Устное народное творечество</option>
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

        <div class="mb-3">
            <label for="textarea1" class="form-label">{{ __('Содержание статьи') }}</label>
            <textarea class="form-control" id="textarea1" name="content" rows="10"></textarea>
        </div>

        <button type="submit" class="btn btn-primary"> {{ __('Записать в БД') }}</button>

        </div>
    </div>

    </form>


@endsection
