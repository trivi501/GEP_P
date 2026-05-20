<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('Super Admin')) {
            abort(403, 'Solo el Super Admin puede acceder a esta sección.');
        }

        return $next($request);
    }
}
