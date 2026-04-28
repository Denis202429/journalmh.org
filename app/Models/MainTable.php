<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MainTable extends Model
{
    use HasFactory;
    
    protected $table = 'main_table';
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
        'totalSymbols',
        'totalWords',
        'totalSentences',
        'help1',
        'status',
        'corrector',
        'added_by',
        'cursor_position',
        'organization',
    ];
    
    protected $attributes = [
        'year_publication' => '',
        'place_publication' => '',
        'genre' => '',
        'category' => '',
        'status' => 0,
    ];

    /**
     * Scope для полнотекстового поиска по полю content
     */
    public function scopeFullTextSearch(Builder $query, string $searchTerm)
    {
        return $query->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
    }

    /**
     * Scope для поиска точной фразы
     */
    public function scopeExactPhraseSearch(Builder $query, string $phrase)
    {
        return $query->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', ['"' . $phrase . '"']);
    }

    /**
     * Scope для поиска с обязательным включением слова
     */
    public function scopeMustContain(Builder $query, string $word)
    {
        return $query->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', ['+' . $word]);
    }

    /**
     * Scope для поиска исключая определенные слова
     */
    public function scopeExcludeWords(Builder $query, array $excludeWords)
    {
        foreach ($excludeWords as $word) {
            $query->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', ['-' . $word]);
        }
        return $query;
    }

    /**
     * Scope для сложного поиска с несколькими условиями
     */
    public function scopeAdvancedSearch(Builder $query, array $mustWords = [], array $excludeWords = [])
    {
        $searchTerm = '';
        
        // Добавляем обязательные слова
        foreach ($mustWords as $word) {
            $searchTerm .= '+' . $word . ' ';
        }
        
        // Добавляем исключаемые слова
        foreach ($excludeWords as $word) {
            $searchTerm .= '-' . $word . ' ';
        }
        
        return $query->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', [trim($searchTerm)]);
    }
}