@extends('layouts.main')

@section('page.title', 'Регистрация')

@section('main.content')
<main class="signup-form">
        <div class="cotainer">
               <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card">
                        <h3 class="card-header text-center">Регистрация</h3>
                            <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <form action="{{ route('register.custom') }}" method="POST" autocomplete="on">
                                @csrf
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Имя" id="name" class="form-control" name="name"
                                        autocomplete="name" required autofocus>
                                    @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" placeholder="Email" id="email_address" class="form-control"
                                        name="email" autocomplete="email" required>
                                    @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <label for="organization" class="form-label">Организация</label>
                                    <select class="form-control" id="organization" name="organization" required>
                                        <option value="">Выберите организацию</option>
                                        @foreach(config('organizations.organizations') as $org)
                                            <option value="{{ $org }}" {{ old('organization') == $org ? 'selected' : '' }}>
                                                {{ $org }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('organization'))
                                    <span class="text-danger">{{ $errors->first('organization') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Пароль" id="password" class="form-control"
                                        name="password" autocomplete="new-password" required>
                                    @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="remember"> Запомнить меня</label>
                                    </div>
                                </div>
                                <div class="d-grid mx-auto">
                                    <button type="submit" class="btn btn-dark btn-block">Зарегистрироваться</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
