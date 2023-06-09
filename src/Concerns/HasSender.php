<?php

namespace Ycs77\NewebPay\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Ycs77\NewebPay\Contracts\HasHttp;
use Ycs77\NewebPay\Contracts\Sender;
use Ycs77\NewebPay\Senders\AsyncSender;
use Ycs77\NewebPay\Senders\SyncSender;

trait HasSender
{
    protected Sender $sender;

    public function setSender(Sender $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSyncSender()
    {
        $this->setSender(new SyncSender());

        return $this;
    }

    public function setAsyncSender()
    {
        $this->setSender(new AsyncSender($this->createHttp()));

        return $this;
    }

    public function setMockHttp(MockHandler|array $mockResponse)
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
