<?php

namespace Ycs77\NewebPay\Transporters;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response as ClientResponse;
use Ycs77\NewebPay\Contracts\HttpTransporter as HttpTransporterContract;

class HttpTransporter implements HttpTransporterContract
{
    public function __construct(
        protected Factory $client
    ) {
        //
    }

    public function send(string $url, array $data): ClientResponse
    {
        return $this->client
            ->asForm()
            ->withUserAgent('NewebPay')
            ->post($url, $data);
    }
}
