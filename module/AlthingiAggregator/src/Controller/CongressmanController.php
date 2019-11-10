<?php
namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\CommitteeSitting;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Session;
use AlthingiAggregator\Extractor\Congressman;

class CongressmanController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    /**
     * Get Congressman.
     * If additional parameter is passed (--assembly=int) than only congressman
     * for given assembly will be fetched.
     *
     * If no additional params is given, only the congressmen are fetched but not
     * their session in parliment.
     *
     * @throws \Exception
     */
    public function findCongressmanAction()
    {
        $assemblyNumber = $this->params('assembly', null);
        $congressmenUrl = ($assemblyNumber)
            ? "http://www.althingi.is/altext/xml/thingmenn/?lthing={$assemblyNumber}"
            : "http://www.althingi.is/altext/xml/thingmenn";
        $congressmenElements = $this->queryForNoteList($congressmenUrl, '//þingmannalisti/þingmaður');

        $this->saveDomNodeList(
            $congressmenElements,
            'thingmenn',
            new Congressman()
        );

        if ($assemblyNumber) {
            /** @var  $congressmanElement \DOMElement*/
            foreach ($congressmenElements as $congressmanElement) {
                $congressmanId = $congressmanElement->getAttribute('id');
                $congressmanSessionUrl = trim(
                    $congressmanElement->getElementsByTagName('þingseta')->item(0)->nodeValue
                );
                $congressmanCommitteeUrl = trim(
                    $congressmanElement->getElementsByTagName('nefndaseta')->item(0)->nodeValue
                );

                $this->queryAndSave(
                    $congressmanSessionUrl,
                    "thingmenn/{$congressmanId}/thingseta",
                    '//þingmaður/þingsetur/þingseta',
                    new Session()
                );

                $this->queryAndSave(
                    $congressmanCommitteeUrl,
                    "thingmenn/{$congressmanId}/nefndaseta",
                    '//þingmaður/nefndasetur/nefndaseta',
                    new CommitteeSitting()
                );
            }
        }
    }
}
