<?php

namespace App\Event;

use App\Event\SystemSuccessEvent;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\{Request, Response};

class SystemSuccessEventTest extends TestCase
{
    public function testSuccess()
    {
        $event = new SystemSuccessEvent(
            new Request(),
            new Response(),
        );

        $this->assertIsString($event->__toString());
    }
}
