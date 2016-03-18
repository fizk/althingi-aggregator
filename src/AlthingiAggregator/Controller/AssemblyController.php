<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:46 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Assembly;
use Zend\Mvc\Controller\AbstractActionController;

class AssemblyController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function currentAssemblyAction()
    {
        $this->singleLevelGet(
            'Assembly:current',
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            'loggjafarthing',
            'þing',
            new Assembly()
        );
    }

    public function findAssemblyAction()
    {
        $this->singleLevelGet(
            'Assembly',
            'http://www.althingi.is/altext/xml/loggjafarthing',
            'loggjafarthing',
            'þing',
            new Assembly()
        );
    }

}
