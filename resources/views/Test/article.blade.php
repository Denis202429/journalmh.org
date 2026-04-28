{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Readerstacks laravel 8 ajax pagination</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
</head> --}}

@extends('layouts.base')

@section('page.title', 'Поиск в параллельном корпусе ')

@section('content')

<body class="antialiased">
    <div class="container">
        <!-- main app container -->
        <div class="readersack">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <h3>Laravel 8 ajax pagination</h3>
                        <div id="pagination_data">
                            @include('Test.article-pagination', ['parallel' => $parallel])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
       
       <script>
            $(function() {
                $(document).on("click", "#pagination a,#search_btn", function() {

                    //get url and make final url for ajax 
                    var url = $(this).attr("href");
                    var append = url.indexOf("?") == -1 ? "?" : "&";
                    var finalURL = url + append + $("#searchform").serialize();

                    //set to current url
                    window.history.pushState({}, null, finalURL);

                    $.get(finalURL, function(data) {

                        $("#pagination_data").html(data);

                    });

                    return false;
                })

            });
        </script>
</body>

</html>
@endsection