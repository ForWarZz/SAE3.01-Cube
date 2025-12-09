<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists by email or Google ID
            $client = Client::where('email_client', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            $isNewAccount = false;

            if ($client) {
                // Update Google ID if it wasn't set before
                if (!$client->google_id) {
                    $client->google_id = $googleUser->getId();
                    $client->save();
                }
            } else {
                // Create new user - Google no longer provides birthday/gender without special approval
                // User will need to complete their profile after first login
                $nameParts = explode(' ', $googleUser->getName(), 2);
                $prenom = $nameParts[0] ?? $googleUser->getName();
                $nom = $nameParts[1] ?? '';

                $client = Client::create([
                    'civilite' => 'Monsieur', // User will update in profile
                    'prenom_client' => $prenom,
                    'nom_client' => $nom,
                    'email_client' => $googleUser->getEmail(),
                    'hash_mdp_client' => Hash::make(uniqid()),
                    'naissance_client' => now()->subYears(18)->format('Y-m-d'), // User will update in profile
                    'google_id' => $googleUser->getId(),
                ]);
                $isNewAccount = true;
            }

            // Log the user in
            Auth::login($client); // Login without remember me
            
            // Regenerate session
            request()->session()->regenerate();

            // Update last connection date
            $client->date_der_connexion = now();
            $client->save();

            // If this is a newly created account, redirect to profile to complete information
            Log::info('Google OAuth - Is new account: ' . ($isNewAccount ? 'YES' : 'NO'));
            
            if ($isNewAccount) {
                Log::info('Redirecting to profile.edit');
                return redirect()->route('dashboard.profile.edit');
            }

            Log::info('Redirecting to dashboard.index');
            return redirect()->route('dashboard.index')
                ->with('success', 'Connexion réussie avec Google !');
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Erreur lors de la connexion avec Google. Veuillez réessayer.');
        }
    }
}
