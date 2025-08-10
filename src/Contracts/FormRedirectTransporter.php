<?php

namespace Ycs77\NewebPay\Contracts;

use Illuminate\Http\Response;

interface FormRedirectTransporter extends Sender
{
    public function send(string $url, array $data): Response;
}
