<?php

namespace App\Handler\ParliamentarySession;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;

class Agenda implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');

        $list = $this->queryForNoteList(
            "https://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}",
            '//þingfundir/þingfundur'
        );

        foreach ($list as $item) {
            $agendaUrl = $item->getElementsByTagName('dagskrá')
                ->item(0)->getElementsByTagName('xml')->item(0)->nodeValue;
            $parliamentarySessionNumber = $item->getAttribute('númer');
            $items = $this->queryForNoteList($agendaUrl, '//dagskráþingfundar/þingfundur/dagskrá/dagskrárliður');
            foreach ($items as $agendaItem) {
                $this->saveDomElement(
                    $agendaItem,
                    "loggjafarthing/{$assemblyNumber}/thingfundir/{$parliamentarySessionNumber}/lidir",
                    new Extractor\ParliamentarySessionAgenda()
                );
            }
        }

        return new TextResponse(self::class);
    }
}
