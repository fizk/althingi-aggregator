<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Assembly;

class AssemblyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function currentAssemblyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            'loggjafarthing',
            '//löggjafarþing/þing',
            new Assembly()
        );
    }

    public function findAssemblyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/loggjafarthing',
            'loggjafarthing',
            '//löggjafarþing/þing',
            new Assembly()
        );
    }
}
