<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\Request;

class SitePageController extends Controller
{
    public function index()
    {
        $this->ensureDefaults();

        $pages = SitePage::query()->orderBy('title')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(SitePage $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, SitePage $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Страница обновлена');
    }

    private function ensureDefaults(): void
    {
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
            SitePage::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'content' => "Текст страницы \"{$title}\".",
                    'is_published' => true,
                ]
            );
        }
    }
}

