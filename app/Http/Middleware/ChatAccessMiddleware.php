<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChatAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        return $next($request);
    }
}

