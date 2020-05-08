<?php

namespace Ycs77\NewebPay\Concerns;

use Ycs77\NewebPay\Contracts\Sender;
use Ycs77\NewebPay\Sender\Async;
use Ycs77\NewebPay\Sender\Sync;

trait HasSender
{
    /**
     * The sender instance.
     *
     * @var \Ycs77\NewebPay\Contracts\Sender
     */
    protected $sender;

    /**
     * Set the sender instance.
     *
     * @param  \Ycs77\NewebPay\Contracts\Sender  $sender
     * @return self
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Set sync sender.
     *
     * @return self
     */
    public function setSyncSender()
    {
        $this->setSender(new Sync());

        return $this;
    }

    /**
     * Set async sender.
     *
     * @return self
     */
    public function setAsyncSender()
    {
        $this->setSender(new Async());

        return $this;
    }
}
