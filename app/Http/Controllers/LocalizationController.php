<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class LocalizationController extends Controller
{
    //
    public function changeLocale(Request $request, $locale)
    {
        //dd($locale); 
        $request->validate([
            'locale' => 'required|in:ru,hu,en,tr,ch', // Проверка валидности выбранного языка
        ]);
       // dump($locale); 
        session(['locale' => $locale]); // Сохранение выбранного языка в сессии

        App::setLocale($locale); // Установка выбранного языка для текущего запроса

   


        return Redirect::back(); // Перенаправление назад на предыдущую страницу
    }
}
