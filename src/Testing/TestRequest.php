<?php

namespace Ycs77\NewebPay\Testing;

use Ycs77\NewebPay\Options\Options;

final class TestRequest
{
    public function __construct(
        private readonly string $resource,
        private readonly ?string $action,
        private readonly Options $options
    ) {}

    public function resource(): string
    {
        return $this->resource;
    }

    public function action(): ?string
    {
        return $this->action;
    }

    public function options(): Options
    {
        return $this->options;
    }
}
