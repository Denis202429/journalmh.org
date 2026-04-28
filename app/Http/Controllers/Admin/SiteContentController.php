<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function edit()
    {
        $defaults = $this->defaults();

        foreach ($defaults as $key => $item) {
            SiteContent::firstOrCreate(
                ['key' => $key],
                ['title' => $item['title'], 'content' => $item['content']]
            );
        }

        $contents = SiteContent::query()
            ->whereIn('key', array_keys($defaults))
            ->get()
            ->keyBy('key');

        return view('admin.content.edit', compact('defaults', 'contents'));
    }

    public function update(Request $request)
    {
        $defaults = $this->defaults();
        $rules = [];

        foreach (array_keys($defaults) as $key) {
            $rules[$key] = ['nullable', 'string'];
        }

        $data = $request->validate($rules);

        foreach ($defaults as $key => $item) {
            SiteContent::updateOrCreate(
                ['key' => $key],
                [
                    'title' => $item['title'],
                    'content' => $data[$key] ?? '',
                ]
            );
        }

        return back()->with('success', 'Разделы сайта обновлены');
    }

    private function defaults(): array
    {
        return [
            'header_journal_menu' => [
                'title' => 'Header: меню "Журнал"',
                'content' => implode(PHP_EOL, [
                    'О журнале|#about',
                    'Архив номеров|#archive',
                    'Редакционная коллегия|#editors',
                    'Этика научных публикаций|#about',
                    'Индексирование и реферирование|#about',
                    'Политика журнала|#about',
                    'Публичная оферта|#contacts',
                ]),
            ],
            'header_authors_menu' => [
                'title' => 'Header: меню "Для авторов"',
                'content' => implode(PHP_EOL, [
                    'Инструкция для авторов|#for-authors',
                    'Требования к оформлению|#for-authors',
                    'Процесс рецензирования|#for-authors',
                    'Генератор списка литературы|#for-authors',
                ]),
            ],
            'home_authors_section_title' => [
                'title' => 'Главная: заголовок секции "Авторам"',
                'content' => 'Авторам',
            ],
            'home_authors_card1_title' => [
                'title' => 'Главная: карточка 1, заголовок',
                'content' => 'Требования к материалам',
            ],
            'home_authors_card1_text' => [
                'title' => 'Главная: карточка 1, текст',
                'content' => 'Оформление, структура, библиография, этика публикаций и порядок рассмотрения.',
            ],
            'home_authors_card2_title' => [
                'title' => 'Главная: карточка 2, заголовок',
                'content' => 'Подача статьи',
            ],
            'home_authors_card2_text' => [
                'title' => 'Главная: карточка 2, текст',
                'content' => 'Контакты редакции и порядок подачи материалов (можем привязать к вашей форме/почте).',
            ],
        ];
    }
}

