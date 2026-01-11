<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // If no roles specified, allow all authenticated users
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has one of the required roles
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // User doesn't have required role
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
