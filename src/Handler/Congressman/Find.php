<?php

namespace App\Handler\Congressman;

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

    /**
     * Get Congressman.
     * If additional parameter is passed (--assembly=int) than only congressman
     * for given assembly will be fetched.
     *
     * If no additional params is given, only the congressmen are fetched but not
     * their session in parliment.
     *
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly', null);
        $congressmenUrl = ($assemblyNumber)
            ? "https://www.althingi.is/altext/xml/thingmenn/?lthing={$assemblyNumber}"
            : "https://www.althingi.is/altext/xml/thingmenn/";
        $congressmenElements = $this->queryForNoteList($congressmenUrl, '//þingmannalisti/þingmaður');

        $this->saveDomNodeList(
            $congressmenElements,
            'thingmenn',
            new Extractor\Congressman()
        );

        if ($assemblyNumber) {
            /** @var  $congressmanElement \DOMElement*/
            foreach ($congressmenElements as $congressmanElement) {
                $congressmanId = $congressmanElement->getAttribute('id');
                $congressmanSessionUrl = trim(
                    $congressmanElement->getElementsByTagName('þingseta')->item(0)->nodeValue
                );
                $congressmanCommitteeUrl = trim(
                    $congressmanElement->getElementsByTagName('nefndaseta')->item(0)->nodeValue
                );

                $this->queryAndSave(
                    $congressmanSessionUrl,
                    "thingmenn/{$congressmanId}/thingseta",
                    '//þingmaður/þingsetur/þingseta',
                    new Extractor\Session()
                );

                $this->queryAndSave(
                    $congressmanCommitteeUrl,
                    "thingmenn/{$congressmanId}/nefndaseta",
                    '//þingmaður/nefndasetur/nefndaseta',
                    new Extractor\CommitteeSession()
                );
            }
        }

        return new TextResponse(self::class);
    }
}
