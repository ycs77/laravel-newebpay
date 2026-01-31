<?php

namespace Ycs77\NewebPay\Results;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @phpstan-consistent-constructor */
abstract class Result implements Arrayable, JsonSerializable
{
    protected array $data;

    protected array $result;

    public function __construct(array $data)
    {
        $this->data = $this->transformData($data);
        $this->result = $this->transformResult($data);
    }

    public static function make(array $data): static
    {
        return new static($data);
    }

    public function result(): array
    {
        return $this->result;
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
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['Result'] ?? [];
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
