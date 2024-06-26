<?php

namespace App\Logging;

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use App\Event\{
    ConsumerErrorEvent,
    ConsumerSuccessEvent,
    ErrorEvent,
    ExceptionEvent,
    ProviderErrorEvent,
    ProviderSuccessEvent,
    SystemSuccessEvent
};
use Exception;
use Monolog\Handler\AbstractProcessingHandler;

class LogFormatTest extends TestCase
{
    public function testErrorEvent()
    {
        $handler = new class extends AbstractProcessingHandler
        {
            private array $record;
            protected function write(array $record): void
            {
                $this->record = $record;
            }

            public function getMessage(): string
            {
                return $this->record['formatted'];
            }
        };

        $handler->setFormatter(new LineFormatter("[%datetime%] %level_name% %message%\n"));

        $event = new ErrorEvent(
            (new Request())->withAddedHeader('X-HTTP-Method-Override', 'GET'),
            new Exception()
        );

        $logger = (new Logger('aggregator'))->pushHandler($handler);

        $logger->debug((string) $event);

        $this->assertEquals(
            1,
            preg_match(
                '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\.[0-9]{6}\+00:00\] DEBUG \{.*\}$/',
                $handler->getMessage()
            )
        );
    }

    public function testSystemSuccessEvent()
    {
        $handler = new class extends AbstractProcessingHandler
        {
            private array $record;
            protected function write(array $record): void
            {
                $this->record = $record;
            }

            public function getMessage(): string
            {
                return $this->record['formatted'];
            }
        };

        $handler->setFormatter(new LineFormatter("[%datetime%] %level_name% %message%\n"));

        $event = new SystemSuccessEvent(
            new Request(),
            new Response()
        );

        $logger = (new Logger('aggregator'))->pushHandler($handler);

        $logger->debug((string) $event);

        $this->assertEquals(
            1,
            preg_match(
                '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\.[0-9]{6}\+00:00\] DEBUG \{.*\}$/',
                $handler->getMessage()
            )
        );
    }
}
