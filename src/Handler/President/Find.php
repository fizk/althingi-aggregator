<?php
namespace App\Handler\President;

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
        $assemblyNumber = $request->getAttribute('assembly');

        if ($assemblyNumber) {
            $nodeList = $this->queryForNoteList(
                'http://huginn.althingi.is/altext/xml/forsetar/',
                '//forsetalisti/forseti'
            );
            foreach ($nodeList as $president) {
                /** @var $president \DOMElement */
                if ((int) $president->getElementsByTagName('þing')->item(0)->nodeValue === (int)$assemblyNumber) {
                    $this->saveDomElement($president, 'forsetar', new Extractor\President());
                }
            }
        } else {
            $this->queryAndSave(
                'http://huginn.althingi.is/altext/xml/forsetar/',
                'forsetar',
                '//forsetalisti/forseti',
                new Extractor\President()
            );
        }
        return new TextResponse(self::class);
    }
}
