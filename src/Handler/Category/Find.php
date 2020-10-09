<?php
namespace App\Handler\Category;

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
        $superCategoriesList = $this->queryForNoteList(
            'http://www.althingi.is/altext/xml/efnisflokkar/',
            '//efnisflokkar/yfirflokkur'
        );
        foreach ($superCategoriesList as $superCategory) {
            $this->saveDomElement(
                $superCategory,
                'thingmal/efnisflokkar',
                new Extractor\SuperCategory()
            );

            $superCategoryId = (int) $superCategory->getAttribute('id');

            $categoryList = $superCategory->getElementsByTagName('efnisflokkur');
            foreach ($categoryList as $category) {
                $this->saveDomElement(
                    $category,
                    "thingmal/efnisflokkar/{$superCategoryId}/undirflokkar",
                    new Extractor\Category()
                );
            }
        }
        return new TextResponse(self::class);
    }
}
