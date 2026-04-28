<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="language" content="ru">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('page.description', 'Современная гуманитаристика — научный журнал')">
    <meta name="keywords" content="@yield('page.keywords', 'журнал, гуманитаристика, научные статьи')">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('page.title', config('app.name'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    @stack('css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        :root {
            --primary: #2c5048;
            --primary-dark: #1d3530;
            --primary-light: #3e6b61;
            --primary-soft: #e5f0ed;
            --accent: #843432;
            --accent-dark: #bf6e40;
            --accent-light: #e8aa85;
            --accent-soft: #feefe6;
            --text: #1e2b2a;
            --text-light: #3f534f;
            --text-soft: #6f847f;
            --bg: #ffffff;
            --bg-light: #fad399;
            --bg-soft: #f9f3ec;
            --border: #e0d6cc;
            --border-light: #ece3db;
            --shadow-sm: 0 4px 12px rgba(44, 80, 72, 0.08);
            --shadow: 0 8px 24px rgba(44, 80, 72, 0.12);
            --shadow-md: 0 16px 32px rgba(44, 80, 72, 0.16);
            --shadow-lg: 0 24px 48px rgba(44, 80, 72, 0.2);
            --shadow-accent: 0 4px 16px rgba(217, 140, 95, 0.25);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            /* font-family: 'Quicksand', sans-serif; */
            color: var(--text);
            background-color: var(--bg);
            line-height: 1.7;
            font-weight: 400;
            font-size: 1rem;
        }

        body.theme-midnight {
            --primary: #9ccfd8;
            --primary-dark: #070d18;
            --primary-light: #67e8f9;
            --primary-soft: #10253a;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --accent-light: #fbbf24;
            --accent-soft: #2a1d06;
            --text: #e5eef7;
            --text-light: #cbd5e1;
            --text-soft: #94a3b8;
            --bg: #0b1220;
            --bg-light: #131c31;
            --bg-soft: #0f172a;
            --border: #334155;
            --border-light: #41536f;
            --shadow-sm: 0 4px 12px rgba(2, 6, 23, 0.35);
            --shadow: 0 8px 24px rgba(2, 6, 23, 0.45);
            --shadow-md: 0 16px 32px rgba(2, 6, 23, 0.5);
            --shadow-lg: 0 24px 48px rgba(2, 6, 23, 0.6);
            --shadow-accent: 0 4px 16px rgba(245, 158, 11, 0.25);
            background:
                radial-gradient(circle at top right, rgba(103, 232, 249, 0.08), transparent 25%),
                linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        }

        body.theme-paper {
            --primary: #6e2c2c;
            --primary-dark: #4a1d1d;
            --primary-light: #8a4b4b;
            --primary-soft: #f3e7d7;
            --accent: #b8893f;
            --accent-dark: #9b6d28;
            --accent-light: #d4aa63;
            --accent-soft: #f4ead6;
            --text: #2d2118;
            --text-light: #5b4737;
            --text-soft: #7b6756;
            --bg: #f8f3ea;
            --bg-light: #efe3c4;
            --bg-soft: #fbf7ef;
            --border: #d6c3a4;
            --border-light: #e5d7be;
            --shadow-sm: 0 4px 12px rgba(86, 57, 26, 0.08);
            --shadow: 0 8px 24px rgba(86, 57, 26, 0.12);
            --shadow-md: 0 16px 32px rgba(86, 57, 26, 0.16);
            --shadow-lg: 0 24px 48px rgba(86, 57, 26, 0.18);
            --shadow-accent: 0 4px 16px rgba(184, 137, 63, 0.2);
            background:
                radial-gradient(circle at top left, rgba(184, 137, 63, 0.08), transparent 22%),
                linear-gradient(180deg, #fbf7ef 0%, #f8f3ea 100%);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            /* font-family: 'Playfair Display', serif; */
            font-weight: 600;
            color: var(--primary);
            letter-spacing: -0.02em;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
            padding: 1rem 0;
        }

        .theme-midnight .navbar {
            background: rgba(11, 18, 32, 0.92);
        }

        .theme-paper .navbar {
            background: rgba(248, 243, 234, 0.95);
            border-bottom: 1px solid #decdb0;
            box-shadow: 0 6px 20px rgba(86, 57, 26, 0.05);
        }

        .theme-midnight .navbar-toggler {
            border-color: var(--border-light);
        }

        .theme-midnight .navbar-toggler-icon {
            filter: invert(1) brightness(2);
        }

        .navbar-brand {
            /* font-family: 'Playfair Display', serif; */
            font-size: 1.8rem;
            font-weight: 700;
            /* color: var(--primary) !important; */
            color: var(--accent);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .navbar-brand span {
            /* font-family: 'Quicksand', sans-serif; */
            font-size: 0.7rem;
            color: var(--accent);
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: block;
            margin-top: 0.2rem;
        }

        .nav-link {
            /* color: var(--text-light) !important; */

            color: var(--accent);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1.2rem !important;
            margin: 0 0.1rem;
            transition: var(--transition);
            position: relative;
            letter-spacing: 0.05em;
        }

        .nav-link:hover {
            color: var(--accent) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.2rem;
            right: 1.2rem;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), var(--accent-light), var(--accent), transparent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::after {
            transform: scaleX(1);
        }

        .dropdown-menu {
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
        }

        .theme-midnight .dropdown-menu {
            background: #131c31;
            border-color: var(--border-light);
        }

        .theme-paper .dropdown-menu {
            background: #fbf7ef;
            border-color: #decdb0;
        }

        .theme-midnight .dropdown-item {
            color: var(--text-light);
        }

        .theme-midnight .dropdown-item:hover,
        .theme-midnight .dropdown-item:focus {
            background: rgba(156, 207, 216, 0.12);
            color: var(--text);
        }

        .theme-paper .dropdown-item:hover,
        .theme-paper .dropdown-item:focus {
            background: rgba(184, 137, 63, 0.12);
            color: var(--primary);
        }

        .section {
            padding: 70px 0;
            position: relative;
        }

        .footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: rgba(255, 255, 255, 0.8);
            padding: 50px 0 30px;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 4" preserveAspectRatio="none"><path d="M0,2 Q300,0 600,2 T1200,2" stroke="%23d98c5f" fill="none" stroke-width="2"/></svg>');
            background-size: cover;
            opacity: 0.4;
        }

        .footer h5 {
            color: white;
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
            font-weight: 700;
            /* font-family: 'Playfair Display', serif; */
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .footer a:hover {
            color: var(--accent-light);
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.6rem;
        }

        .footer-links i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
            color: var(--accent-light);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.3rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--accent-light);
            transform: translateY(-2px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 2px solid rgba(255, 255, 255, 0.1);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.1em;
            font-weight: 500;
        }

        .theme-midnight .card,
        .theme-midnight .table,
        .theme-midnight .table th,
        .theme-midnight .table td,
        .theme-midnight .form-control,
        .theme-midnight .form-select,
        .theme-midnight .alert,
        .theme-midnight .input-group-text {
            background-color: #131c31;
            color: var(--text-light);
            border-color: var(--border);
        }

        .theme-paper .card,
        .theme-paper .table,
        .theme-paper .table th,
        .theme-paper .table td,
        .theme-paper .form-control,
        .theme-paper .form-select,
        .theme-paper .alert,
        .theme-paper .input-group-text {
            background-color: #fbf7ef;
            color: var(--text-light);
            border-color: var(--border);
        }

        .theme-midnight .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: rgba(255, 255, 255, 0.02);
            color: var(--text-light);
        }

        .theme-midnight .text-muted {
            color: var(--text-soft) !important;
        }

        .theme-paper .text-muted {
            color: var(--text-soft) !important;
        }

        .theme-midnight .btn-outline-secondary,
        .theme-midnight .btn-outline-primary {
            color: var(--text-light);
            border-color: var(--border-light);
        }

        .theme-midnight .btn-outline-secondary:hover,
        .theme-midnight .btn-outline-primary:hover {
            color: #0b1220;
            background: var(--primary);
            border-color: var(--primary);
        }

        .theme-paper .btn-outline-secondary,
        .theme-paper .btn-outline-primary {
            color: var(--primary);
            border-color: var(--border);
        }

        .theme-paper .btn-outline-secondary:hover,
        .theme-paper .btn-outline-primary:hover {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
        }

        .text-justify {
            text-align: justify !important;
        }

        .hover-shadow {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .15) !important;
        }


        /* === ГАМБУРГЕР: гарантированная видимость === */
        .navbar-toggler {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(0,0,0,0.85)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
            width: 24px;
            height: 24px;
            opacity: 1 !important;
        }

        /* Для тёмной темы */
        .theme-midnight .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        .theme-midnight .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* === Компенсация fixed-top === */
        body {
            padding-top: 72px;
        }

        /* === Кнопки языка в выпадающем меню === */
        .dropdown-menu .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* === Адаптивный шрифт бренда (если нужно точнее) === */
        .navbar-brand .fs-6 {
            font-size: 1rem;
        }

        .navbar-brand .fs-md-5 {
            font-size: 1.25rem;
        }

        .navbar-brand .fs-lg-4 {
            font-size: 1.5rem;
        }

        .navbar-brand .fs-xl-3 {
            font-size: 1.75rem;
        }

        .transition {
            transition: var(--transition);
        }



        /* Дополнительные стили для анимаций и CSS переменных */
        :root {
            --bg: #ffffff;
            --border: #e0e0e0;
            --text-soft: #6c757d;
        }

        [data-bs-theme="dark"] {
            --bg: #2b2b2b;
            --border: #404040;
            --text-soft: #adb5bd;
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: var(--bg);
            border: 1px solid var(--border) !important;
        }

        .card .text-muted {
            color: var(--text-soft) !important;
        }

        .btn-outline-primary:hover i,
        .btn-outline-success:hover i,
        .btn-outline-info:hover i {
            transform: translateX(4px);
            transition: transform 0.2s ease;
        }

        .btn i {
            transition: transform 0.2s ease;
        }


        /* Дополнительные стили */
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .btn-outline-primary:hover,
        .btn-outline-info:hover,
        .btn-outline-danger:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-3px);
            transition: transform 0.2s ease;
        }

        .btn-outline-primary,
        .btn-outline-info,
        .btn-outline-danger,
        .btn-outline-secondary {
            transition: transform 0.2s ease;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
        }

        a {
            transition: color 0.2s ease;
        }

        a:hover {
            color: #0b5ed7 !important;
        }


        .article-text {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .article-text p {
            margin-bottom: 0.75rem;
        }
    </style>


</head>

<body class="theme-{{ $currentTheme ?? 'classic' }}">
    <div class="d-flex flex-column justify-content-between min-vh-100">
        @include('includes.header')

        <main class="flex-grow-1" style="padding-top: 92px;">

            @if (session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        @include('includes.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>

</html>