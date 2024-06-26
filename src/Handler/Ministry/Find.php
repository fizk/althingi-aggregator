<?php

namespace App\Handler\Ministry;

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
        $this->queryAndSave(
            'https://www.althingi.is/altext/xml/radherraembaetti/',
            'radherraembaetti',
            '//ráðherrar/ráðherraembætti',
            new Extractor\Ministry()
        );
        return new TextResponse(self::class);
    }
}
