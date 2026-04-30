<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $table = 'issues';

    protected $fillable = [
        'issn',
        'eissn',
        'volume',
        'number',
        'alt_number',
        'part',
        'year',
        'month',
        'issue_pages',
        'issue_type',
        'title',
        'title_en',
        'doi',
        'issue_doi',
        'edn',
        'published_at',
        'pdf_url',
        'issue_files',
        'publisher',
        'description',
        'description_en',
        'is_published',
        'sort_order',
        'pdf_url',
        'pdf_file_path',
        'pdf_original_name',
        'cover_image',      // внешняя ссылка на обложку
        'cover_image_path', // путь к загруженному файлу
        'cover_original_name', // оригинальное имя файла

    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
        'year' => 'integer',
        'number' => 'integer',
        'sort_order' => 'integer',
        'part' => 'integer',
        'issue_files' => 'array',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(IssueSection::class)->orderBy('sort_order');
    }

    public function getFullTitleAttribute(): string
    {
        $parts = [];
        if ($this->volume) $parts[] = "Том {$this->volume}";
        if ($this->number) $parts[] = "№ {$this->number}";
        if ($this->year) $parts[] = $this->year;
        return implode(' • ', $parts);
    }

    public function getIssueTypeLabelAttribute(): string
    {
        $labels = ['ISS' => 'Обычный выпуск', 'OFI' => 'Online First', 'SPI' => 'Специальный выпуск'];
        return $labels[$this->issue_type] ?? 'Обычный выпуск';
    }

    // Добавьте аксессор для получения URL PDF
    public function getPdfUrlAttribute($value)
    {
        if ($this->pdf_file_path) {
            return asset('storage/' . $this->pdf_file_path);
        }
        return $value;
    }

    // Проверяем, есть ли PDF файл
    public function hasPdfFile()
    {
        return !empty($this->pdf_file_path) && file_exists(storage_path('app/public/' . $this->pdf_file_path));
    }

    // Добавьте аксессор для получения URL обложки
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image_path) {
            return asset('storage/' . $this->cover_image_path);
        }
        return $this->cover_image;
    }

    // Проверяем, есть ли загруженная обложка
    public function hasCoverImage()
    {
        return !empty($this->cover_image_path) && file_exists(storage_path('app/public/' . $this->cover_image_path));
    }

    // Добавьте этот метод в модель Issue
    public function getCoverUrlAttribute()
    {
        if ($this->cover_image_path && file_exists(storage_path('app/public/' . $this->cover_image_path))) {
            return asset('storage/' . $this->cover_image_path);
        }
        if ($this->cover_image) {
            return $this->cover_image;
        }
        return null;
    }

    // Проверяет, есть ли обложка
    public function hasCover()
    {
        return !is_null($this->getCoverUrlAttribute());
    }
}
