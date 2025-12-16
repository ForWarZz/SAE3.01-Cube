<?php

namespace App\Auth\Passwords;

use Closure;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class PasswordBroker extends BasePasswordBroker
{
    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     */
    public function getUser(array $credentials)
    {
        // Remove non-email fields that shouldn't be used to query the user
        $credentials = array_diff_key($credentials, array_flip(['token', 'password', 'password_confirmation']));

        // Map email_client to the correct field for our custom user provider
        if (isset($credentials['email_client'])) {
            $email = $credentials['email_client'];
            unset($credentials['email_client']);
            $credentials['email'] = $email;
        }

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            return null;
        }

        return $user;
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        $this->tokens->delete($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @param  \Closure|null  $callback
     * @return string
     */
    public function sendResetLink(array $credentials, Closure $callback = null)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = $this->tokens->create($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            $user->sendPasswordResetNotification($token);
        }

        return static::RESET_LINK_SENT;
    }
}
