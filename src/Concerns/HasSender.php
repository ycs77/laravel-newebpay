<?php

namespace Ycs77\NewebPay\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\Contracts\Httpable;
use Ycs77\NewebPay\Contracts\SenderV1;
use Ycs77\NewebPay\Senders\BackgroundSender;
use Ycs77\NewebPay\Senders\FrontendSender;

/** @deprecated */
trait HasSender
{
    protected SenderV1 $sender;

    public function setSender(SenderV1 $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender(): SenderV1
    {
        return $this->sender;
    }

    public function setFrontendSender()
    {
        $this->setSender(new FrontendSender);

        return $this;
    }

    public function setBackgroundSender()
    {
        $this->setSender(new BackgroundSender($this->createHttp()));

        return $this;
    }

    public function setMockHttp(MockHandler|Response $mockResponse)
    {
        if ($this->sender instanceof Httpable) {
            if ($mockResponse instanceof Response) {
                $mockHandler = new MockHandler([$mockResponse]);
            }

            $this->sender->setHttp($this->createHttp($mockHandler));
        }

        return $this;
    }

    protected function createHttp(?MockHandler $mockHttpHandler = null): Client
    {
        $attributes = [];

        if ($mockHttpHandler) {
            $attributes['handler'] = HandlerStack::create($mockHttpHandler);
        }

        return new Client($attributes);
    }
}
