<?php

namespace Treerful\NewebPay\Traits;

use GuzzleHttp\Client;

trait RequestTrait
{
    private function setRequestForm($request, $url)
    {
        $result = '<form name="newebpay" id="order-form" method="post" action=' . $url . ' >';

        foreach ($request as $key => $value) {
            $result .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }

        $result .= '</form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>';

        return $result;
    }

    // post 背景呼叫取得回傳值
    private function setPostRequest($request, $url)
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
