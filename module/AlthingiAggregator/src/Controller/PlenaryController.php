<?php
namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\PlenaryAgenda;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Plenary;

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
            new Plenary()
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
                    new PlenaryAgenda()
                );
            }
        }
    }
}
