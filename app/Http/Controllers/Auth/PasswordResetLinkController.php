<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if the email exists in our client table
        $client = Client::where('email_client', $request->email)->first();

        if (! $client) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Aucun compte n\'est associé à cette adresse email.']);
        }

        // Check if user registered via Google OAuth (no password to reset)
        if ($client->google_id && ! $client->hash_mdp_client) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce compte utilise la connexion Google. Veuillez vous connecter avec Google.']);
        }

        $status = Password::sendResetLink($validated);

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse email.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Impossible d\'envoyer le lien de réinitialisation. Veuillez réessayer.']);
    }
}
