@extends('layouts.base')

@section('page.title', $page->title)

@section('content')

<section class="section" style="padding-top: 70px; padding-bottom: 40px; background: linear-gradient(135deg, var(--bg) 0%, var(--bg-light) 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- АДАПТИВНЫЙ ЗАГОЛОВОК -->
                <h1 style="font-size: clamp(2rem, 8vw, 4rem); line-height: 1.2; margin-bottom: 1.2rem;">
                    <span style="color: var(--accent);">Современная гуманитаристика</span>
                </h1>

                <!-- АДАПТИВНЫЙ АБЗАЦ -->
                <p style="font-size: clamp(1rem, 4vw, 1.3rem); color: var(--text-light); margin-bottom: 2rem; max-width: 640px; margin-left: 0;">
                    Периодическое рецензируемое научное издание, представляющее результаты исторических, филологических и искусствоведческих исследований на русском, английском и чувашском языках
                </p>

                <!-- АДАПТИВНЫЕ КНОПКИ -->
                <!-- <div class="d-flex gap-3 mb-4 flex-wrap">
                    <a href="#archive" class="btn btn-lg"
                        style="border-radius: 40px; padding: 0.7rem 1.5rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); font-size: clamp(0.8rem, 3vw, 1rem);">
                        <i class="bi bi-journal"></i> Текущий выпуск
                    </a>
                </div> -->

                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <a href="{{ route('current.issue') }}" class="btn btn-lg"
                        style="border-radius: 40px; padding: 0.7rem 1.5rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); font-size: clamp(0.8rem, 3vw, 1rem);">
                        <i class="bi bi-journal"></i> Текущий выпуск
                    </a>
                </div>

            </div>

            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div style="background: var(--bg); border: 2px solid var(--border); border-radius: 20px 20px 20px 0; padding: 1.5rem;">
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 0.2rem;">
                        Быстрые ссылки
                    </div>
                    <div class="d-grid gap-2">
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#about">
                            <i class="bi bi-info-circle"></i> Для авторов
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent; font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#editors">
                            <i class="bi-bookshelf"></i> Архив выпусков
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.7rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent; font-size: clamp(0.8rem, 3vw, 0.9rem);"
                            href="#contacts">
                            <i class="bi bi-people"></i> Контакты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<section class="section">
    <div class="container" style="max-width: 980px;">


        <h1 style="font-size: 2.8rem; margin-bottom: 1.2rem;">{{ $page->title }}</h1>
        <div style="color: var(--text-light); font-size: 1.05rem; line-height: 1.9;">
            {!! $page->content ?? '' !!}
        </div>
    </div>
</section>
@endsection
