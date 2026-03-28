<?php

namespace Ycs77\NewebPay\Builders\Concerns;

use Closure;

trait HasPrepareOptions
{
    /**
     * @var callable|null
     */
    protected $onPreparedOptionsCallback;

    public function onPreparedOptions(Closure $callback)
    {
        $this->onPreparedOptionsCallback = $callback;

        return $this;
    }
}
