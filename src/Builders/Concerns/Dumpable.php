<?php

namespace Ycs77\NewebPay\Builders\Concerns;

/**
 * @method \Ycs77\NewebPay\Options\Options options()
 */
trait Dumpable
{
    public function dd(): never
    {
        dd($this->options()->toArray());
    }
}
