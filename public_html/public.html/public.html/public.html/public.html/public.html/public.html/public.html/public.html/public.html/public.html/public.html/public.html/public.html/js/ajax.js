$(document).ready(function() {
    // Ваш JavaScript для обработки AJAX-запросов и другие скрипты
    // ...

    $(document).on('click', '#pagination-links a', function(e) {
        e.preventDefault();

        var url = $(this).attr('href');

        $.ajax({
            url: url,
            success: function(response) {
                $('#search-results').html(response);
            },
            error: function() {
                alert('Ошибка при получении данных.');
            }
        });
    });

    // ...
});
