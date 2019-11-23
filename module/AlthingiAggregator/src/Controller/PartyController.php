<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class PartyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPartyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/thingflokkar',
            'thingflokkar',
            '//þingflokkar/þingflokkur',
            new Extractor\Party()
        );
    }
}
