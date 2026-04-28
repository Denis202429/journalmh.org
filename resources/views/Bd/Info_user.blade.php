@extends('layouts.base')

@section('page.title', 'Заполнение базы данных')

@section('content')

<div class="container">
  <div class="card custom-border mt-5">
      <div class="card-body">
          <h1>Всего в базе слов: @isset($totalWords){{ $totalWords }}@endisset</h1>
          <h1>Всего в базе предложений: @isset($totalSentences){{ $totalSentences }}@endisset</h1>
          
          {{-- <form action="{{ route('bd.update_admin') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary">{{ __('Обновить информацию о базе корпуса') }}</button>
          </form> --}}
      </div>
  </div>
</div>


@endsection
