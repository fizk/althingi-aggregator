<?php
namespace AlthingiAggregator\Controller;

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
}
