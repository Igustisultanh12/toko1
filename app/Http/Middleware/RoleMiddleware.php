<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- PERBAIKAN DI SINI
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // Administrator memiliki akses ke seluruh fitur sistem (termasuk kasir POS)
        if (Auth::user()->role === 'admin' || in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        abort(403, 'UNAUTHORIZED ACTION.');
    }
}