<?php

namespace Ycs77\NewebPay\Contracts;

interface Sender
{
    /**
     * Send the data to API.
     */
    public function send(array $data, string $url): mixed;
}
