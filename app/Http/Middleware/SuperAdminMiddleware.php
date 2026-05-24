<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Solo el Super Admin o Admin pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
