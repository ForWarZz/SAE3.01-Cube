<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'civilite' => ['required', 'string', 'in:M,F'],
            'nom_client' => ['required', 'string', 'max:255'],
            'prenom_client' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:client,email_client'],
            'naissance_client' => ['required', 'date'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $client = Client::create([
            'civilite' => $request->civilite === 'M' ? 'Monsieur' : 'Madame',
            'nom_client' => $request->nom_client,
            'prenom_client' => $request->prenom_client,
            'email_client' => $request->email,
            'naissance_client' => $request->naissance_client,
            'hash_mdp_client' => Hash::make($request->password),
        ]);

        event(new Registered($client));

        Auth::login($client);

        return redirect()->route('dashboard.index');
    }
}
