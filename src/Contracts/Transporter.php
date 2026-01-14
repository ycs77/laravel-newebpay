<?php

namespace Ycs77\NewebPay\Contracts;

interface Transporter
{
    public function send(string $url, array $data): mixed;
}
