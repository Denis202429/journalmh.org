@extends('layouts.base')

@section('page.title', 'Страницы сайта')

@section('content')
    <div class="container mt-4">
        <h2>Страницы сайта</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Slug</th>
                        <th>Публикация</th>
                        <th style="min-width: 200px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr>
                            <td>{{ $page->id }}</td>
                            <td>{{ $page->title }}</td>
                            <td><code>{{ $page->slug }}</code></td>
                            <td>
                                @if($page->is_published)
                                    <span class="badge bg-success">Опубликована</span>
                                @else
                                    <span class="badge bg-secondary">Скрыта</span>
                                @endif
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                                <a href="{{ route('journal.page', $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right"></i> Открыть
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

