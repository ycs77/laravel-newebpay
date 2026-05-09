<?php

namespace Ycs77\NewebPay\Transporters;

use Illuminate\Http\Response;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter as FormRedirectTransporterContract;

class FormRedirectTransporter implements FormRedirectTransporterContract
{
    public function send(string $url, array $data): Response
    {
        $inputFields = '';

        foreach ($data as $key => $value) {
            $inputFields .= sprintf(
                '<input type="hidden" name="%s" value="%s">', e($key), e($value)
            );
        }

        return response(<<<HTML
<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8" />
        <title>正在前往金流頁面</title>
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang TC", "Microsoft JhengHei", sans-serif;
                color: #555;
                background: #fafafa;
            }
            .loading {
                text-align: center;
            }
            .spinner {
                width: 40px;
                height: 40px;
                margin: 0 auto 16px;
                border: 4px solid #eee;
                border-top-color: #888;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            .loading p {
                margin: 0;
                font-size: 15px;
                letter-spacing: 0.05em;
            }
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>

    <body>
        <div class="loading">
            <div class="spinner"></div>
            <p>正在前往金流頁面，請稍候…</p>
        </div>

        <form id="order-form" action="{$url}" method="post" hidden>
            {$inputFields}
            <noscript><input type="submit" value="若未自動跳轉，請點此繼續"></noscript>
        </form>

        <script>document.getElementById("order-form").submit();</script>
    </body>
</html>
HTML)->header('Content-Type', 'text/html');
    }
}
