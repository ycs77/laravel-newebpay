<?php

namespace Ycs77\NewebPay\Senders;

use Ycs77\NewebPay\Contracts\Sender;

class SyncSender implements Sender
{
    /**
     * Send the data to API.
     */
    public function send(array $request, string $url): mixed
    {
        $result = '<form id="order-form" method="post" action="'.$url.'">';

        foreach ($request as $key => $value) {
            $result .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
        }

        $result .= '</form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>';

        return $result;
    }
}
