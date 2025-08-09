<?php

namespace Ycs77\NewebPay\Url;

use Illuminate\Config\Repository as Config;

class PrependAppUrl
{
    public function __construct(
        protected Config $config
    ) {
        //
    }

    public function handle(string $url): string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $url = $this->config->get('app.url').$url;
        }

        return $url;
    }
}
