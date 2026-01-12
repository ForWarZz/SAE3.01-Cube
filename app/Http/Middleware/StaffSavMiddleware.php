<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffSavMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('staff')->user();

        if (! $user || ! $user->isSAV()) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
