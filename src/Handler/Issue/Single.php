<?php
namespace App\Handler\Issue;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Consumer\ConsumerAwareInterface;
use App\Handler\ConsoleHelper;
use App\Provider\ProviderAwareInterface;

class Single implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;
    use IssueUtilities;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');
        $issueNumber = $request->getAttribute('issue');
        $category = $request->getAttribute('category');
        $url = 'http://www.althingi.is/altext/xml/thingmalalisti';
        $this->queryIssueInformation(
            $assemblyNumber,
            $issueNumber,
            $category === 'B'
                ? "{$url}/bmal/?lthing={$assemblyNumber}&malnr={$issueNumber}"
                : "{$url}/thingmal/?lthing={$assemblyNumber}&malnr={$issueNumber}",
            $category
        );

        return new TextResponse(self::class);
    }
}
