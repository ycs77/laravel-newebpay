<?php

namespace Ycs77\NewebPay\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Ycs77\NewebPay\Contracts\HasHttp;
use Ycs77\NewebPay\Contracts\Sender;
use Ycs77\NewebPay\Senders\BackgroundSender;
use Ycs77\NewebPay\Senders\FrontendSender;

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

    public function setFrontendSender()
    {
        $this->setSender(new FrontendSender());

        return $this;
    }

    public function setBackgroundSender()
    {
        $this->setSender(new BackgroundSender($this->createHttp()));

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
