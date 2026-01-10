<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffTechnicalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $staffUser = auth('staff')->user();

        if (! $staffUser->isTechnical()) {
            abort(403, 'Vous n\'avez pas les droits nécessaires pour accéder à cette section. Réservé au personnel technique.');
        }

        return $next($request);
    }
}
