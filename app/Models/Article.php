<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'issue_id',
        'section_id',
        'pages',
        'art_type',
        'lang_publ',
        // Русский
        'title_ru',
        'abstract_ru',
        'keywords_ru',
        // Английский
        'title_en',
        'abstract_en',
        'keywords_en',
        // Чувашский
        'title_cv',
        'abstract_cv',
        'keywords_cv',

        'text_ru',        // Добавьте
        'text_en',        // Добавьте
        'text_cv',        // Добавьте
        // Идентификаторы
        'doi',
        'edn',
        'udk',
        'bbk',
        'vak',
        'vak21',
        'jel',
        'msc',
        'pacs',
        'anycode',
        'rubrics',
        'fundings',
        'references',
        // Даты
        'date_received',
        'date_accepted',
        'date_publication',
        'published_at',
        'pdf_url',
        'additional_files',
        'is_published',
        'sort_order',
        // загружаемый файл
        'pdf_url',
        'pdf_file_path',
        'pdf_original_name',
        'pdf_file_size',

    ];

    protected $casts = [
        'udk' => 'array',
        'bbk' => 'array',
        'jel' => 'array',
        'msc' => 'array',
        'pacs' => 'array',
        'anycode' => 'array',
        'rubrics' => 'array',
        'fundings' => 'array',
        'references' => 'array',
        'additional_files' => 'array',
        'date_received' => 'date',
        'date_accepted' => 'date',
        'date_publication' => 'date',
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(IssueSection::class, 'section_id');
    }

    public function authors(): HasMany
    {
        return $this->hasMany(ArticleAuthor::class, 'article_id')->orderBy('author_num');
        // return $this->hasMany(ArticleAuthor::class, 'article_id')->orderBy('author_num');
    }

    // Получение названия на нужном языке
    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "title_{$locale}";
        return $this->$field ?? $this->title_ru ?? $this->title_en ?? null;
    }

    // Получение аннотации на нужном языке
    public function getAbstractAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "abstract_{$locale}";
        return $this->$field ?? $this->abstract_ru ?? $this->abstract_en ?? null;
    }

    // Получение ключевых слов на нужном языке
    public function getKeywordsAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "keywords_{$locale}";
        return $this->$field ?? $this->keywords_ru ?? $this->keywords_en ?? null;
    }

    // Добавьте аксессор для получения URL PDF
    // public function getPdfUrlAttribute($value)
    // {
    //     // Если есть загруженный файл, возвращаем путь к нему
    //     if ($this->pdf_file_path) {
    //         return asset('storage/' . $this->pdf_file_path);
    //     }
    //     // Иначе возвращаем внешнюю ссылку
    //     return $value;
    // }


    public function getPdfUrlAttribute($value)
    {
        // Если есть загруженный файл, возвращаем путь к нему
        if ($this->pdf_file_path && !$this->use_external_url) {
            return asset('storage/' . $this->pdf_file_path);
        }
        // Иначе возвращаем внешнюю ссылку
        return $value;
    }


    // Проверяем, есть ли PDF файл
    public function hasPdfFile()
    {
        return !empty($this->pdf_file_path) && file_exists(storage_path('app/public/' . $this->pdf_file_path));
    }
}
