<?php

namespace Ycs77\NewebPay\Senders;

use GuzzleHttp\Client;
use Ycs77\NewebPay\Contracts\Httpable;
use Ycs77\NewebPay\Contracts\RespondTypeable;
use Ycs77\NewebPay\Contracts\Sender;

class BackgroundSender implements Sender, Httpable, RespondTypeable
{
    /**
     * The API respond type.
     */
    protected string $respondType;

    public function __construct(
        protected Client $client
    ) {}

    /**
     * Send the data to API.
     */
    public function send(array $data, string $url): mixed
    {
        $parameter = [
            'form_params' => $data,
            'verify' => false,
        ];

        $result = $this->http->post($url, $parameter)->getBody();

        if ($this->respondType === 'JSON') {
            $result = json_decode($result, true);
        }

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

    /**
     * Respond type can setting "JSON" or "String".
     */
    public function respondType(string $type)
    {
        $this->respondType = $type;

        return $this;
    }
}
