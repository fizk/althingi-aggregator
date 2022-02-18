<?php

namespace App\Event;

use App\Event\ThrowEvent;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\{Request};
use Exception;

class ThrowEventTest extends TestCase
{
    public function testSuccess()
    {
        $event = new ThrowEvent(
            new Exception()
        );

        $this->assertIsString($event->__toString());
    }
}
