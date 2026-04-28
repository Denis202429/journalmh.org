
<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Sr No.</th>
            <th>Title</th>
            <th>Body</th>
            {{-- <th>Date</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach($parallel as $article)
        <tr>
            <td>{{$article->id}}</td>
            <td>{{$article->title_article}}</td>
            <td>{{$article->Autor}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div id="pagination">
    {{ $parallel->links() }}
</div>
