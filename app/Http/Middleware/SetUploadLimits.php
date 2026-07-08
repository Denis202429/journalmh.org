<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUploadLimits
{
    /**
     * Устанавливает лимиты загрузки файлов для PHP
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Устанавливаем лимиты для загрузки файлов
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '100M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');

        return $next($request);
    }
}