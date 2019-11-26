<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor;

class AssemblyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function currentAssemblyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            'loggjafarthing',
            '//löggjafarþing/þing',
            new Extractor\Assembly()
        );
    }

    public function findAssemblyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/loggjafarthing',
            'loggjafarthing',
            '//löggjafarþing/þing',
            new Extractor\Assembly()
        );
    }
}
