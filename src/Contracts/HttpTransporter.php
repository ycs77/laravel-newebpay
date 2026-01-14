<?php

namespace Ycs77\NewebPay\Contracts;

use Illuminate\Http\Client\Response as ClientResponse;

interface HttpTransporter extends Transporter
{
    public function setTimeout(int $seconds): static;

    public function send(string $url, array $data): ClientResponse;
}
