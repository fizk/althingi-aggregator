<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Constituency;

class ConstituencyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findConstituencyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/kjordaemi',
            'kjordaemi',
            '//kjördæmin/kjördæmið',
            new Constituency()
        );
    }
}
