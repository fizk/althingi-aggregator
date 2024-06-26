<?php

namespace App\Handler\Committee;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;

class Assembly implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');

        $meetings = $this->queryForNoteList(
            "https://www.althingi.is/altext/xml/nefndarfundir/?lthing={$assemblyNumber}",
            '//nefndarfundir/nefndarfundur'
        );

        foreach ($meetings as $meeting) {
            $meetingId = (int) $meeting->getAttribute('númer');
            $committeeId = (int) $meeting->getElementsByTagName('nefnd')->item(0)->getAttribute('id');

            $meetingElement = $this->queryForDocument(
                "http://www.althingi.is/altext/xml/nefndarfundir/nefndarfundur/?dagskrarnumer={$meetingId}"
            );

            $this->saveDomElement(
                $meetingElement->documentElement,
                "loggjafarthing/{$assemblyNumber}/nefndir/{$committeeId}/nefndarfundir",
                new Extractor\CommitteeMeeting()
            );

            $this->saveDomNodeList(
                $meetingElement->getElementsByTagName('dagskrárliður'),
                "loggjafarthing/{$assemblyNumber}/nefndir/{$committeeId}/nefndarfundir/{$meetingId}/dagskralidir",
                new Extractor\CommitteeAgenda()
            );
        }
        return new TextResponse(self::class);
    }
}
