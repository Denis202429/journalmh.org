<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Http\Request;

// class AdminMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  \Closure  $next
//      * @return mixed
//      */
//     public function handle(Request $request, Closure $next)
//     {
       
//         if(auth()->check() && auth()->user()->isAdmin()) {
//             return $next($request);
//         }
    
//         abort(403, 'Unauthorized'); // Или перенаправьте на другую страницу

//     }

//     // protected function isAdmin(Request $request)
//     // {
//     //     return false;
//     // }
// }


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Доступ: админ или суперадмин
        if (
            !$user ||
            !(
                (method_exists($user, 'isAdmin') && $user->isAdmin()) ||
                (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())
            )
        ) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}