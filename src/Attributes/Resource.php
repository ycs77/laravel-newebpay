<?php

namespace Ycs77\NewebPay\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Resource
{
    public function __construct(
        public string $name,
        public ?string $action = null
    ) {}
}
