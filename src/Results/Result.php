<?php

namespace Ycs77\NewebPay\Results;

abstract class Result
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}
