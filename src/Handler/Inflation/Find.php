<?php
namespace App\Handler\Inflation;

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
        $date = $request->getAttribute('date', "1930-01-01");

        $list = $this->queryForNoteList(
            "https://www.sedlabanki.is/xmltimeseries/Default.aspx?GroupID=3&Type=xml&DagsFra={$date}&TimeSeriesID=2",
            '//Group/TimeSeries[2]/TimeSeriesData/Entry'
        );

        foreach ($list as $item) {
            $this->saveDomElement(
                $item,
                'verdbolga',
                new Extractor\Inflation()
            );
        }
        return new TextResponse(self::class);
    }
}
