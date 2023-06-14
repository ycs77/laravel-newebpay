<?php

namespace Ycs77\NewebPay\Senders;

use Illuminate\Http\Request;
use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\Contracts\Sender;
use Ycs77\NewebPay\Contracts\UserSourceable;

class FrontendSender implements Sender, UserSourceable
{
    public function __construct(
        protected UserSource $userSource
    ) {}

    /**
     * Preserve the user information into session.
     */
    public function preserveUserSource(Request $request): void
    {
        $this->userSource->preserve($request);
    }

    /**
     * Send the data to API.
     */
    public function send(array $request, string $url): mixed
    {
        $result = '<!DOCTYPE html><html><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><form id="order-form" method="post" action="'.$url.'">';

        foreach ($request as $key => $value) {
            $result .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
        }

        $result .= '</form><script>document.getElementById("order-form").submit();</script></body></html>';

        return $result;
    }

    /**
     * Set the user source instance.
     */
    public function setUserSource(UserSource $userSource)
    {
        $this->userSource = $userSource;

        return $this;
    }
}
