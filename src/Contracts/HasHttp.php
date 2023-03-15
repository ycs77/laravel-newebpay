<?php

namespace Webcs4JIG\NewebPay\Contracts;

use GuzzleHttp\Client;

interface HasHttp
{
    /**
     * Set mock http client instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return $this
     */
    public function setHttp(Client $client);
}
