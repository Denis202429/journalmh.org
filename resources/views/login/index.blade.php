{{-- @extends('layouts.auth')

@section('page.title', 'Страница входа')

@section('auth.content')
    <x-login.card />
@endsection --}}

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход</title>
    <link href="css/app.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <form method="POST" action="login" style="text-align: center; margin: auto;">
                @csrf
                <div>
                    Email
                    <input style="margin: 10px;" type="email" name="email" value="{{ old('email') }}">
                </div>

                <div>
                    Пароль
                    <input style="margin: 10px;" type="password" name="password" id="password">
                </div>

                <div>
                    <input style="margin: 10px;" type="checkbox" name="remember"> Запомнить меня
                </div>

                <div>
                    <button style="margin: 10px;" type="submit">Вход</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/app.js"></script>
</body>

</html> --}}
