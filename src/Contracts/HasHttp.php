<?php

namespace Ycs77\NewebPay\Contracts;

use GuzzleHttp\Client;

interface HasHttp
{
    public function setHttp(Client $client);
}
