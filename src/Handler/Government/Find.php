<?php
namespace App\Handler\Government;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Callback\GovernmentDocumentCallback;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;

class Find implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $list = $this->queryForNoteList(
            'https://www.stjornarradid.is/rikisstjorn/sogulegt-efni/rikisstjornartal/',
            '//root/item',
            new GovernmentDocumentCallback()
        );

        $this->saveDomNodeList($list, 'raduneyti', new Extractor\Government());
        return new TextResponse(self::class);
    }
}
