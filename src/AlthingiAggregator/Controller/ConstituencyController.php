<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:49 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Constituency;
use Zend\Mvc\Controller\AbstractActionController;

class ConstituencyController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function findConstituencyAction()
    {
        $this->singleLevelGet(
            'Constituency',
            'http://www.althingi.is/altext/xml/kjordaemi',
            'kjordaemi',
            'kjördæmið',
            new Constituency()
        );
    }
}
