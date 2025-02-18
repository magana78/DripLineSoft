<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check() || Auth::user()->rol !== $role) {
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
