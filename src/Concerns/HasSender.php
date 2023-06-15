<?php

namespace Ycs77\NewebPay\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\Contracts\Httpable;
use Ycs77\NewebPay\Contracts\Sender;
use Ycs77\NewebPay\Contracts\UserSourceable;
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
        $this->setSender(new FrontendSender(app()->make(UserSource::class)));

        return $this;
    }

    public function setUserSource(UserSource $userSource)
    {
        if ($this->sender instanceof UserSourceable) {
            $this->sender->setUserSource($userSource);
        }

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

    protected function createHttp(MockHandler $mockHttpHandler = null): Client
    {
        $attributes = [];

        if ($mockHttpHandler) {
            $attributes['handler'] = HandlerStack::create($mockHttpHandler);
        }

        return new Client($attributes);
    }
}
