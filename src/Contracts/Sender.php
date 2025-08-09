<?php

namespace Ycs77\NewebPay\Contracts;

interface Sender
{
    public function send(string $url, array $data): mixed;
}
