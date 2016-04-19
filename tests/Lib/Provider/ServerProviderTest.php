<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 12/04/2016
 * Time: 3:26 PM
 */

namespace AlthingiAggregator\Lib\Provider;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Test;

class ServerProviderTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleRequest()
    {
        $logger = (new Logger('test'))
            ->setHandlers([new NullHandler()]);

        $testClientAdapter = new Test();
        $testClientAdapter->setResponse($this->createValidHttpResponse());
        $client = (new Client())
            ->setAdapter($testClientAdapter);

        $serverProvider = (new ServerProvider(['save'=> false]))
            ->setClient($client)
            ->setLogger($logger);

        $domDocument = $serverProvider->get('hudnur.is');

        $this->assertInstanceOf('DOMDocument', $domDocument);
    }

    public function testSaveCacheToDisk()
    {
        $root = vfsStream::setup('./');

        $logger = (new Logger('test'))
            ->setHandlers([new NullHandler()]);

        $testClientAdapter = new Test();
        $testClientAdapter->setResponse($this->createValidHttpResponse());
        $client = (new Client())
            ->setAdapter($testClientAdapter);

        $serverProvider = (new ServerProvider(['save'=> true]))
            ->setClient($client)
            ->setLogger($logger);

        $serverProvider->get('http://gaman.is/skra');
    }

    private function createValidHttpResponse()
    {
        return
            "HTTP/1.1 200 OK\r\n\r\n" .
            "<?xml version=\"1.0\" ?>\r\n" .
            "<root />";
    }
}
