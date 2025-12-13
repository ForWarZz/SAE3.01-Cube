<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.commercial-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'email_commercial' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('commercial')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('commercial.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ]);
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
