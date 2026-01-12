<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WarehouseAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Check if warehouse_id is in the Request (Input or Route)
        $warehouseId = $request->input('warehouse_id') ?? $request->route('warehouse');

        // 2. If no warehouse context is present, just proceed 
        // (Controller will handle default filtering or scoping)
        if (!$warehouseId) {
            return $next($request);
        }

        // 3. User Helper Check
        if ($user && !$user->hasWarehouseAccess($warehouseId)) {
            abort(403, 'Anda tidak memiliki akses ke gudang ini.');
        }

        return $next($request);
    }
}
