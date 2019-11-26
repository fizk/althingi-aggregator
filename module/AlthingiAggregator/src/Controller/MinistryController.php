<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class MinistryController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findMinistryAction()
    {
        $this->queryAndSave(
            'https://www.althingi.is/altext/xml/radherraembaetti',
            'radherraembaetti',
            '//ráðherrar/ráðherraembætti',
            new Extractor\Ministry()
        );
    }
}
