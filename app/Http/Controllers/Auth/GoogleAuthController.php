<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $client = Client::where('google_id', $googleUser->getId())->first();
        if (! $client) {
            $client = Client::where('email_client', $googleUser->getEmail())->first();
        }

        $isNewAccount = false;

        if ($client) {
            if (! $client->google_id) {
                $client->google_id = $googleUser->getId();
            }
        } else {
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $prenom = $nameParts[0] ?? $googleUser->getName();
            $nom = $nameParts[1] ?? '';

            $client = Client::create([
                'civilite' => 'Monsieur', // User will update in profile
                'prenom_client' => $prenom,
                'nom_client' => $nom,
                'email_client' => $googleUser->getEmail(),
                'hash_mdp_client' => null,
                'naissance_client' => now()->subYears(18)->format('Y-m-d'), // User will update in profile
                'google_id' => $googleUser->getId(),
            ]);

            $isNewAccount = true;
        }

        $client->date_der_connexion = now();
        $client->save();

        Auth::login($client);
        request()->session()->regenerate();

        if ($isNewAccount) {
            return redirect()->route('dashboard.profile.edit');
        }

        return redirect()->route('dashboard.index')
            ->with('success', 'Connexion réussie avec Google !');
    }
}
