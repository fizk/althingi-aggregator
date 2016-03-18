<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Issue;
use AlthingiAggregator\Model\Dom\Proponent;
use AlthingiAggregator\Model\Dom\Speech;
use Zend\Mvc\Controller\AbstractActionController;

class IssueController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    public function findIssueAction()
    {
        $assemblyNumber = $this->params('assembly');

        $this->getLogger()->info("Issue -- start");

        $issues = $this->queryForNoteList(
            "http://www.althingi.is/altext/xml/thingmalalisti/?lthing={$assemblyNumber}",
            '//málaskrá/mál'
        );

        foreach ($issues as $issue) {
            $issueNumber = $issue->getAttribute('málsnúmer');
            $this->processEachIssue($issue, $assemblyNumber, $issueNumber);
        }

        $this->getLogger()->info("Issue -- end");
    }

    private function processEachIssue(\DOMElement $element, $assemblyNumber, $issueNumber)
    {
        $dom = $this->queryForDocument(
            $element->getElementsByTagName('xml')->item(0)->nodeValue
        );

        //ISSUE
        $issue = $dom->getElementsByTagName('mál')->item(0);
        $this->singleElementProcess(
            $issue,
            "loggjafarthing/{$assemblyNumber}/thingmal",
            new Issue()
        );

        //Get xml path to first associated document
        $documentsLinkXPath = new \DOMXPath($dom);
        /** @var  $el\DOMNodeList */
        $documentsNodeList = $documentsLinkXPath->query('//þingmál/þingskjöl/þingskjal[1]/slóð/xml');
        if ($documentsNodeList->length > 0) {
            $documentsDom = $this->queryForDocument($documentsNodeList->item(0)->nodeValue);
            $documentsXPath = new \DOMXPath($documentsDom);
            $congressmenNodeList = $documentsXPath->query('//þingskjal/þingskjal[1]/flutningsmenn[1]/flutningsmaður');

            foreach ($congressmenNodeList as $congressman) { //TODO not implemented in server
                $this->singleElementProcess(
                    $congressman,
                    "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/flutningsmenn",
                    new Proponent()
                );
            }
        }

        //SPEECH
        $speeches = $dom->getElementsByTagName('ræður')->item(0);
        foreach ($speeches->getElementsByTagName('ræða') as $item) {
            $this->processEachSpeech($item, $assemblyNumber, $issueNumber);
        }
    }

    private function processEachSpeech(\DOMElement $item, $assemblyNumber, $issueNumber)
    {
        $speechDocument = $this->buildSpeechDocument($item);

        $this->getLogger()->info('Storing one Speech');
        $this->singleElementProcess(
            $speechDocument->documentElement,
            "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/raedur",
            new Speech()
        );
        $this->getLogger()->info('end of Storing one Speech');

        $speechDocument = null;
    }

    /**
     * Speech is a complicated thing. The data is in two places, the list (xml) of speeches
     * has the speaker, begin and end date.
     * The actual speech xml has the text. These two documents have to be combined.
     *
     * @param \DOMElement $item
     * @return \DOMDocument
     */
    private function buildSpeechDocument(\DOMElement $item)
    {
        $speechDocument = new \DOMDocument();
        $speechMetaElement = $speechDocument->importNode($item, true);
        $speechDocument->appendChild($speechMetaElement);

        if ($item->getElementsByTagName('xml')->item(0)) {

            $speechDom = $this->queryForDocument($item->getElementsByTagName('xml')->item(0)->nodeValue);
            $speechEl = $speechDom->getElementsByTagName('ræðutexti')->item(0);
            $speechBodyElement = $speechDocument->importNode($speechEl, true);
            $speechDocument->documentElement->appendChild($speechBodyElement);

            $issueEl = $speechDom->getElementsByTagName('mál')->item(0);
            $issueElement = $speechDocument->importNode($issueEl, true);
            $speechDocument->documentElement->appendChild($issueElement);
        }

        return $speechDocument;
    }

}
