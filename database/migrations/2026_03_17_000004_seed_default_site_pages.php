<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $defaults = [
            'about-journal' => 'О журнале',
            'editorial-board' => 'Редакционная коллегия',
            'ethics' => 'Этика научных публикаций',
            'indexing' => 'Индексирование и реферирование',
            'policy' => 'Политика журнала',
            'public-offer' => 'Публичная оферта',
            'authors-guide' => 'Инструкция для авторов',
            'formatting-requirements' => 'Требования к оформлению',
            'peer-review' => 'Процесс рецензирования',
            'references-generator' => 'Генератор списка литературы',
            'contacts' => 'Контакты',
            'requisites' => 'Реквизиты',
        ];

        foreach ($defaults as $slug => $title) {
            $exists = DB::table('site_pages')->where('slug', $slug)->exists();
            if (!$exists) {
                DB::table('site_pages')->insert([
                    'slug' => $slug,
                    'title' => $title,
                    'content' => "Текст страницы \"{$title}\".",
                    'is_published' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('site_pages')->whereIn('slug', [
            'about-journal',
            'editorial-board',
            'ethics',
            'indexing',
            'policy',
            'public-offer',
            'authors-guide',
            'formatting-requirements',
            'peer-review',
            'references-generator',
            'contacts',
            'requisites',
        ])->delete();
    }
};

