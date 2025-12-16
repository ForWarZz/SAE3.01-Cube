<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $client = Client::create([
            'civilite' => $validated['civilite'] === 'M' ? 'Monsieur' : 'Madame',
            'nom_client' => $validated['nom_client'],
            'prenom_client' => $validated['prenom_client'],
            'email_client' => $validated['email'],
            'naissance_client' => $validated['naissance_client'],
            'hash_mdp_client' => Hash::make($validated['password']),
        ]);

        event(new Registered($client));
        Auth::login($client);

        $client->date_der_connexion = now();
        $client->save();

        return redirect()->route('dashboard.index');
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
}
