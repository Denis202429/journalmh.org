@extends('layouts.base')

@section('page.title', 'Дизайн сайта')

@section('content')
    <div class="container mt-4" style="max-width: 960px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Дизайн сайта</h2>
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

        <form action="{{ route('admin.design.update') }}" method="POST" class="mt-4">
            @csrf
            @method('PUT')

            <div class="row g-4">
                @foreach($themes as $key => $item)
                    <div class="col-md-6 col-lg-4">
                        <label class="card h-100 border-0 shadow-sm" style="cursor: pointer;">
                            <div class="card-body">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="theme"
                                        id="theme_{{ $key }}"
                                        value="{{ $key }}"
                                        {{ $theme === $key ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label fw-bold" for="theme_{{ $key }}">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                <p class="text-muted mt-3 mb-0">{{ $item['description'] }}</p>

                                @if($key === 'classic')
                                    <div class="mt-3 p-3 rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #fad399 100%); border: 1px solid #ece3db;">
                                        <div style="font-family: 'Playfair Display', serif; color: #2c5048; font-weight: 700;">Современная гуманитаристика</div>
                                        <small style="color: #6f847f;">Светлая журнальная палитра</small>
                                    </div>
                                @elseif($key === 'midnight')
                                    <div class="mt-3 p-3 rounded-4" style="background: linear-gradient(135deg, #0b1220 0%, #131c31 100%); border: 1px solid #334155;">
                                        <div style="font-family: 'Playfair Display', serif; color: #9ccfd8; font-weight: 700;">Современная гуманитаристика</div>
                                        <small style="color: #cbd5e1;">Тёмная редакционная палитра</small>
                                    </div>
                                @else
                                    <div class="mt-3 p-3 rounded-4" style="background: linear-gradient(135deg, #f7f1e3 0%, #efe3c4 100%); border: 1px solid #d9c8a0;">
                                        <div style="font-family: 'Playfair Display', serif; color: #6e2c2c; font-weight: 700;">Современная гуманитаристика</div>
                                        <small style="color: #8c6b4f;">Бумажная журнальная палитра</small>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-palette"></i> Применить дизайн
                </button>
            </div>
        </form>
    </div>
@endsection

