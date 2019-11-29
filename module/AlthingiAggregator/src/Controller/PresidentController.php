<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class PresidentController extends AbstractActionController implements
    ConsumerAwareInterface,
    ProviderAwareInterface
{
    use ConsoleHelper;

    public function findPresidentAction()
    {
        $assemblyNumber = $this->params('assembly');

        if ($assemblyNumber) {
            $nodeList = $this->queryForNoteList(
                'http://huginn.althingi.is/altext/xml/forsetar/',
                '//forsetalisti/forseti'
            );
            foreach ($nodeList as $president) {
                /** @var $president \DOMElement */
                if ((int) $president->getElementsByTagName('þing')->item(0)->nodeValue === (int)$assemblyNumber) {
                    $this->saveDomElement($president, 'forsetar', new Extractor\President());
                }
            }
        } else {
            $this->queryAndSave(
                'http://huginn.althingi.is/altext/xml/forsetar/',
                'forsetar',
                '//forsetalisti/forseti',
                new Extractor\President()
            );
        }
    }
}
