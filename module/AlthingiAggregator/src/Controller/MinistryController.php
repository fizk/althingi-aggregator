<?php
namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\Ministry;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;

class MinistryController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findMinistryAction()
    {
        $this->queryAndSave(
            'https://www.althingi.is/altext/xml/radherraembaetti',
            'radherraembaetti',
            '//ráðherrar/ráðherraembætti',
            new Ministry()
        );
    }
}
