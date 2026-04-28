<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteDesignController extends Controller
{
    public function edit()
    {
        $theme = SiteSetting::query()
            ->where('key', 'theme')
            ->value('value') ?? 'classic';

        $themes = [
            'classic' => [
                'title' => 'Classic Journal',
                'description' => 'Текущий светлый академический стиль с теплыми цветами.',
            ],
            'midnight' => [
                'title' => 'Midnight Editorial',
                'description' => 'Тёмная контрастная тема для современного журнального оформления.',
            ],
            'paper' => [
                'title' => 'Paper Review',
                'description' => 'Светлая тема в стиле печатного журнала с бумажной фактурой и бордовыми акцентами.',
            ],
        ];

        return view('admin.design.edit', compact('theme', 'themes'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'theme' => ['required', 'in:classic,midnight,paper'],
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => $data['theme']]
        );

        return back()->with('success', 'Дизайн сайта обновлён');
    }
}

