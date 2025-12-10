<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show the two-factor authentication challenge
     */
    public function show()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor authentication code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\Client::find($userId);
        if (!$user || !$user->two_factor_confirmed_at) {
            session()->forget('2fa:user:id');
            return redirect()->route('login');
        }

        $code = $request->code;
        $secret = decrypt($user->two_factor_secret);

        // Check if it's a TOTP code
        if (strlen($code) === 6 && is_numeric($code)) {
            $valid = $this->google2fa->verifyKey($secret, $code);
            
            if ($valid) {
                session()->forget('2fa:user:id');
                Auth::login($user, session('2fa:remember', false));
                session()->forget('2fa:remember');
                
                return redirect()->intended(route('dashboard.index'));
            }
        }

        // Check if it's a recovery code
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        if (in_array($code, $recoveryCodes)) {
            // Remove the used recovery code
            $recoveryCodes = array_values(array_diff($recoveryCodes, [$code]));
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            session()->forget('2fa:user:id');
            Auth::login($user, session('2fa:remember', false));
            session()->forget('2fa:remember');
            
            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors([
            'code' => 'Le code est invalide. Veuillez réessayer.'
        ]);
    }
}
