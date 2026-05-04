@extends('layouts.base')

@section('content')
@php
$authorsSectionTitle = trim($siteContentMap['home_authors_section_title'] ?? '') ?: 'Авторам';
$authorsCard1Title = trim($siteContentMap['home_authors_card1_title'] ?? '') ?: 'Требования к материалам';
$authorsCard1Text = trim($siteContentMap['home_authors_card1_text'] ?? '') ?: 'Оформление, структура, библиография, этика публикаций и порядок рассмотрения.';
$authorsCard2Title = trim($siteContentMap['home_authors_card2_title'] ?? '') ?: 'Подача статьи';
$authorsCard2Text = trim($siteContentMap['home_authors_card2_text'] ?? '') ?: 'Контакты редакции и порядок подачи материалов (можем привязать к вашей форме/почте).';
@endphp

<!-- <section class="section" style="padding-top: 70px; padding-bottom: 40px; background: linear-gradient(135deg, var(--bg) 0%, var(--bg-light) 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 style="font-size: 4rem; line-height: 1; margin-bottom: 1.2rem;">
                    <span style="color: var(--accent); ">Современная гуманитаристика</span>
                </h1>
                <p style="font-size: 1.3rem; color: var(--text-light); margin-bottom: 3rem; max-width: 640px; margin-left: 5rem;">
                    Периодическое рецензируемое научное издание, представляющее результаты исторических, филологических и искусствоведческих исследований на русском, английском и чувашском языках
                </p>

                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <a href="#archive" class="btn btn-lg"
                        style="border-radius: 40px; padding: 0.9rem 2.2rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
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
                            style="border-radius: 40px; padding: 0.85rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: #fff; border: 2px solid var(--primary); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);"
                            href="#about">
                            <i class="bi bi-info-circle"></i> Для авторов
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.85rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent;"
                            href="#editors">
                            <i class="bi-bookshelf"></i> Архив выпусков
                        </a>
                        <a class="btn"
                            style="border-radius: 40px; padding: 0.85rem 1.2rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--primary); border: 2px solid var(--primary); background: transparent;"
                            href="#contacts">
                            <i class="bi bi-people"></i> Контакты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->
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
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <a href="#archive" class="btn btn-lg"
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




<section class="section" id="about">
    <div class="container">
        <div class="row align-items-start g-4">
            <div class="col-lg-8">
                <h2 style="font-size: 2.6rem; margin-bottom: 1rem;">Сведения об издании</h2>


                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Название:</span> «Современная гуманитаристика»
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Учредитель:</span> Бюджетное научное учреждение Чувашской Республики «Чувашский государственный институт гуманитарных наук» Министерства образования Чувашской Республики
                    Издается с 2025 г.
                    Выходит 4 раза в год.
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Цели и задачи журнала:</span> освещение актуальных для обсуждения в международном сообществе проблем и публикация научно-исследовательских работ, имеющих теоретическую и практическую значимость для гуманитарной сферы; создание платформы для обсуждения академических научных исследований; содействие сохранению и развитию языков, культур, традиций народов России.
                    Журнал является преемником ежегодного научного журнала «Чувашский гуманитарный вестник», издававшегося Чувашским государственным институтом гуманитарных наук с 2006 г. по 2024 г.
                    В журнале публикуются статьи известных и молодых ученых, профессоров, преподавателей и аспирантов из России и зарубежных стран <span class="fw-bolder">на трех языках:</span> русском, английском, чувашском.
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Тематические разделы журнала</span> созданы в соответствии с шифрами научных специальностей ВАК:
                    5.6. Исторические науки (5.6.1. Отечественная история, 5.6.3. Археология, 5.6.4. Этнология, антропология и этнография)
                    5.9. Филология (5.9.1. Русская литература и литературы народов Российской Федерации, 5.9.4. Фольклористика, 5.9.5. Русский язык. Языки народов России, 5.9.8. Теоретическая, прикладная и сравнительно-сопоставительная лингвистика)
                    5.10. Искусствоведение и культурология (5.10.3 Виды искусства (музыкальное искусство; театральное искусство; изобразительное, декоративно-прикладное искусство и архитектура)).
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Индексирование:</span> <a href="https://elibrary.ru/title_about_new.asp?id=174116">Российский индекс научного цитирования</a> (НЭБ).
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Рецензирование:</span> двойное слепое.
                </p>

                <p class="text-justify" style="color: var(--text-light); font-size: 1.05rem;">
                    <span class="fw-bolder">Публикация статей</span> – бесплатно.
                    Подача рукописей статей для публикации производится на e-mail редакции: sovrem_human@rambler.ru (отв. редактор — Гаврилов Артем Дмитриевич)
                    ISSN 3034-6827 (print)
                </p>
            </div>

            <div class="col-lg-4">
                <div style="background: var(--bg-light); border: 2px solid var(--border-light); border-radius: 30px 0 30px 0; padding: 1.8rem;">
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Журнал придерживается:</div>
                    <ul class="mb-0" style="color: var(--text-soft); padding-left: 1.1rem;">
                        <li class="mb-2">- политики открытого доступа (Open Access)</li>
                        <li class="mb-2">- международных стандартов публикационной этики, сформулированных в документе COPE (Committee on Publication Ethics)</li>
                        <!-- <li class="mb-2">Научная этика и прозрачность</li>
                        <li>Индексирование и распространение</li> -->
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section" id="archive">
    <div class="container">
        <h2 style="font-size: 2.6rem; margin-bottom: 1.2rem;">Для читателей</h2>

        @if(isset($issues) && $issues->count())
        <p style="color: var(--text-light); margin-bottom: 2rem;">Опубликованные выпуски.</p>
        <div class="row g-3">
            @foreach($issues as $issue)
            <div class="col-md-4">
                <div style="background: var(--bg); border: 2px solid var(--border); border-radius: 20px 20px 20px 0; padding: 1.4rem;">
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700;">
                        @if($issue->volume && $issue->number)
                        Том {{ $issue->volume }}, № {{ $issue->number }}
                        @elseif($issue->number)
                        № {{ $issue->number }}
                        @else
                        Выпуск
                        @endif
                    </div>

                    <div style="color: var(--accent); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">
                        {{ $issue->month ? $issue->month . ' ' : '' }}{{ $issue->year }}
                    </div>

                    @if($issue->title)
                    <div class="mt-2" style="color: var(--text-light); font-style: italic;">
                        {{ $issue->title }}
                    </div>
                    @endif

                    <!-- Обложка (если есть) -->
                    @if($issue->cover_image_path)
                    <div class="text-center mt-3">
                        <img src="{{ route('issue.cover', $issue) }}?t={{ $issue->updated_at->timestamp }}"
                            alt="Обложка выпуска"
                            class="img-fluid"
                            style="max-height: 200px; object-fit: cover;">
                    </div>
                    @endif

                    <!-- Тип выпуска и кнопки -->
                    <div class="mt-3">
                        <span class="badge bg-primary mb-2">{{ $issue->issue_type_label }}</span>

                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list"></i> Содержание
                            </a>
                            @if($issue->pdf_file_path)
                            <a href="{{ route('download.issue.pdf', $issue) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-file-pdf"></i> PDF
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p style="color: var(--text-light); margin-bottom: 0;">Пока нет опубликованных выпусков. Добавьте их в админке.</p>
        @endif
    </div>
</section>


<section class="section" id="editors">
    <div class="container">
        <h2 style="font-size: 2.6rem; margin-bottom: 2rem;">Для авторов</h2>

        <div class="row g-4">
            <!-- Раздел 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Общий порядок публикации </h3>
                        <!-- <p class="text-muted">Пошаговая инструкция по оформлению и подаче рукописи в журнал</p> -->
                        <a href="#" class="btn btn-outline-primary btn-sm mt-2">Подробнее →</a>
                    </div>
                </div>
            </div>

            <!-- Раздел 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Требования к статьям </h3>

                        <!-- <p class="text-muted">Технические требования, структура и правила оформления</p> -->
                        <a href="#" class="btn btn-outline-success btn-sm mt-2">Подробнее →</a>
                    </div>
                </div>
            </div>

            <!-- Раздел 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-info">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Инструкция по отзыву и исправлению статей</h3>
                        <!-- <p class="text-muted">Процедура рецензирования и внесения правок</p> -->
                        <a href="#" class="btn btn-outline-info btn-sm mt-2">Подробнее →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section py-5" id="for-authors">
    <div class="container">
        <!-- Заголовок с декоративной линией -->
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" style="font-family:'Playfair Display', serif;">Новости</h2>
            <div class="mx-auto" style="width: 80px; height: 4px; background: linear-gradient(90deg, #0d6efd, #0dcaf0); border-radius: 2px;"></div>
            <p class="text-muted mt-3">Будьте в курсе последних событий</p>
        </div>

        <div class="row g-4">
            <!-- Новость 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card" style="border-radius: 20px; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-primary bg-gradient px-3 py-2 rounded-pill">
                                <i class="bi bi-megaphone"></i> Событие
                            </div>
                            <small class="text-muted ms-auto">
                                <i class="bi bi-calendar3"></i> 15 апреля 2026
                            </small>
                        </div>
                        <h3 class="card-title h4 fw-bold mb-3" style="font-family:'Playfair Display', serif;">
                            Запуск нового сезона
                        </h3>
                        <p class="card-text text-muted">
                            Открыт прием заявок на участие в новом литературном сезоне. Присоединяйтесь к нам!
                        </p>
                        <a href="#" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                            Подробнее <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Новость 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-success bg-gradient px-3 py-2 rounded-pill">
                                <i class="bi bi-trophy"></i> Конкурс
                            </div>
                            <small class="text-muted ms-auto">
                                <i class="bi bi-calendar3"></i> 10 апреля 2026
                            </small>
                        </div>
                        <h3 class="card-title h4 fw-bold mb-3" style="font-family:'Playfair Display', serif;">
                            Итоги конкурса поэзии
                        </h3>
                        <p class="card-text text-muted">
                            Объявлены победители ежегодного конкурса молодых поэтов. Спасибо всем участникам!
                        </p>
                        <a href="#" class="btn btn-outline-success rounded-pill px-4 mt-2">
                            Читать <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Новость 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-info bg-gradient px-3 py-2 rounded-pill text-dark">
                                <i class="bi bi-chat-dots"></i> Встреча
                            </div>
                            <small class="text-muted ms-auto">
                                <i class="bi bi-calendar3"></i> 5 апреля 2026
                            </small>
                        </div>
                        <h3 class="card-title h4 fw-bold mb-3" style="font-family:'Playfair Display', serif;">
                            Клуб книголюбов
                        </h3>
                        <p class="card-text text-muted">
                            Приглашаем на ежемесячную встречу книжного клуба. Тема: современная проза.
                        </p>
                        <a href="#" class="btn btn-outline-info rounded-pill px-4 mt-2">
                            Записаться <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопка "Все новости" -->
        <div class="text-center mt-5">
            <a href="#" class="btn btn-lg btn-dark rounded-pill px-5 py-3">
                Все новости <i class="bi bi-newspaper"></i>
            </a>
        </div>
    </div>
</section>


<!-- <section class="section" id="contacts">
    <div class="container">
        <h2 style="font-size: 2.6rem; margin-bottom: 1.2rem;">Контакты</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div style="background: var(--bg-light); border: 2px solid var(--border-light); border-radius: 30px 0 30px 0; padding: 1.8rem;">
                    <div class="mb-2"><i class="bi bi-geo-alt"></i> 428015, Чувашская Республика, город Чебоксары, Московский проспект, 29, корпус 1.</div>
                    <div class="mb-2"><i class="bi bi-envelope"></i> sovrem_human@rambler.ru</div>
                
                </div>
            </div>

        </div>
    </div>
</section> -->


<section class="section py-5" id="contacts">
    <div class="container">
        <!-- Заголовок -->
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" style="font-family:'Playfair Display', serif;">Контакты</h2>
            <div class="mx-auto" style="width: 80px; height: 4px; background: linear-gradient(90deg, #0d6efd, #0dcaf0); border-radius: 2px;"></div>
            <p class="text-muted mt-3">Свяжитесь с нами любым удобным способом</p>
        </div>

        <div class="row g-4">
            <!-- Контактная информация -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 30px; overflow: hidden;">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="h4 fw-bold mb-4">
                            <i class="bi bi-chat-dots text-primary me-2"></i>Напишите нам
                        </h3>

                        <!-- Адрес -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">Адрес</h4>
                                <p class="text-muted mb-0">428015, Чувашская Республика,<br>г. Чебоксары, Московский проспект, 29, корпус 1</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-envelope-fill text-success fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">Email</h4>
                                <p class="mb-0">
                                    <a href="mailto:sovrem_human@rambler.ru" class="text-decoration-none">
                                        sovrem_human@rambler.ru
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- Телефон -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-telephone-fill text-info fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">Телефон</h4>
                                <p class="mb-0">
                                    <a href="tel:+78352450010" class="text-decoration-none">
                                        8 (8352) 45-00-10
                                    </a>
                                </p>
                                <small class="text-muted">Пн-Пт с 9:00 до 18:00</small>
                            </div>
                        </div>

                        <!-- Социальные сети -->
                        <div class="mt-5 pt-3">
                            <h4 class="h6 fw-bold mb-3">Мы в соцсетях</h4>
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-outline-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-telegram fs-5"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-vk fs-5"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-youtube fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 30px; overflow: hidden;">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="h4 fw-bold mb-4">
                            <i class="bi bi-send text-primary me-2"></i>Отправить сообщение
                        </h3>

                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ваше имя</label>
                                    <input type="text" class="form-control form-control-lg" placeholder="Иван Иванов" style="border-radius: 15px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control form-control-lg" placeholder="ivan@example.com" style="border-radius: 15px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Тема</label>
                                    <input type="text" class="form-control form-control-lg" placeholder="Вопрос о сотрудничестве" style="border-radius: 15px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Сообщение</label>
                                    <textarea class="form-control" rows="5" placeholder="Напишите ваше сообщение здесь..." style="border-radius: 15px;"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill">
                                        Отправить <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- КАРТА - ВСТАВЬТЕ СЮДА ВАШ КОД -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 30px; overflow: hidden;">
                    <!-- Вместо этого комментария вставьте ваш код из конструктора -->
                    <div style="position:relative;overflow:hidden; width:100%; height:400px;">
                        <a href="https://yandex.ru/maps/45/cheboksary/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Чебоксары</a>
                        <a href="https://yandex.ru/maps/45/cheboksary/house/moskovskiy_prospekt_29k1/YE4YdAZgSUIOQFtsfX11cn1mbA==/?from=mapframe&ll=47.210079%2C56.143158&source=mapframe&um=constructor%3A1234567890&utm_medium=mapframe&utm_source=maps&z=17" style="color:#eee;font-size:12px;position:absolute;top:14px;">Московский проспект, 29к1 — Яндекс Карты</a>
                        <iframe
                            src="https://yandex.ru/map-widget/v1/?from=mapframe&ll=47.210079%2C56.143158&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1NjAxMjI0NhKKAdCg0L7RgdGB0LjRjywg0KfRg9Cy0LDRiNGB0LrQsNGPINCg0LXRgdC_0YPQsdC70LjQutCwIOKAlCDQp9GD0LLQsNGI0LjRjywg0KfQtdCx0L7QutGB0LDRgNGLLCDQnNC-0YHQutC-0LLRgdC60LjQuSDQv9GA0L7RgdC_0LXQutGCLCAyOdC6MSIKDR_XPEIVmJJgQg%2C%2C&source=mapframe&um=constructor%3A1234567890&utm_source=mapframe&z=17"
                            width="100%"
                            height="400"
                            frameborder="0"
                            allowfullscreen="true"
                            style="position:relative; border:0;">
                        </iframe>
                    </div>
                    <div class="card-body bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> Московский проспект, 29, корпус 1, Чебоксары
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection