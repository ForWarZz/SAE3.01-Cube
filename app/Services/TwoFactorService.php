<?php

namespace App\Services;

use App\DTOs\TwoFactorResultDTO;
use App\Exceptions\InvalidCodeException;
use App\Exceptions\PasswordException;
use App\Exceptions\TwoFactorException;
use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use Throwable;

class TwoFactorService
{
    public function __construct(private readonly Google2FA $google2fa) {}

    /**
     * @throws TwoFactorException
     */
    public function enable($user): TwoFactorResultDTO
    {
        try {
            $secret = $this->google2fa->generateSecretKey();
            $user->two_factor_secret = encrypt($secret);
            $user->save();

            return new TwoFactorResultDTO(
                secret: $secret,
                qrCodeUrl: $this->getQrCodeUrl($user, $secret),
            );
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
            throw new TwoFactorException('Erreur lors de la génération de la clé secrète.');
        }
    }

    /**
     * @return array<string>
     *
     * @throws InvalidCodeException
     * @throws TwoFactorException
     */
    public function confirm($user, string $code): array
    {
        try {
            $secret = decrypt($user->two_factor_secret);

            if (! $this->google2fa->verifyKey($secret, $code)) {
                throw new InvalidCodeException('Le code 2FA est invalide.');
            }

            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_confirmed_at = now();
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            return $recoveryCodes;
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
            throw new TwoFactorException('Erreur lors de la vérification du code 2FA.');
        }
    }

    /**
     * @throws PasswordException
     */
    public function disable($user, ?string $password = null): void
    {
        if (! $user->google_id) {
            if (! $password || ! password_verify($password, $user->hash_mdp_client)) {
                throw new PasswordException('Mot de passe incorrect.');
            }
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
    }

    /**
     * @return array<string>
     *
     * @throws TwoFactorException
     */
    public function showRecoveryCodes($user): array
    {
        if (! $user->two_factor_confirmed_at) {
            throw new TwoFactorException('L\'authentification à deux facteurs n\'est pas activée.');
        }

        return json_decode(decrypt($user->two_factor_recovery_codes), true);
    }

    /**
     * @return array<string>
     *
     * @throws TwoFactorException
     * @throws PasswordException
     */
    public function regenerateRecoveryCodes($user, ?string $password = null): array
    {
        if (! $user->two_factor_confirmed_at) {
            throw new TwoFactorException('L\'authentification à deux facteurs n\'est pas activée.');
        }

        if (! $user->google_id) {
            if (! $password || ! password_verify($password, $user->hash_mdp_client)) {
                throw new PasswordException('Mot de passe incorrect.');
            }
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return $recoveryCodes;
    }

    public function getQrCodeUrl($user, string $secret): string
    {
        $appName = Config::get('app.name');
        $email = $user->email_client;

        return $this->google2fa->getQRCodeUrl($appName, $email, $secret);
    }

    public function generateRecoveryCodes(): array
    {
        return Collection::times(8, fn () => Str::random(10).'-'.Str::random(10))->toArray();
    }

    /**
     * @throws TwoFactorException
     */
    public function verifyTotp(Client $user, string $code): bool
    {
        try {
            if (! $user->two_factor_secret) {
                throw new TwoFactorException("La 2FA n'est pas configurée pour cet utilisateur.");
            }

            $secret = decrypt($user->two_factor_secret);

            return $this->google2fa->verifyKey($secret, $code);
        } catch (Throwable $e) {
            throw new TwoFactorException('Erreur lors de la vérification du code TOTP.');
        }
    }

    /**
     * @throws TwoFactorException
     */
    public function useRecoveryCode(Client $user, string $code): bool
    {
        try {
            if (! $user->two_factor_recovery_codes) {
                throw new TwoFactorException('Aucun code de récupération disponible.');
            }

            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            if (! is_array($recoveryCodes) || ! in_array($code, $recoveryCodes)) {
                return false;
            }

            $recoveryCodes = array_values(array_diff($recoveryCodes, [$code]));
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            return true;
        } catch (Throwable $e) {
            throw new TwoFactorException('Erreur lors de la vérification du code de récupération.');
        }
    }
}
