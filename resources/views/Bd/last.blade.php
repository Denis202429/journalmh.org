@extends('layouts.base')

@section('page.title', 'Последние добавления в базу')

@section('content')

<div class="container">
    <h1 class="mt-5 mb-3">Последние добавления в базу</h1>
    {{-- <ul>
        @foreach ($mainTableData as $row)
            <li>
                <strong>Created_at:</strong> {{ $row->created_at }}<br>
                <strong>Autor:</strong> {{ $row->Autor }}<br>
                <strong>Title:</strong> {{ $row->title_article }}<br>
                <strong>Year:</strong> {{ $row->year_publication }}<br>
                <strong>Place:</strong> {{ $row->place_publication }}<br>
                <strong>Category:</strong> {{ $row->category }}<br>
                <strong>Genre:</strong> {{ $row->genre }}<br>
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
                <th>Created_at</th>
                <th>Autor</th>
                <th>title_article</th>
                <th>year_publication</th>
                <th>place_publication</th>
                <th>category</th>
                <th>genre</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mainTableData as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->created_at }}</td>
                <td>{{ $row->Autor }}</td>
                <td>{{ $row->title_article }}</td>
                <td>{{ $row->year_publication }}</td>
                <td>{{ $row->place_publication }}</td>
                <td>{{ $row->category }}</td>
                <td>{{ $row->genre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 
    {{-- {{ $mainTableData->firstPageUrl() }} --}}
     {{ $mainTableData->links() }}  
     {{-- {{ $mainTableData->lastPageUrl() }} --}}
</div>

@endsection