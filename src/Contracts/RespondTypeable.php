<?php

namespace Ycs77\NewebPay\Contracts;

interface RespondTypeable
{
    /**
     * Respond type can setting "JSON" or "String".
     */
    public function respondType(string $type);
}
