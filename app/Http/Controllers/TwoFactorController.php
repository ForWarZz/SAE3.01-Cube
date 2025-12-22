<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequiredRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly Google2FA $google2fa
    ) {}

    public function enable(Request $request)
    {
        $client = Auth::user();

        try {
            $secret = $this->google2fa->generateSecretKey();

            $client->two_factor_secret = encrypt($secret);
            $client->save();

            return response()->json([
                'success' => true,
                'secret' => $secret,
                'qr_code_url' => $this->getQrCodeUrl($client, $secret),
            ]);
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la génération de la clé secrète.',
            ], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (! $valid) {
            return response()->json([
                'success' => false,
                'message' => 'Le code est invalide. Veuillez réessayer.',
            ], 422);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Confirm 2FA
        $user->two_factor_confirmed_at = now();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    public function disable(PasswordRequiredRequest $request)
    {
        $user = Auth::user();

        // If user has Google OAuth, don't require password
        if (! $user->google_id) {
            if (! password_verify($request->password, $user->hash_mdp_client)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe est incorrect.',
                ], 422);
            }
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'L\'authentification à deux facteurs a été désactivée.',
        ]);
    }

    public function showRecoveryCodes()
    {
        $user = Auth::user();

        if (! $user->two_factor_confirmed_at) {
            return response()->json([
                'success' => false,
                'message' => 'L\'authentification à deux facteurs n\'est pas activée.',
            ], 400);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    public function regenerateRecoveryCodes(PasswordRequiredRequest $request)
    {
        $user = Auth::user();

        if (! $user->two_factor_confirmed_at) {
            return response()->json([
                'success' => false,
                'message' => 'L\'authentification à deux facteurs n\'est pas activée.',
            ], 400);
        }

        // If user has Google OAuth, don't require password
        if (! $user->google_id) {
            if (! password_verify($request->password, $user->hash_mdp_client)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe est incorrect.',
                ], 422);
            }
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    protected function getQrCodeUrl($user, $secret)
    {
        $appName = config('app.name');
        $email = $user->email_client;

        return $this->google2fa->getQRCodeUrl(
            $appName,
            $email,
            $secret
        );
    }

    protected function generateRecoveryCodes()
    {
        return Collection::times(8, function () {
            return Str::random(10).'-'.Str::random(10);
        })->toArray();
    }
}
