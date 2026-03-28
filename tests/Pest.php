<?php

if (function_exists('pest')) {
    pest()->extend(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
    /** @deprecated */
    pest()->extend(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');
} else {
    // Fallback for Pest v1.x
    uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
    /** @deprecated */
    uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');
}
