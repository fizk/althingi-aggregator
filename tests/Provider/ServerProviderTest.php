<?php

namespace App\Provider;

use PHPUnit\Framework\TestCase;
use Laminas\Cache\Storage\Adapter\BlackHole;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Provider\ServerProvider;
use ErrorException;
use UnexpectedValueException;

class ServerProviderTest extends TestCase
{
    public function testHtmlEntity()
    {
        $client = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new XmlResponse('<?xml version="1.0" encoding="UTF-8"?>
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
                </nefndarfundir>', 200, ['content-type' => 'text/html, charset=iso-8859-1']);
            }
        };

        $serverProvider = (new ServerProvider())
            ->setHttpClient($client)
            ->setCache(new BlackHole());

        $dom = $serverProvider->get('http://example.com');

        $this->assertInstanceOf(\DOMDocument::class, $dom);
    }

    public function test403()
    {
        $this->expectException(ErrorException::class);

        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(403, ['content-type' => 'text/html, charset=iso-8859-1']);
            }
        };

        $serverProvider = (new ServerProvider())
            ->setHttpClient($client)
            ->setCache(new BlackHole());

        $dom = $serverProvider->get('http://example.com');
    }

    public function testCanNotParseXML()
    {
        $this->expectException(UnexpectedValueException::class);
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new XmlResponse('not a valid xml', 200, [
                    'content-type' => 'text/html, charset=iso-8859-1'
                ]);
            }
        };

        (new ServerProvider())
            ->setHttpClient($client)
            ->setCache(new BlackHole())
            ->get('http://example.com');
    }
}
