<?php

namespace Ycs77\NewebPay\Transporters;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response as ClientResponse;
use Ycs77\NewebPay\Contracts\HttpTransporter as HttpTransporterContract;

class HttpTransporter implements HttpTransporterContract
{
    protected int $timeout = 30;

    public function __construct(
        protected Factory $client
    ) {
        //
    }

    public function setTimeout(int $seconds): static
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function send(string $url, array $data): ClientResponse
    {
        /** @var ClientResponse */
        $response = $this->client
            ->asForm()
            ->withUserAgent('NewebPay')
            ->timeout($this->timeout)
            ->post($url, $data);

        return $response;
    }
}
