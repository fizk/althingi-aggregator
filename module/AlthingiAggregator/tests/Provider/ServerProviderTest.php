<?php
namespace AlthingiAggregatorTest\Provider;

use AlthingiAggregator\Provider\ServerProvider;
use AlthingiAggregatorTest\Helpers\LogTrait;
use Monolog\Handler\NullHandler;
use PHPUnit\Framework\TestCase;
use Zend\Cache\Storage\Adapter\Memory;
use Monolog\Logger;
use Monolog\Handler;

class ServerProviderTest extends TestCase
{
    use LogTrait;

    /**
     * @throws \Exception
     */
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
                             
                <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=150&amp;malnr=1</xml>
                             
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

        $testLogHandler = new Handler\TestHandler();

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

    public function testLogs403()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 403 Forbidden"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n"
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        $client = new \Zend\Http\Client();
        $client->setAdapter($adapter);

        $serverProvider = (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache(new Memory());

        try {
            $dom = $serverProvider->get('http://example.com');
            $this->assertInstanceOf(\DOMDocument::class, $dom);
        } catch (\Throwable $e) {
            $this->assertLogHandler($testLogHandler);
        }
    }

    public function testLogs200()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 200 OK"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n" .

            '<?xml version="1.0" encoding="UTF-8"?>
             <root />
            '
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = new Logger('logger');
        $logger->setHandlers([$testLogHandler]);

        $client = new \Zend\Http\Client();
        $client->setAdapter($adapter);

        (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache(new Memory())
            ->get('http://example.com');

        $this->assertLogHandler($testLogHandler);
    }

    public function testLogs500()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 500 Internal Server Error"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n" .

            '<?xml version="1.0" encoding="UTF-8"?>
             <root />
            '
        );

        $testLogHandler = new Handler\TestHandler();

        $logger = (new Logger('logger'))
            ->setHandlers([$testLogHandler]);

        $client = (new \Zend\Http\Client())
            ->setAdapter($adapter);

        (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache(new Memory())
            ->get('http://example.com');

        $this->assertLogHandler($testLogHandler);
    }

    public function testLogsCache()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 200 OK"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n" .

            '<?xml version="1.0" encoding="UTF-8"?>
             <root />
            '
        );

        $url = 'http://example.com';

        $testLogHandler = new Handler\TestHandler();

        $logger = (new Logger('logger'))
            ->setHandlers([$testLogHandler]);

        $client = (new \Zend\Http\Client())
            ->setAdapter($adapter);

        $cache = (new Memory());
        $cache->addItem(md5($url), '<?xml version="1.0" encoding="UTF-8"?><root />');

        (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->get($url);

        $this->assertLogHandler($testLogHandler);
    }

    /**
     * @expectedException \Exception
     */
    public function testCanNotParseXML()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $adapter->setResponse(
            "HTTP/1.1 200 OK"      . "\r\n" .
            "Content-Type: text/xml; charset=utf-8" . "\r\n" .
            "\r\n" .

            'not valid xml'
        );

        $url = 'http://example.com';

        $testLogHandler = new Handler\TestHandler();

        $logger = (new Logger('logger'))
            ->setHandlers([$testLogHandler]);

        $client = (new \Zend\Http\Client())
            ->setAdapter($adapter);

        (new ServerProvider())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache(new Memory())
            ->get($url);
    }
}
