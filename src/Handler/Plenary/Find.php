<?php
namespace App\Handler\Plenary;

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

        $this->queryAndSave(
            "http://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}",
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            '//þingfundir/þingfundur',
            new Extractor\Plenary()
        );
        return new TextResponse(self::class);
    }
}
