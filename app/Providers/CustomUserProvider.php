<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Builder|Builder[]|Collection|Model
     */
    public function retrieveById($identifier): Model|Collection|Builder|array
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $model = $this->createModel();

        unset($credentials['token']);

        if (isset($credentials['email'])) {
            $realColumn = $model->getLoginIdentifierName();

            if ($realColumn !== 'email') {
                $credentials[$realColumn] = $credentials['email'];
                unset($credentials['email']);
            }
        }

        return parent::retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        return Hash::check($plain, $user->getAuthPassword());
    }
}
