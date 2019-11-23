<?php
namespace AlthingiAggregatorTest\Consumer;

use AlthingiAggregatorTest\Helpers\LogTrait;
use AlthingiAggregator\Consumer\HttpConsumer;
use AlthingiAggregatorTest\Consumer\Stub\ExtractorValidPut;
use AlthingiAggregatorTest\Consumer\Stub\ExtractorValidPost;
use Monolog\Logger;
use Monolog\Handler;
use PHPUnit\Framework\TestCase;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Http\Client\Adapter;
use Zend\Http\Client;
use Zend\Uri\Http;

class HttpConsumerLogTest extends TestCase
{
    use LogTrait;

    /**
     * @throws \Exception
     */
    public function testLogs()
    {
        $adapter = new Adapter\Test();
        $adapter->setNextRequestWillFail(true);
        $adapter->setResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs200()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 201 Created'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs400()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs400NoLocation()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs400Cache()
    {
        $adapter = new Adapter\Test();

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        $host = new Http('http://localhost:8080');
        $value = ['key' => 'value'];

        $cache = new Memory();
        $cache->addItem(
            HttpConsumer::createStorageKey((clone $host)->setPath('/this/key')),
            HttpConsumer::createStorageValue($value)
        );

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri($host)
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost($value));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs200Put()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 201 Created'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs400Put()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs500Put()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs500PCache()
    {

        $adapter = new Adapter\Test();

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        $host = new Http('http://localhost:8080');
        $value = ['key' => 'value'];

        $cache = new Memory();
        $cache->addItem(
            HttpConsumer::createStorageKey((clone $host)->setPath('/this/key/1')),
            HttpConsumer::createStorageValue($value)
        );

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri($host)
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut($value));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs200PatchSuccess()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );
        $adapter->addResponse(
            'HTTP/1.1 201 Created'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @throws \Exception
     */
    public function testLogs200PatchError()
    {
        $adapter = new Adapter\Test();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );
        $adapter->addResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: application/json' . "\r\n\r\n" .
            '{"message": "some crazy message"}'
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        (new HttpConsumer())
            ->setClient((new Client())->setAdapter($adapter))
            ->setLogger($logger)
            ->setCache(new Memory())
            ->setUri((new Http('http://localhost:8080')))
            ->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut(['key' => 'value']));

        $this->assertLogHandler($testLogHandler);
    }
}
