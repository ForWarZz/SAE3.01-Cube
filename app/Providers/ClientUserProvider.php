<?php

namespace App\Providers;

use App\Models\Client;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class ClientUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if ($key === 'email') {
                $query->where('email_client', $value);
            } elseif (! str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return Hash::check($plain, $user->getAuthPassword());
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Le modèle Client n'a pas de remember_token
        // On peut l'ajouter plus tard si nécessaire
    }
}
