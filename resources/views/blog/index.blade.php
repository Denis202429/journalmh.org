{{-- @extends('layouts.main')

@section('page.title', 'Обзор базы данных')

@section('main.content')
    <x-title>
        {{ __('База данных корпуса') }}
    </x-title>

    @include('blog.filter')

    @if($posts->isEmpty())
        {{ __('Нет ни одной записи') }}
    @else
        <div class="row">
            @foreach($posts as $post)
                <div class="col-12 col-md-4">
                    <x-post.card :post="$post" />
                </div>
            @endforeach
        </div>
         {{ $posts->appends(request()->except('page'))->onEachSide(1)->links() }}
   



    @endif
@endsection --}}
