<?php

namespace App\Http\Controllers;

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
}
