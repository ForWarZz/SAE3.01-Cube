<?php

namespace App\Http\Controllers\Staff\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\StaffUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialAuthController extends Controller
{
    public function index()
    {
        return view('auth.commercial-login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->attributes->set('guard', 'staff');
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('commercial.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function viewStats()
    {
        $staffUser = Auth::guard('staff')->user();

        if ($staffUser->role != StaffUser::COMMERCIAL_DIRECTOR_ROLE) {
            abort(403, 'Accès non autorisé aux statistiques. Réservé aux directeurs commerciaux.');
        }

        return view('staff.commercial.stats');
    }
}
