<?php

namespace Enflow\Address;

use Enflow\Address\Http\Controllers\SuggestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AddressServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->defineRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/address.php' => config_path('address.php'),
            ], 'config');

            if (!class_exists('CreateAddressesTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_addresses_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_addresses_table.php'),
                ], 'migrations');
            }
        }
    }

    public function register()
    {
        $this->app->singleton(DriverManager::class, function ($app) {
            return new DriverManager($app);
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/address.php', 'address');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'address');
    }

    protected function defineRoutes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        require __DIR__ . '/Http/routes.php';
    }
}
