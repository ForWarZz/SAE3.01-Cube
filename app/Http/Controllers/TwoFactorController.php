<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Enable two-factor authentication for the user
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        // Generate a secret key
        $secret = $this->google2fa->generateSecretKey();

        // Store the secret temporarily (not confirmed yet)
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        return response()->json([
            'success' => true,
            'secret' => $secret,
            'qr_code_url' => $this->getQrCodeUrl($user, $secret)
        ]);
    }

    /**
     * Confirm two-factor authentication setup
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Le code est invalide. Veuillez réessayer.'
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
            'recovery_codes' => $recoveryCodes
        ]);
    }

    /**
     * Disable two-factor authentication
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = Auth::user();

        // If user has Google OAuth, don't require password
        if (!$user->google_id) {
            if (!password_verify($request->password, $user->hash_mdp_client)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe est incorrect.'
                ], 422);
            }
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'L\'authentification à deux facteurs a été désactivée.'
        ]);
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes()
    {
        $user = Auth::user();

        if (!$user->two_factor_confirmed_at) {
            return response()->json([
                'success' => false,
                'message' => 'L\'authentification à deux facteurs n\'est pas activée.'
            ], 400);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes
        ]);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = Auth::user();

        if (!$user->two_factor_confirmed_at) {
            return response()->json([
                'success' => false,
                'message' => 'L\'authentification à deux facteurs n\'est pas activée.'
            ], 400);
        }

        // If user has Google OAuth, don't require password
        if (!$user->google_id) {
            if (!password_verify($request->password, $user->hash_mdp_client)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe est incorrect.'
                ], 422);
            }
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes
        ]);
    }

    /**
     * Generate QR code URL
     */
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

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes()
    {
        return Collection::times(8, function () {
            return Str::random(10) . '-' . Str::random(10);
        })->toArray();
    }
}
