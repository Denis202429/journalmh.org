<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleAuthor extends Model
{
    protected $table = 'article_authors';

    protected $fillable = [
        'article_id',
        'author_num',
        'author_id',
        'role',
        'is_correspondent',
        'researcherid',
        'spin',
        'scopusid',
        'orcid',
        // Фамилия
        'surname_ru',
        'surname_en',
        'surname_cv',
        // Имя
        'name_ru',
        'name_en',
        'name_cv',
        // Отчество
        'patronymic_ru',
        'patronymic_en',
        'patronymic_cv',
        // Инициалы
        'initials_ru',
        'initials_en',
        'initials_cv',
        // Адрес
        'address_ru',
        'address_en',
        // Город
        'town_ru',
        'town_en',
        'town_cv',
        // Страна
        'country_ru',
        'country_en',
        'country_cv',
        // Организация
        'org_name_ru',
        'org_name_en',
        'org_name_cv',
        // Должность
        'position_ru',
        'position_en',
        'position_cv',
        // Ученая степень и звание
        // 'degree',
        'degree_ru',
        'degree_en',
        'degree_cv',

        // 'rank',
        'rank_ru',
        'rank_en',
        'rank_cv',

        // Другое
        'other_info_ru',
        'other_info_en',
        'comment',
        'comment_date',
        'email',
        'email_not_authentic',
        'org_not_authentic',
    ];

    protected $casts = [
        'is_correspondent' => 'boolean',
        'org_not_authentic' => 'boolean',
        'email_not_authentic' => 'boolean',
        'comment_date' => 'date',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    // Полное ФИО на русском
    public function getFullNameRuAttribute(): string
    {
        $parts = array_filter([
            $this->surname_ru,
            $this->name_ru,
            $this->patronymic_ru,
        ]);
        return implode(' ', $parts);
    }

    // Полное ФИО на английском
    public function getFullNameEnAttribute(): string
    {
        $parts = array_filter([
            $this->surname_en,
            $this->name_en,
        ]);
        return implode(' ', $parts);
    }

    // Полное ФИО на чувашском
    public function getFullNameCvAttribute(): string
    {
        $parts = array_filter([
            $this->surname_cv,
            $this->name_cv,
        ]);
        return implode(' ', $parts);
    }

    // Получение ФИО на нужном языке
    public function getFullNameAttribute(): string
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'cv':
                return $this->full_name_cv ?: $this->full_name_ru;
            case 'en':
                return $this->full_name_en ?: $this->full_name_ru;
            default:
                return $this->full_name_ru;
        }
    }

    // Получение организации на нужном языке
    public function getOrganizationAttribute(): ?string
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'cv':
                return $this->org_name_cv ?? $this->org_name_ru;
            case 'en':
                return $this->org_name_en ?? $this->org_name_ru;
            default:
                return $this->org_name_ru;
        }
    }

    // Получение города на нужном языке
    public function getTownAttribute(): ?string
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'cv':
                return $this->town_cv ?? $this->town_ru;
            case 'en':
                return $this->town_en ?? $this->town_ru;
            default:
                return $this->town_ru;
        }
    }

    // Получение страны на нужном языке
    public function getCountryAttribute(): ?string
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'cv':
                return $this->country_cv ?? $this->country_ru;
            case 'en':
                return $this->country_en ?? $this->country_ru;
            default:
                return $this->country_ru;
        }
    }

    // Получение должности на нужном языке
    public function getPositionAttribute(): ?string
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'cv':
                return $this->position_cv ?? $this->position_ru;
            case 'en':
                return $this->position_en ?? $this->position_ru;
            default:
                return $this->position_ru;
        }
    }

    // Инициалы с фамилией (рус)
    public function getInitialsFullRuAttribute(): string
    {
        $initials = '';
        if ($this->name_ru) {
            $initials .= mb_substr($this->name_ru, 0, 1) . '.';
        }
        if ($this->patronymic_ru) {
            $initials .= mb_substr($this->patronymic_ru, 0, 1) . '.';
        }
        return trim($this->surname_ru . ' ' . $initials);
    }


    // Ученая степень на нужном языке
    public function getDegreeAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "degree_{$locale}";
        return $this->$field ?? $this->degree_ru ?? null;
    }

    // Ученое звание на нужном языке
    public function getRankAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "rank_{$locale}";
        return $this->$field ?? $this->rank_ru ?? null;
    }

    // Адрес на нужном языке
    public function getAddressAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "address_{$locale}";
        return $this->$field ?? $this->address_ru ?? null;
    }

    // Другие сведения на нужном языке
    public function getOtherInfoAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "other_info_{$locale}";
        return $this->$field ?? $this->other_info_ru ?? null;
    }
}
