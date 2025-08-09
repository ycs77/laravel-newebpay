<?php

namespace Ycs77\NewebPay\Senders;

use Illuminate\Http\Response;
use Ycs77\NewebPay\Contracts\FormPostSender as FormPostSenderContract;

class FormPostSender implements FormPostSenderContract
{
    public function send(string $url, array $data): Response
    {
        $inputFields = '';

        foreach ($data as $key => $value) {
            $inputFields .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                htmlspecialchars($key, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
            );
        }

        return response(<<<HTML
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>

    <body>
        <form id="order-form" action="{$url}" method="post">
            {$inputFields}
            <input type="submit">
        </form>

        <script>document.getElementById("order-form").submit();</script>
    </body>
</html>
HTML)->header('Content-Type', 'text/html');
    }
}
