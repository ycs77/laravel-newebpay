<?php

namespace W4ll4se\NewebPay\Traits;

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
}
