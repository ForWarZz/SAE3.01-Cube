<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use App\Models\Client;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(PasswordResetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $status = Password::reset(
            $validated,
            fn (Client $client) => $client->forceFill([
                'hash_mdp_client' => Hash::make($validated['password']),
            ])->save() && event(new PasswordReset($client))
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Le lien de réinitialisation est invalide ou a expiré.']);
    }
}
