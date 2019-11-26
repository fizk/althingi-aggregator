<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class PresidentController extends AbstractActionController implements
    ConsumerAwareInterface,
    ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPresidentAction()
    {
        $this->queryAndSave(
            'http://huginn.althingi.is/altext/xml/forsetar/',
            'forsetar',
            '//forsetalisti/forseti',
            new Extractor\President()
        );
    }
}
