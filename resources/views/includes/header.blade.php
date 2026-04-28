<nav class="navbar navbar-expand-lg fixed-top navbar-light bg-white">
    <div class="container">

        <!-- ========== ЛОГОТИП ========== -->
        <a class="navbar-brand me-auto" href="{{ route('home') }}">
            <div class="fs-6 fs-md-5 fs-lg-4 fs-xl-3">
                Современная гуманитаристика
            </div>
            <span class="small text-muted d-block">Научный журнал</span>
        </a>

        <!-- ========== ГАМБУРГЕР (виден только на мобильных) ========== -->
        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Меню">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ========== ВЫПАДАЮЩЕЕ МЕНЮ ========== -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Основное меню -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Главная</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="journalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        О журнале
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="journalDropdown">
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'about-journal') }}">Политика издания</a></li>
                        <!-- <li><a class="dropdown-item" href="{{ route('issues.index') }}">Архив номеров</a></li> -->
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'editorial-board') }}">Редакционный совет и коллегия</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'ethics') }}">Публикационная этика</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'reviewing') }}">Рецензирование</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'indexing') }}">Индексирование</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'policy') }}">Новости</a></li>
                        <!-- <li><a class="dropdown-item" href="{{ route('journal.page', 'public-offer') }}">Публичная оферта</a></li> -->
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="contactsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Для читателей
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="contactsDropdown">
                        <li><a class="dropdown-item" href="{{ route('current.issue', 'contacts') }}">Текущий номер</a></li>
                        <li><a class="dropdown-item" href="{{ route('show_archiv') }}">Архив</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'retraction') }}">Ретракция</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="authorsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Для авторов
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="authorsDropdown">
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'general-order-of-publication') }}">Общий порядок публикации</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'requirements-for-articles') }}">Требования к статьям, представляемым для опубликования в журнале</a></li>
                        <li><a class="dropdown-item" href="{{ route('journal.page', 'instructions-for-reviewing') }}">Инструкция по отзыву и исправлению статей</a></li>
                        <!-- <li><a class="dropdown-item" href="{{ route('journal.page', 'references-generator') }}">Генератор списка литературы</a></li> -->
                    </ul>
                </li>



                <!-- 🔹 Поиск внутри мобильного меню (виден только на мобильных) -->
                <li class="nav-item d-lg-none mt-3 pt-2 border-top">
                    <form action="{{ route('search.index') }}" method="GET" class="d-flex" role="search">
                        <input class="form-control form-control-sm me-2" type="search" name="q"
                            value="{{ request('q') }}" placeholder="Поиск..." aria-label="Поиск по сайту">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </li>

                <!-- 🔹 ЯЗЫКОВОЙ ПЕРЕКЛЮЧАТЕЛЬ ДЛЯ МОБИЛЬНЫХ (виден только в гамбургере) -->
                <li class="nav-item d-lg-none mt-2 pt-2 border-top">
                    <span class="nav-link text-muted small mb-1 px-3">Язык интерфейса:</span>
                    <div class="d-flex gap-2 px-3 pb-2">
                        <a class="btn btn-sm {{ app()->getLocale() === 'ru' ? 'btn-primary' : 'btn-outline-secondary' }}"
                            href="{{ route('lang.switch', 'ru') }}">RU</a>
                        <a class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-secondary' }}"
                            href="{{ route('lang.switch', 'en') }}">EN</a>
                    </div>
                </li>

                <!-- Админ-меню (видимо на всех устройствах) -->
                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('messages.admin') }}
                    </a>
                    <ul class="dropdown-menu">

                        <li><a class="dropdown-item" href="{{ route('admin.pages.index') }}">Страницы сайта</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.issues.index') }}">Выпуски журнала</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.articles.index') }}">Статьи</a></li>

                        @if (auth()->check() && auth()->user()->isSuperAdmin())
                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Управление пользователями</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.design.edit') }}">Дизайн сайта</a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>

            <!-- ========== ПРАВАЯ ЧАСТЬ: ЯЗЫК + АВТОРИЗАЦИЯ ========== -->
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">

                <!-- 🔹 ЯЗЫК (виден ТОЛЬКО на десктопе, сразу в навбаре) -->
                <li class="nav-item d-none d-lg-block ms-3 me-2">
                    <div class="d-flex align-items-center gap-1">
                        <a class="nav-link py-1 px-2 {{ app()->getLocale() === 'ru' ? 'fw-bold text-primary' : '' }}"
                            href="{{ route('lang.switch', 'ru') }}">RU</a>
                        <span class="text-muted">|</span>
                        <a class="nav-link py-1 px-2 {{ app()->getLocale() === 'en' ? 'fw-bold text-primary' : '' }}"
                            href="{{ route('lang.switch', 'en') }}">EN</a>
                    </div>
                </li>

                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('messages.Login') }}</a>
                </li>
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Профиль</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item">{{ __('messages.Logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endguest
            </ul>

        </div>
    </div>
</nav>