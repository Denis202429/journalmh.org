<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class MainTable1 extends Model
{
    use HasFactory;
    // use Searchable;
    protected $table = 'main_table_copy1';
    protected $fillable = [
        'Autor',
        'title_article',
        'year_creation',
        'content',
        'year_publication',
        'place_publication',
        'genre',
        'category',
        'url',
        'help_url',
        'tags',
        'page',
        'totalWords',
        'totalSentences',
        'help1',
    ];
    

    // Значения по умолчанию
    protected $attributes = [
        'year_publication' => '',
        'place_publication' => '',
        'genre' => '',
        'category' => '',
    ];
//  public function toSearchableArray()
// {
//   $array = $this->toArray();
    
//   return array('content' => $array['content']);
// }
}


// Мне надо разработать консольную команду на Ларавел, которая делает статистику по всем символам которые находятся в поле 
// content двух таблиц модели которых приведены ниже. Надо сделать статистику по всем записям базы данных и потом
//  вывести сумарный результат в процентах. Например символ 'a' встречается столько то раз и сколько это в процентах от общего количества символов.
// Вот модели двух таблиц:

// class MainTable1 extends Model
// {
//     use HasFactory;
//     // use Searchable;
//     protected $table = 'main_table_copy1';
//     protected $fillable = [
//         'Autor',
//         'title_article',
//         'year_creation',
//         'content',
//         'year_publication',
//         'place_publication',
//         'genre',
//         'category',
//         'url',
//         'help_url',
//         'tags',
//         'page',
//         'totalWords',
//         'totalSentences',
//         'help1',
//     ];
    

//     // Значения по умолчанию
//     protected $attributes = [
//         'year_publication' => '',
//         'place_publication' => '',
//         'genre' => '',
//         'category' => '',
//     ];
// }


// class MainTable extends Model
// {
//     use HasFactory;
//     // use Searchable;
//     protected $table = 'main_table';
//     protected $fillable = [
//         'Autor',
//         'title_article',
//         'year_creation',
//         'content',
//         'year_publication',
//         'place_publication',
//         'genre',
//         'category',
//         'url',
//         'help_url',
//         'tags',
//         'page',
//         'totalSymbols',
//         'totalWords',
//         'totalSentences',
//         'help1',
//         'status',         // Новый статус (0 или 1)
//         'corrector',      // ФИО корректоров
//         'added_by',       // ФИО, кто добавил текст
//         'cursor_position',
//     ];
    

//     // Значения по умолчанию
//     protected $attributes = [
//         'year_publication' => '',
//         'place_publication' => '',
//         'genre' => '',
//         'category' => '',
//         'status' => 0,  // По умолчанию статус = 0 (неактивен)
//     ];

// }