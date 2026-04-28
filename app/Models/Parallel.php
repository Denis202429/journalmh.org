<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parallel extends Model
{
    use HasFactory;
    protected $table = 'parallel';
    protected $fillable = [
        'Autor',
        'title_article',
        'year_creation',
        'content',
        'translate',
        'year_publication',
        'place_publication',
        'genre',
        'category',
    ];

    // Значения по умолчанию
    protected $attributes = [
        'year_publication' => '', // Значение по умолчанию для year_publication
        'place_publication' => '', // Значение по умолчанию для place_publication
        'genre' => '', // Значение по умолчанию для genre
        'category' => '', // Значение по умолчанию для category
    ];



}
