@extends('layouts.base')

@section('page.title', 'Заполнение базы данных')

@section('content')

<div class="container">
  <div class="card custom-border mt-5">
      <div class="card-body">
          @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          @endif
          
          <h1>Всего в базе слов: 
              @if(session('totalWords'))
                  {{ number_format(session('totalWords'), 0, ',', ' ') }}
              @elseif(isset($totalWords))
                  {{ number_format($totalWords, 0, ',', ' ') }}
              @else
                  0
              @endif
          </h1>
          <h1>Всего в базе предложений: 
              @if(session('totalSentences'))
                  {{ number_format(session('totalSentences'), 0, ',', ' ') }}
              @elseif(isset($totalSentences))
                  {{ number_format($totalSentences, 0, ',', ' ') }}
              @else
                  0
              @endif
          </h1>
          
          @if (auth()->check() && auth()->user()->isSuperAdmin())
          <form action="{{ route('bd.update_admin') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary mt-3">{{ __('Обновить информацию о базе корпуса') }}</button>
          </form>
          @elseif (auth()->check())
          <div class="alert alert-info mt-3">
              <p>Только суперадминистратор может обновлять статистику базы данных.</p>
              <p class="small text-muted">Ваш статус: 
                @if(auth()->user()->isAdmin())
                  Администратор
                @elseif(auth()->user()->isCorrector())
                  Корректор
                @else
                  Пользователь
                @endif
                | super_admin в БД: {{ auth()->user()->super_admin ?? 'null' }}
              </p>
          </div>
          @endif
      </div>
  </div>
</div>


@endsection
