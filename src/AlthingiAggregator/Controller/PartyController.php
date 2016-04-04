<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:48 PM
 */

namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Model\Party;

class PartyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPartyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/thingflokkar',
            'thingflokkar',
            '//þingflokkar/þingflokkur',
            new Party()
        );
    }
}
