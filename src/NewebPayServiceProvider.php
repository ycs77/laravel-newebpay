<?php

namespace Ycs77\NewebPay;

use Illuminate\Support\ServiceProvider;

class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NewebPay::class, function($app) {
            return new NewebPay($app['config']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/newebpay.php' => config_path('newebpay.php'),
        ]);
    }
}
