<?php

namespace Ycs77\NewebPay\Contracts;

use Illuminate\Http\Client\Response as ClientResponse;

interface HttpSender extends Sender
{
    public function send(string $url, array $data): ClientResponse;
}
