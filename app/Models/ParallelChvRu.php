<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParallelChvRu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parallel_chv_ru';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'autor',
        'title_article',
        'year_creation',
        'chuvash_text',
        'russian_text',
        'year_publication',
        'place_publication',
        'genre',
        'category'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'year_creation' => 'integer',
        'year_publication' => 'integer',
    ];
}