@extends('layouts.base')

@section('page.title', 'Добро пожаловать')

@section('content')

<div class="container">
  <div class="card custom-border mt-5">
      <div class="card-body">
          <h1>Добро пожаловать: {{$userName}} </h1>
      </div>
  </div>
</div>


@endsection