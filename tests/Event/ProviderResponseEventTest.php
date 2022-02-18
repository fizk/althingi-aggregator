<?php

namespace App\Event;

use App\Event\ProviderResponseEvent;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\{Request, Response};

class ProviderResponseEventTest extends TestCase
{
    public function testSuccess()
    {
        $event = new ProviderResponseEvent(
            new Request(),
            new Response(),
        );

        $payload = json_decode($event, true);

        $this->assertNull($payload['error_trace']);
        $this->assertNull($payload['error_message']);
    }

    public function testSuccessWithResponseErrors()
    {
        $event = new ProviderResponseEvent(
            new Request(),
            (new Response())->withStatus(301),
        );

        $payload = json_decode($event, true);

        $this->assertIsArray($payload['error_trace']);
        $this->assertIsString($payload['error_message']);
    }
}
