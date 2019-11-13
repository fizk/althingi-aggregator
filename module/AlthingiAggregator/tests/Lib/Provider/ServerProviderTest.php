<?php
namespace AlthingiAggregatorTest\Lib\Provider;

use AlthingiAggregator\Lib\Provider\ServerProvider;
use Monolog\Handler\NullHandler;
use PHPUnit\Framework\TestCase;
use Zend\Cache\Storage\Adapter\Memory;
use Monolog\Logger;

class ServerProviderTest extends TestCase
{
    public function testHtmlEntity()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 200 OK"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n" .

            '<?xml version="1.0" encoding="UTF-8"?>
                <nefndarfundir>
                    <nefndarfundur númer=\'13025\' þingnúmer=\'143\'>
                        <nefnd id=\'205\'>velferðarnefnd &nbsp;</nefnd>
                        <tegundFundar></tegundFundar>
                        <staður>í Austurstræti 8&ndash;10</staður>
                        <hefst>
                             &amp;, &quot;, &lt;, &gt;, &apos;
                             &quot;
                             
                            <tag>&apos;</tag>
                            <texti> 2. október 13, kl.  9:30 árdegis</texti>
                            <dagur>2013-10-02</dagur>
                            <timi>09:30</timi>
                            <dagurtími>2013-10-02T09:30:00</dagurtími>
                        </hefst>
                        <staður>í Austurstræti 8&ndash;10</staður>
                    </nefndarfundur>
                </nefndarfundir>
            '
        );

        $logger = new Logger('logger');
        $logger->setHandlers([new NullHandler()]);

        $client = new \Zend\Http\Client();
        $client->setAdapter($adapter);

        $serverProvider = (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache(new Memory());

        $dom = $serverProvider->get('http://example.com');

        $this->assertInstanceOf(\DOMDocument::class, $dom);
    }

    /**
     * @expectedException \Exception
     */
    public function test403()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 403 Forbidden"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n"
        );

        $client = new \Zend\Http\Client();
        $client->setAdapter($adapter);

        $serverProvider = (new ServerProvider())
            ->setClient($client)
            ->setLogger((new Logger('logger')))
            ->setCache(new Memory());

        $dom = $serverProvider->get('http://example.com');

        $this->assertInstanceOf(\DOMDocument::class, $dom);
    }
}
