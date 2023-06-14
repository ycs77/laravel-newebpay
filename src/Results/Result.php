<?php

namespace Ycs77\NewebPay\Results;

abstract class Result
{
    /**
     * The newebpay result data.
     */
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $this->transformData($data);
    }

    /**
     * The result data.
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Transform the input data.
     */
    protected function transformData(array $data): array
    {
        $keys = $this->dataKeys();

        if (count($keys)) {
            return collect($data)
                ->only($keys)
                ->all();
        }

        return $data;
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [];
    }
}
