<?php

namespace App\Event;

use App\Event\ErrorEvent;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\{Request};
use Exception;

class ErrorEventTest extends TestCase
{
    public function testSuccess()
    {
        $event = new ErrorEvent(
            new Request(),
            new Exception()
        );

        $this->assertIsString($event->__toString());
    }
}
