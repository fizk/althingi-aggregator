<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Model\Session;
use AlthingiAggregator\Model\Congressman;

class CongressmanController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    /**
     * Get Congressman.
     * If additional parameter is passed (--assembly=int) than only congressman
     * for given assembly will be fetched.
     *
     * @throws \Exception
     */
    public function findCongressmanAction()
    {
        $assemblyNumber = $this->params('assembly');
        $congressmenUrl = ($assemblyNumber)
            ? "http://www.althingi.is/altext/xml/thingmenn/?lthing={$assemblyNumber}"
            : "http://www.althingi.is/altext/xml/thingmenn/";
        $congressmenElements = $this->queryForNoteList($congressmenUrl, '//þingmannalisti/þingmaður');

        $this->saveDomNodeList(
            $congressmenElements,
            'thingmenn',
            new Congressman()
        );

        foreach ($congressmenElements as $congressmanElement) {
            $congressmanId = $congressmanElement->getAttribute('id');
            $congressmanSessionUrl = $congressmanElement->getElementsByTagName('þingseta')->item(0)->nodeValue;

            $this->queryAndSave(
                $congressmanSessionUrl,
                "thingmenn/{$congressmanId}/thingseta",
                '//þingmaður/þingsetur/þingseta',
                new Session()
            );
        }
    }
}
