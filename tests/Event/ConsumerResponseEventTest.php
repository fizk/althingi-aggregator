<?php

namespace App\Event;

use App\Event\ConsumerResponseEvent;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\{Request, Response};
use Exception;

class ConsumerResponseEventTest extends TestCase
{
    public function testSuccess()
    {
        $event = new ConsumerResponseEvent(
            new Request(),
            new Response(),
            new Exception()
        );

        $this->assertIsString($event->__toString());
    }

    public function testNoException()
    {
        $event = new ConsumerResponseEvent(
            new Request(),
            new Response(),
        );

        $this->assertIsString($event->__toString());
    }
}
