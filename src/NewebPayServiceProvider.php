<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\ServiceProvider;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter as FormRedirectTransporterContract;
use Ycs77\NewebPay\Contracts\HttpTransporter as HttpTransporterContract;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Transporters\FormRedirectTransporter;
use Ycs77\NewebPay\Transporters\HttpTransporter;
use Ycs77\NewebPay\Url\UrlFormatter;

class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * Register service for package.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newebpay.php', 'newebpay');

        $this->app->singleton(HttpTransporterContract::class, function ($app) {
            return new HttpTransporter($app->make(HttpClient::class));
        });

        $this->app->singleton(FormRedirectTransporterContract::class, function () {
            return new FormRedirectTransporter;
        });

        $this->app->singleton(Factory::class, function ($app) {
            return new Factory(
                $app->make(Crypto::class),
                $app->make(FormRedirectTransporterContract::class),
                $app->make(HttpTransporterContract::class),
                $app->make(UrlFormatter::class),
                $app->make('config')->get('newebpay')
            );
        });

        $this->app->alias(Factory::class, 'newebpay');
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
