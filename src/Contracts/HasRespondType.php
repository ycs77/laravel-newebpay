<?php

namespace Ycs77\NewebPay\Contracts;

interface HasRespondType
{
    /**
     * Respond type can setting "JSON" or "String".
     */
    public function respondType(string $type);
}
