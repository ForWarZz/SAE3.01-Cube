<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('client')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Refresh client data from database to ensure it's up to date
        $client = \App\Models\Client::find($request->session()->get('client')->id_client);
        
        if (!$client) {
            $request->session()->forget('client');
            return redirect()->route('login')->with('error', 'Session invalide. Veuillez vous reconnecter.');
        }
        
        // Update session with fresh client data
        $request->session()->put('client', $client);

        return $next($request);
    }
}
