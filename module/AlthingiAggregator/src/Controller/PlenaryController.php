<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:50 PM
 */

namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Plenary;

class PlenaryController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPlenaryAction()
    {
        $assemblyNumber = $this->params('assembly');

        $this->queryAndSave(
            "http://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}",
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            '//þingfundir/þingfundur',
            new Plenary()
        );
    }
}
