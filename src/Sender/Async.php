<?php

namespace Ycs77\NewebPay\Sender;

use GuzzleHttp\Client;
use Ycs77\NewebPay\Contracts\HasHttp;
use Ycs77\NewebPay\Contracts\Sender;

class Async implements Sender, HasHttp
{
    /**
     * The guzzle http client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new async instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->http = $client;
    }

    /**
     * Send the data to API.
     *
     * @param  array  $request
     * @param  string  $url
     * @return mixed
     */
    public function send($request, $url)
    {
        $parameter = [
            'form_params' => $request,
            'verify' => false,
        ];

        $result = json_decode($this->http->post($url, $parameter)->getBody(), true);

        return $result;
    }

    /**
     * Set mock http client instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return self
     */
    public function setHttp(Client $client)
    {
        $this->http = $client;

        return $this;
    }
}
