<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !(method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}