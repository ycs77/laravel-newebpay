<?php

namespace Ycs77\NewebPay;

use Illuminate\Support\ServiceProvider;

class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * Register service for package.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newebpay.php', 'newebpay');

        $this->app->singleton(NewebPay::class, function ($app) {
            return new NewebPay($app['config']);
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
