<?php

namespace Ycs77\NewebPay\Senders;

use Ycs77\NewebPay\Contracts\HttpSender as HttpSenderContract;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response as ClientResponse;

class HttpSender implements HttpSenderContract
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
