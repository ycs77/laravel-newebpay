<?php

namespace Ycs77\NewebPay\Sender;

use GuzzleHttp\Client;
use Ycs77\NewebPay\Contracts\Sender;

class Async implements Sender
{
    /**
     * Send the data to API.
     *
     * @param  array  $request
     * @param  string  $url
     * @return mixed
     */
    public function send($request, $url)
    {
        $parameter = [
            'form_params' => $request,
            'verify' => false
        ];

        $client = new Client();
        $result = json_decode($client->post($url, $parameter)->getBody(), true);

        return $result;
    }
}
