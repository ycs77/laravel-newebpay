<?php

namespace Ycs77\NewebPay\Contracts;

use GuzzleHttp\Client;

/** @deprecated */
interface Httpable
{
    /**
     * Set the http client instance.
     */
    public function setHttp(Client $client);
}
