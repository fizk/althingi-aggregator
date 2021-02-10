<?php
namespace App\Handler\Issue;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Consumer\ConsumerAwareInterface;
use App\Handler\ConsoleHelper;
use App\Provider\ProviderAwareInterface;

class Find implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;
    use IssueUtilities;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');

        $issuesNodeList = $this->queryNoteList(
            "https://www.althingi.is/altext/xml/thingmalalisti/?lthing={$assemblyNumber}",
            '//málaskrá/mál'
        );

        foreach ($issuesNodeList as $issueElement) {
            $issueNumber = $issueElement->getAttribute('málsnúmer');
            $issueUrl = $issueElement->getElementsByTagName('xml')->item(0)->nodeValue;
            $this->queryIssueInformation(
                $assemblyNumber,
                $issueNumber,
                $issueUrl,
                $issueElement->getAttribute('málsflokkur')
            );
        }
        return new TextResponse(self::class);
    }
}
