<?php

use Ycs77\NewebPay\Tests\TestCase;

if (function_exists('pest')) {
    pest()->extend(TestCase::class)->in('Feature');
} else {
    // Fallback for Pest v1.x
    uses(TestCase::class)->in('Feature');
}
