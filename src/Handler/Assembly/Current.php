<?php

namespace App\Handler\Assembly;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Extractor\Assembly;
use App\Handler\ConsoleHelper;

class Current implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->queryAndSave(
            'https://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi/',
            'loggjafarthing',
            '//löggjafarþing/þing',
            new Assembly()
        );

        return new TextResponse(self::class);
    }
}
