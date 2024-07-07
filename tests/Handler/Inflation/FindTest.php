<?php

namespace App\Handler\Inflation;

use App\Consumer\ConsumerInterface;
use App\Extractor\ExtractionInterface;
use App\Provider\ProviderInterface;
use DOMDocument;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;

class FindTest extends TestCase
{
    public function testTrue()
    {
        /** @var \App\Handler\Inflation\Find */
        $handler = (new Find())
            ->setConsumer(new class implements ConsumerInterface {
                public function save(string $storageKey, ExtractionInterface $extract) {

                }
            })
            ->setProvider(new class implements ProviderInterface {
                public function get(string $url, callable $cb = null): DOMDocument
                {
                    $content = file_get_contents('tests/data/inflation.xml');
                    $dom =  new DOMDocument();
                    $dom->loadXML($content);
                    return $dom;
                }
            })
        ;
        $handler->handle(new ServerRequest());
        $this->assertTrue(true);
    }
}
