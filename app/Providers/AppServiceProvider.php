<?php

namespace App\Providers;

use App\Auth\Passwords\PasswordBrokerManager;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Extend the password broker manager to use our custom implementation
        $this->app->extend('auth.password', function ($service, $app) {
            return new PasswordBrokerManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
        Cashier::useCustomerModel(Client::class);

        //        // Configurer le fuseau horaire d'affichage pour Carbon
        //        // Les dates sont stockées en UTC mais affichées en Europe/Paris
        //        date_default_timezone_set('Europe/Paris');
    }
}
