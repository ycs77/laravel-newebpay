<?php

namespace Ycs77\NewebPay\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Ycs77\NewebPay\Contracts\HasHttp;
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
     * @return $this
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get the sender instance.
     *
     * @return \Ycs77\NewebPay\Contracts\Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set sync sender.
     *
     * @return $this
     */
    public function setSyncSender()
    {
        $this->setSender(new Sync());

        return $this;
    }

    /**
     * Set async sender.
     *
     * @return $this
     */
    public function setAsyncSender()
    {
        $this->setSender(new Async($this->createHttp()));

        return $this;
    }

    /**
     * Set mock http instance.
     *
     * @param  \GuzzleHttp\Handler\MockHandler|array  $mockHandler
     * @return $this
     */
    public function setMockHttp($mockResponse)
    {
        if ($this->sender instanceof HasHttp) {
            if (!$mockResponse instanceof MockHandler) {
                $mockHandler = new MockHandler($mockResponse);
            }

            $this->sender->setHttp($this->createHttp($mockHandler));
        }

        return $this;
    }

    /**
     * Create http instance.
     *
     * @param  \GuzzleHttp\Handler\MockHandler|null  $mockHttpHandler
     * @return \GuzzleHttp\Client
     */
    protected function createHttp($mockHttpHandler = null)
    {
        $attributes = [
            'handler' => $mockHttpHandler ? HandlerStack::create($mockHttpHandler) : null,
        ];

        $attributes = array_filter($attributes, function ($value) {
            return $value !== null;
        });

        return new Client($attributes);
    }
}
