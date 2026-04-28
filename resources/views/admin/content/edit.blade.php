@extends('layouts.base')

@section('page.title', 'Редактор разделов сайта')

@section('content')
    <div class="container mt-4" style="max-width: 1100px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h2 class="mb-0">Редактор разделов сайта</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> В панель
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <div class="fw-bold mb-2">Ошибка сохранения</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.content.update') }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Меню Header: Журнал</h5>
                    <p class="text-muted mb-2">Формат: одна строка = <code>Название|ссылка</code>. Ссылка может быть якорем <code>#about</code> или URL.</p>
                    <textarea name="header_journal_menu" rows="8" class="form-control">{{ old('header_journal_menu', $contents['header_journal_menu']->content ?? $defaults['header_journal_menu']['content']) }}</textarea>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Меню Header: Для авторов</h5>
                    <p class="text-muted mb-2">Формат такой же: <code>Название|ссылка</code>.</p>
                    <textarea name="header_authors_menu" rows="6" class="form-control">{{ old('header_authors_menu', $contents['header_authors_menu']->content ?? $defaults['header_authors_menu']['content']) }}</textarea>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Главная: секция "Авторам"</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Заголовок секции</label>
                            <input type="text" name="home_authors_section_title" class="form-control"
                                value="{{ old('home_authors_section_title', $contents['home_authors_section_title']->content ?? $defaults['home_authors_section_title']['content']) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Карточка 1: заголовок</label>
                            <input type="text" name="home_authors_card1_title" class="form-control"
                                value="{{ old('home_authors_card1_title', $contents['home_authors_card1_title']->content ?? $defaults['home_authors_card1_title']['content']) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Карточка 2: заголовок</label>
                            <input type="text" name="home_authors_card2_title" class="form-control"
                                value="{{ old('home_authors_card2_title', $contents['home_authors_card2_title']->content ?? $defaults['home_authors_card2_title']['content']) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Карточка 1: текст</label>
                            <textarea name="home_authors_card1_text" rows="4" class="form-control">{{ old('home_authors_card1_text', $contents['home_authors_card1_text']->content ?? $defaults['home_authors_card1_text']['content']) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Карточка 2: текст</label>
                            <textarea name="home_authors_card2_text" rows="4" class="form-control">{{ old('home_authors_card2_text', $contents['home_authors_card2_text']->content ?? $defaults['home_authors_card2_text']['content']) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Сохранить изменения
                </button>
                <a href="{{ route('admin.content.edit') }}" class="btn btn-outline-secondary">Сбросить форму</a>
            </div>
        </form>
    </div>
@endsection

