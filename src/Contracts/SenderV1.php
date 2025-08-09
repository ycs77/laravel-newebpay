<?php

namespace Ycs77\NewebPay\Contracts;

/** @deprecated */
interface SenderV1
{
    /**
     * Send the data to API.
     */
    public function send(array $data, string $url): mixed;
}
