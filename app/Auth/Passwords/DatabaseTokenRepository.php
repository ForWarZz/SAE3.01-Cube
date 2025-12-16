<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class DatabaseTokenRepository extends BaseDatabaseTokenRepository
{
    /**
     * Get the client ID from the user model.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    protected function getClientId(CanResetPasswordContract $user)
    {
        return $user->id_client;
    }

    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $this->getTable()->insert([
            'id_client' => $this->getClientId($user),
            'token' => $this->hasher->make($token),
            'created_at' => new \DateTime,
        ]);

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where(
            'id_client', $this->getClientId($user)
        )->delete();
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'id_client', $this->getClientId($user)
        )->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()->where(
            'id_client', $this->getClientId($user)
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Delete a token record by user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->getTable()->where('id_client', $this->getClientId($user))->delete();
    }
}
