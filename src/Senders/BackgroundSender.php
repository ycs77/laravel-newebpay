<?php

namespace Ycs77\NewebPay\Senders;

use GuzzleHttp\Client;
use Ycs77\NewebPay\Contracts\Httpable;
use Ycs77\NewebPay\Contracts\Sender;

class BackgroundSender implements Sender, Httpable
{
    public function __construct(
        protected Client $http
    ) {
    }

    /**
     * Send the data to API.
     */
    public function send(array $data, string $url): mixed
    {
        $parameter = [
            'form_params' => $data,
            'verify' => false,
        ];

        $result = json_decode($this->http->post($url, $parameter)->getBody(), true);

        return $result;
    }

    /**
     * Set the http client instance.
     */
    public function setHttp(Client $client)
    {
        $this->http = $client;

        return $this;
    }
}
