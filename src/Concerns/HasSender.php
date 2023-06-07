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
    protected Sender $sender;

    public function setSender(Sender $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSyncSender(): self
    {
        $this->setSender(new Sync());

        return $this;
    }

    public function setAsyncSender(): self
    {
        $this->setSender(new Async($this->createHttp()));

        return $this;
    }

    public function setMockHttp(MockHandler|array $mockResponse): self
    {
        if ($this->sender instanceof HasHttp) {
            if (! $mockResponse instanceof MockHandler) {
                $mockHandler = new MockHandler($mockResponse);
            }

            $this->sender->setHttp($this->createHttp($mockHandler));
        }

        return $this;
    }

    protected function createHttp(MockHandler $mockHttpHandler = null): Client
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
