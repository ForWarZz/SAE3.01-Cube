<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoAuthVerifyRequest;
use App\Models\Client;
use App\Services\TwoFactorService;
use Exception;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticationController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService
    ) {}

    public function show()
    {
        if (! session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(TwoAuthVerifyRequest $request)
    {
        $userId = session('2fa:user:id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = Client::find($userId);
        if (! $user || ! $user->two_factor_confirmed_at) {
            session()->forget('2fa:user:id');

            return redirect()->route('login');
        }

        $code = $request->code;
        $isValid = false;

        if (strlen($code) === 6 && is_numeric($code)) {
            try {
                $isValid = $this->twoFactorService->verifyTotp($user, $code);
            } catch (Exception) {
            }
        }

        if (! $isValid) {
            try {
                $isValid = $this->twoFactorService->useRecoveryCode($user, $code);
            } catch (Exception) {
            }
        }

        if ($isValid) {
            session()->forget('2fa:user:id');
            Auth::login($user);

            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors([
            'code' => 'Le code est invalide. Veuillez réessayer.',
        ]);
    }
}
