<?php

if (function_exists('pest')) {
    pest()->extend(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
} else {
    // Fallback for Pest v1.x
    uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
}
