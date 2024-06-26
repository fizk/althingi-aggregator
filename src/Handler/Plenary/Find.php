<?php

namespace App\Handler\Plenary;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;
use DOMDocument;

class Find implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');

        $dom = $this->queryForDocument(
            "https://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}"
        );
        $xPathObject = new \DOMXPath($dom);
        $elements = $xPathObject->query('//þingfundir/þingfundur');

        // IF there are no Plenary items, make them up and give
        //  them the ID of 0
        // PlenaryAgenda and Speech items will reference this made-up item
        if (count($elements) === 0) {
            $dom = new DOMDocument();
            $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>
                <þingfundir>
                    <þingfundur númer="0">
                        <fundarheiti>sameinaðir þingsetningarfundir</fundarheiti>
                    </þingfundur>
                </þingfundir>
            ');
            $elements = $xPathObject->query('//þingfundir/þingfundur');
        }

        $this->saveDomNodeList(
            $elements,
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            new Extractor\Plenary()
        );

        return new TextResponse(self::class);
    }
}
