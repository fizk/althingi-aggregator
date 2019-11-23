<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class ConstituencyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findConstituencyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/kjordaemi',
            'kjordaemi',
            '//kjördæmin/kjördæmið',
            new Extractor\Constituency()
        );
    }
}
