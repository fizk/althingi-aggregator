<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class PlenaryController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPlenaryAction()
    {
        $assemblyNumber = $this->params('assembly');

        $this->queryAndSave(
            "http://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}",
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            '//þingfundir/þingfundur',
            new Extractor\Plenary()
        );
    }

    public function findPlenaryAgendaAction()
    {
        $assemblyNumber = $this->params('assembly');

        $list = $this->queryForNoteList(
            "http://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}",
            '//þingfundir/þingfundur'
        );

        foreach ($list as $item) {
            $agendaUrl = $item->getElementsByTagName('dagskrá')
                ->item(0)->getElementsByTagName('xml')->item(0)->nodeValue;
            $plenaryNumber = $item->getAttribute('númer');
            $items = $this->queryForNoteList($agendaUrl, '//dagskráþingfundar/þingfundur/dagskrá/dagskrárliður');
            foreach ($items as $agendaItem) {
                $this->saveDomElement(
                    $agendaItem,
                    "loggjafarthing/{$assemblyNumber}/thingfundir/{$plenaryNumber}/lidir",
                    new Extractor\PlenaryAgenda()
                );
            }
        }
    }
}
