<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Глобальные middleware
        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
        ]);
        
        // Middleware алиасы
        $middleware->alias([
            'active' => \App\Http\Middleware\ActiveMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'chat.access' => \App\Http\Middleware\ChatAccessMiddleware::class,
            'token' => \App\Http\Middleware\TokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
