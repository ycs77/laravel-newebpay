<?php

namespace Ycs77\NewebPay\Results;

abstract class Result
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $this->transformData($data);
    }

    public function data(): array
    {
        return $this->data;
    }

    /**
     * Transform the input data.
     */
    protected function transformData(array $data): array
    {
        $keys = $this->allowedDataKeys();

        if (count($keys)) {
            return collect($data)
                ->only($keys)
                ->all();
        }

        return $data;
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [];
    }
}
