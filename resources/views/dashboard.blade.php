@extends('layouts.base') {{-- Если у вас есть базовый шаблон --}}

@section('page.title', 'Dashboard - ' . config('app.name'))
@section('content')

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-4">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Панель управления
                    </h1>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Добро пожаловать, {{ Auth::user()->name }}!</h5>
                                <p class="mb-0">
                                    Ваша роль: 
                                    @if(auth()->user()->isSuperAdmin())
                                        <span class="badge bg-danger">Суперадмин</span>
                                    @elseif(auth()->user()->isAdmin())
                                        <span class="badge bg-warning text-dark">Администратор</span>
                                    @elseif(auth()->user()->isCorrector())
                                        <span class="badge bg-primary">Корректор</span>
                                    @else
                                        <span class="badge bg-secondary">Пользователь</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        @if(auth()->check() && auth()->user()->isCorrector())
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-chat-left-text text-primary me-2"></i>
                                        Корректура
                                    </h5>
                                    <p class="card-text">Работа с текстами для проверки и редактирования</p>
                                    <a href="{{ route('gigachat.index') }}" class="btn btn-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-journal-text text-primary me-2"></i>
                                        Выпуски журнала
                                    </h5>
                                    <p class="card-text">Добавление и редактирование номеров (выпусков)</p>
                                    <a href="{{ route('admin.issues.index') }}" class="btn btn-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-file-earmark-richtext text-primary me-2"></i>
                                        Статьи
                                    </h5>
                                    <p class="card-text">Добавление и редактирование статей внутри выпусков</p>
                                    <a href="{{ route('admin.articles.index') }}" class="btn btn-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-palette text-primary me-2"></i>
                                        Дизайн сайта
                                    </h5>
                                    <p class="card-text">Переключение между доступными темами оформления</p>
                                    <a href="{{ route('admin.design.edit') }}" class="btn btn-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-people text-primary me-2"></i>
                                        Пользователи
                                    </h5>
                                    <p class="card-text">Управление учетными записями и правами</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-person text-primary me-2"></i>
                                        Профиль
                                    </h5>
                                    <p class="card-text">Настройки учетной записи</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                        Перейти <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection