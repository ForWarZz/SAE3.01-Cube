<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use Illuminate\Support\Str;

class PasswordBrokerManager extends BasePasswordBrokerManager
{
    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \App\Auth\Passwords\DatabaseTokenRepository
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return new DatabaseTokenRepository(
            $this->app['db']->connection($config['connection'] ?? null),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }
}
