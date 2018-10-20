<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\President;

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
            new President()
        );
    }
}
