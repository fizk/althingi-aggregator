<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:48 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Party;
use Zend\Mvc\Controller\AbstractActionController;

class PartyController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function findPartyAction()
    {
        $this->singleLevelGet(
            'Party',
            'http://www.althingi.is/altext/xml/thingflokkar',
            'thingflokkar',
            'þingflokkur',
            new Party()
        );
    }
}
