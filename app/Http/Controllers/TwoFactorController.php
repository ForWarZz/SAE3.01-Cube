<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCodeException;
use App\Exceptions\PasswordException;
use App\Exceptions\TwoFactorException;
use App\Http\Requests\PasswordRequiredRequest;
use App\Http\Requests\TwoAuthVerifyRequest;
use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService
    ) {}

    public function enable()
    {
        $user = Auth::user();

        try {
            $result = $this->twoFactorService->enable($user);

            return response()->json([
                'success' => true,
                'secret' => $result->secret,
                'qr_code_url' => $result->qrCodeUrl,
            ]);
        } catch (TwoFactorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function confirm(TwoAuthVerifyRequest $request)
    {
        $user = Auth::user();

        try {
            $recoveryCodes = $this->twoFactorService->confirm($user, $request->code);

            return response()->json([
                'success' => true,
                'recovery_codes' => $recoveryCodes,
            ]);
        } catch (InvalidCodeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (TwoFactorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function disable(PasswordRequiredRequest $request)
    {
        $user = Auth::user();
        try {
            $this->twoFactorService->disable($user, $request->password);

            return response()->json([
                'success' => true,
                'message' => "L'authentification à deux facteurs a été désactivée.",
            ]);
        } catch (PasswordException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function showRecoveryCodes()
    {
        $user = Auth::user();

        try {
            $recoveryCodes = $this->twoFactorService->showRecoveryCodes($user);

            return response()->json([
                'success' => true,
                'recovery_codes' => $recoveryCodes,
            ]);
        } catch (TwoFactorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function regenerateRecoveryCodes(PasswordRequiredRequest $request)
    {
        $user = Auth::user();

        try {
            $recoveryCodes = $this->twoFactorService->regenerateRecoveryCodes($user, $request->password);

            return response()->json([
                'success' => true,
                'recovery_codes' => $recoveryCodes,
            ]);
        } catch (PasswordException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (TwoFactorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
