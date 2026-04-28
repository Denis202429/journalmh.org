@extends('layouts.base')

@section('page.title', 'Морфологический анализ')

@section('content')

    {{-- <x-form action="{{ route('register.store') }}" method="POST"> --}}
    <form action="{{ route('Morph.Analysis') }}" method="POST">
        @csrf
        {{-- <div class="row mt-3"> --}}
        <div class="container">
            <h4 class="m-0 mt-5">
                {{ __('Морфологический анализ') }}
            </h4>

            <div class="row mt-3">
                <div class="col">
             
                    <span class="input-group-text">Введите слово для анализа</span>
                    <textarea name='Leksema' class="form-control" aria-label="With textarea"></textarea>
                </div>
            </div>


        <div class="mb-3">
            <label for="textarea1" class="form-label">{{ __('Результаты морфолигческого анализа') }}</label>
            <textarea class="form-control" id="textarea1" name="content" rows="10">{{$s1}}</textarea>
        </div>

        <button type="submit" class="btn btn-primary"> {{ __('Анализ') }}</button>

        </div>
    </div>

    </form>


@endsection
