<?php

namespace Ycs77\NewebPay\Resources\Concerns;

use Ycs77\NewebPay\Builders\Concerns\HasPrepareOptions;

trait PrepareBuilder
{
    use HasPrepareOptions;

    /**
     * @template TBuilder
     *
     * @param  TBuilder  $builder
     * @return TBuilder
     */
    protected function prepareBuilder($builder)
    {
        if ($this->onPreparedOptionsCallback) {
            return $builder->onPreparedOptions($this->onPreparedOptionsCallback);
        }

        return $builder;
    }
}
