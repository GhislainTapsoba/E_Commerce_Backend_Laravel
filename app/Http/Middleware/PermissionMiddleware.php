<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: 'permission:orders.view'
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check() || !app('Illuminate\Contracts\Auth\Access\Gate')->check($permission)) {
            abort(403, 'Accès refusé. Permission requise : ' . $permission);
        }
        return $next($request);
    }
}