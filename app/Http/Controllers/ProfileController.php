<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        return view('dashboard.profile.show', [
            'client' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('dashboard.profile.edit', [
            'client' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $client = $request->user();

        $validated = $request->validate([
            'civilite' => ['required', 'string', 'in:Monsieur,Madame'],
            'prenom_client' => ['required', 'string', 'max:255'],
            'nom_client' => ['required', 'string', 'max:255'],
            'email_client' => ['required', 'string', 'email', 'max:255', 'unique:client,email_client,'.$client->id_client.',id_client'],
            'naissance_client' => ['required', 'date'],
        ]);

        $client->update($validated);

        return redirect()->route('dashboard.profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $client = $request->user();

        // Google OAuth users cannot change password
        if ($client->google_id) {
            return back()->withErrors(['password' => 'Vous êtes connecté avec Google. Gérez votre mot de passe via votre compte Google.']);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:12',
                'regex:/[a-z]/',      // au moins une minuscule
                'regex:/[A-Z]/',      // au moins une majuscule
                'regex:/[0-9]/',      // au moins un chiffre
                'regex:/[@$!%*?&#]/', // au moins un caractère spécial
            ],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&#).',
        ]);

        // Check if current password is correct
        if (! Hash::check($validated['current_password'], $client->hash_mdp_client)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $client->update([
            'hash_mdp_client' => Hash::make($validated['password']),
        ]);

        return redirect()->route('dashboard.profile.show')
            ->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Delete the user's account (GDPR compliance).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $client = $request->user();

        // For Google OAuth users, skip password verification
        if (! $client->google_id) {
            $request->validate([
                'password' => ['required', 'string'],
                'confirmation' => ['required', 'accepted'],
            ]);

            // Verify password
            if (! Hash::check($request->password, $client->hash_mdp_client)) {
                return back()->withErrors(['password' => 'Le mot de passe est incorrect.']);
            }
        } else {
            $request->validate([
                'confirmation' => ['required', 'accepted'],
            ]);
        }

        // Delete all related data (GDPR compliance)
        // Delete addresses if the relationship exists
        if (method_exists($client, 'addresses')) {
            $client->addresses()->delete();
        }

        // TODO: Uncomment when serviceRequests model is created
        // if (method_exists($client, 'serviceRequests')) {
        //     $client->serviceRequests()->delete();
        // }

        // Logout
        Auth::logout();

        // Delete the client account
        $client->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Votre compte et toutes vos données ont été supprimés conformément au RGPD.');
    }
}
