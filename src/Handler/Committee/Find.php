<?php
namespace App\Handler\Committee;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;

class Find implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dom = $this->queryForDocument('https://www.althingi.is/altext/xml/nefndir/');

        $defaultCommitteeElement = $dom->createElement('nefnd');
        $defaultCommitteeElement->setAttribute('id', '0');

        $defaultCommitteeNameElement = $dom->createElement('heiti', 'óskylgreind nefnd');
        $defaultCommitteePeriodElement = $dom->createElement('tímabil');
        $defaultCommitteeFirstPeriodElement = $dom->createElement('fyrstaþing', '1');

        $defaultCommitteeElement->appendChild($defaultCommitteeNameElement);
        $defaultCommitteeElement->appendChild($defaultCommitteePeriodElement);
        $defaultCommitteePeriodElement->appendChild($defaultCommitteeFirstPeriodElement);

        $dom->documentElement->appendChild($defaultCommitteeElement);


        $xPathObject = new \DOMXPath($dom);
        $elements = $xPathObject->query('//nefndir/nefnd');
        $this->saveDomNodeList($elements, 'nefndir', new Extractor\Committee());

        return new TextResponse(self::class);
    }
}
