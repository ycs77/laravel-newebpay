<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\ServiceProvider;
use Ycs77\NewebPay\Contracts\FormPostSender as FormPostSenderContract;
use Ycs77\NewebPay\Contracts\HttpSender as HttpSenderContract;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Senders\FormPostSender;
use Ycs77\NewebPay\Senders\HttpSender;

class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * Register service for package.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newebpay.php', 'newebpay');

        $this->app->singleton(HttpSenderContract::class, function ($app) {
            return new HttpSender($app->make(HttpClient::class));
        });

        $this->app->singleton(FormPostSenderContract::class, function () {
            return new FormPostSender;
        });

        $this->app->singleton(Factory::class, function ($app) {
            return new Factory(
                $app->make(Crypto::class),
                $app->make(FormPostSenderContract::class),
                $app->make(HttpSenderContract::class),
                $app->make('config')->get('newebpay')
            );
        });

        $this->app->alias(Factory::class, 'newebpay');

        $this->app->singleton(FactoryV1::class, function ($app) {
            return new FactoryV1(
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
