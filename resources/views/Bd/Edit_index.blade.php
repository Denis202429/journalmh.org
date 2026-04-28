@extends('layouts.base')
@section('page.title', 'Удаление из базы данных')
@section('content')
<div class="container">
    <h1 class="mt-5"> Удаление и редактирование базы данных</h1>
    <!-- {{-- <ul>
        @foreach ($ParallelData as $row)
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
    </ul> --}} -->
 <div class="table-responsive">
    <table class="table table-striped table-bordered mt-5">
        <thead class="table-dark">
            <tr>
                <th>Название</th>
                <th>Autor</th>
                <th>Год</th>
                <th>Количество символов</th>
                <!-- <th>Колитчество слов</th> -->
                <!-- <th>Количество предложений</th> -->
                <th>Добавил</th>
                <th>Корректор</th>
                <th>Статус</th>
                <th>Редактировать</th>
            </tr>
        </thead>
        <tbody>
            @foreach($MainTable_Data as $row)
            <tr>
                <td>{{ $row->title_article }}</td>
                <td>{{ $row->Autor }}</td>
                <td>{{ $row->year_publication }}</td>

                <td>{{ $row->totalSymbols }}</td>
                <!-- <td>{{ $row->totalWords }}</td> -->
                <!-- <td>{{ $row->totalSentences }}</td> -->
            

                <td>{{ $row->added_by }}</td>
                <td>{{ $row->corrector }}</td>

                <td>
                    @if ($row->status)
                        <span class="text-success">
                            <i class="bi bi-check-circle-fill"></i> <!-- Зеленая галочка -->
                        </span>
                    @else
                        <span class="text-danger">
                            <i class="bi bi-x-circle-fill"></i> <!-- Красный крестик -->
                        </span>
                    @endif
                </td>

                <td>
                    @if ((auth()->check() && auth()->user()->isSuperAdmin()) || (auth()->check() && $row->corrector === auth()->user()->name) || (empty($row->corrector) && auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())))
                        <a href="{{ route('admin.BD_edit', $row->id) }}" class="btn btn-primary">Редактировать</a>
                    @else
                        <span class="text-muted">Заблокировано</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $MainTable_Data->links() }} {{-- Вывод пагинации --}}
 </div>
</div>
@endsection

