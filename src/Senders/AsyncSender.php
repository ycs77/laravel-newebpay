<?php

namespace Ycs77\NewebPay\Senders;

use GuzzleHttp\Client;
use Ycs77\NewebPay\Contracts\HasHttp;
use Ycs77\NewebPay\Contracts\Sender;

class AsyncSender implements Sender, HasHttp
{
    /**
     * The guzzle http client instance.
     */
    protected Client $http;

    public function __construct(Client $client)
    {
        $this->http = $client;
    }

    /**
     * Send the data to API.
     */
    public function send(array $request, string $url): mixed
    {
        $parameter = [
            'form_params' => $request,
            'verify' => false,
        ];

        $result = json_decode($this->http->post($url, $parameter)->getBody(), true);

        return $result;
    }

    public function setHttp(Client $client)
    {
        $this->http = $client;

        return $this;
    }
}
