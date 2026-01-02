<?php

namespace App\Providers;

use App\Models\Client;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias('botman', BotMan::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model::preventLazyLoading(! app()->isProduction());
        Cashier::useCustomerModel(Client::class);
    }
}
