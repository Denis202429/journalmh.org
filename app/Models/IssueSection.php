<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssueSection extends Model
{
    protected $table = 'issue_sections';
    
    protected $fillable = [
        'issue_id',
        'title_ru',
        'title_en',
        'title_cv',
        'sort_order',
    ];
    
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
    
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'section_id');
    }
    
    // Получение названия раздела на нужном языке
    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "title_{$locale}";
        return $this->$field ?? $this->title_ru ?? null;
    }
    
    public function getTitleRuAttribute(): ?string
    {
        return $this->title_ru;
    }
    
    public function getTitleEnAttribute(): ?string
    {
        return $this->title_en;
    }
    
    public function getTitleCvAttribute(): ?string
    {
        return $this->title_cv;
    }
}