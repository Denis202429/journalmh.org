@extends('layouts.base')

@section('page.title', 'Обзор базы данных')

@section('content')

<div class="container">
    <h1 class="mt-5 mb-3">Обзор базы данных</h1>
    {{-- <ul>
        @foreach ($mainTableData as $row)
            <li>
                <strong>Autor:</strong> {{ $row->Autor }}<br>
                <strong>Title:</strong> {{ $row->title_article }}<br>
                <strong>Year:</strong> {{ $row->year_publication }}<br>
                <strong>Place:</strong> {{ $row->place_publication }}<br>
                <strong>Category:</strong> {{ $row->category }}<br>
                <strong>genre:</strong> {{ $row->genre }}<br>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

        @endforeach
    </ul> --}}



    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Sr No.</th>
                <th>Autor</th>
                <th>title_article</th>
                <th>year_publication</th>
                <th>place_publication</th>
                <th>category</th>
                <th>genre</th>
                <th>URL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mainTableData as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->Autor }}</td>
                <td>{{ $row->title_article }}</td>
                <td>{{ $row->year_publication }}</td>
                <td>{{ $row->place_publication }}</td>
                <td>{{ $row->category }}</td>
                <td>{{ $row->genre }}</td>
                <td>
                    <!-- Активная ссылка -->
                    <a href="{{ $row->url }}" target="_blank">{{ $row->url }}</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $mainTableData->links() }} {{-- Вывод пагинации --}}
</div>

@endsection