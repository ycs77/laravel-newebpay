<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * Register service for package.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newebpay.php', 'newebpay');

        $this->app->singleton(Factory::class, function (Application $app) {
            return new Factory(
                $app->make('config'),
                $app->make('session.store')
            );
        });
    }

    /**
     * Bootstrap service for package.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/newebpay.php' => config_path('newebpay.php'),
        ], 'newebpay-config');
    }
}
