<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:50 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Plenary;
use AlthingiAggregator\Lib\Http\DomClient;
use Zend\Mvc\Controller\AbstractActionController;

class PlenaryController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function findPlenaryAction()
    {
        $assemblyNumber = $this->params('assembly');
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get("http://www.althingi.is/altext/xml/thingfundir/?lthing={$assemblyNumber}");

        foreach ($dom->getElementsByTagName('þingfundur') as $element) {
            $element->setAttribute('þing', $assemblyNumber);
        }

        $this->getLogger()->info("Plenary -- start");
        $this->singleLevelPut(
            $dom,
            "loggjafarthing/{$assemblyNumber}/thingfundir",
            'þingfundur',
            new Plenary()
        );
        $this->getLogger()->info("Plenary -- end");
    }
}
