<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:49 PM
 */

namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Model\Constituency;

class ConstituencyController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findConstituencyAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/kjordaemi',
            'kjordaemi',
            '//kjördæmin/kjördæmið',
            new Constituency()
        );
    }
}
