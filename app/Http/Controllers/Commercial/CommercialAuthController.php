<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Commercial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.commercial-login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->attributes->set('guard', 'commercial');
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('commercial.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('commercial')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function viewReferences()
    {
        return view('commercial.references');
    }

    public function viewStats()
    {
        $commercial = Auth::guard('commercial')->user();

        if ($commercial->role != Commercial::DIRECTOR_ROLE) {
            abort(403, 'Accès non autorisé aux statistiques. Réservé aux directeurs commerciaux.');
        }

        return view('commercial.stats');
    }
}
