<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace AlthingiAggregator\Controller;

use DOMElement;
use DOMXPath;
use DOMDocument;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;
use AlthingiAggregator\Extractor\Document;
use AlthingiAggregator\Extractor\IssueCategory;
use AlthingiAggregator\Extractor\Issue;
use AlthingiAggregator\Extractor\Proponent;
use AlthingiAggregator\Extractor\Speech;
use AlthingiAggregator\Extractor\Vote;
use AlthingiAggregator\Extractor\VoteItem;

class IssueController extends AbstractActionController implements
    ConsumerAwareInterface,
    ProviderAwareInterface
{
    use ConsoleHelper;

    public function findIssueAction()
    {
        $assemblyNumber = $this->params('assembly');

        $issuesNodeList = $this->queryForNoteList(
            "http://www.althingi.is/altext/xml/thingmalalisti/?lthing={$assemblyNumber}",
            '//málaskrá/mál'
        );

        foreach ($issuesNodeList as $issueElement) {
            $issueNumber = $issueElement->getAttribute('málsnúmer');
            $issueUrl = $issueElement->getElementsByTagName('xml')->item(0)->nodeValue;

            if ($issueElement->getAttribute('málsflokkur') === 'A') {
                $issueDocumentDom = $this->queryForDocument($issueUrl);
                $issueDocumentXPath = new DOMXPath($issueDocumentDom);

                $this->processIssue($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processIssueCategory($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processDocuments($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processVotes($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processProponents($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processSpeeches($assemblyNumber, $issueNumber, $issueDocumentXPath);
            } elseif ($issueElement->getAttribute('málsflokkur') === 'B') {
                $issueDocumentDom = $this->queryForDocument($issueUrl);
                $issueDocumentXPath = new DOMXPath($issueDocumentDom);

                $this->processUndocumentedIssue($assemblyNumber, $issueNumber, $issueDocumentXPath);
                $this->processUndocumentedSpeeches($assemblyNumber, $issueNumber, $issueDocumentXPath);
            }
        }
    }

    private function processIssue($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $issue = $xPath->query('//þingmál/mál')->item(0);

        $proponent = $xPath->query('//þingmál/framsögumenn/framsögumaður');
        $proponentId = $proponent->length
            ? $proponent->item(0)->getAttribute('id')
            : null;
        if ($proponentId) {
            $issue->setAttribute('framsögumaður', $proponentId);
        }

        $summaryDoc = $this->queryForDocument(
            "http://www.althingi.is/altext/xml/samantektir/samantekt/?lthing={$assemblyNumber}&malnr={$issueNumber}"
        );
        $summaryElements = [
            'markmið',
            'helstuBreytingar',
            'breytingaráLögum',
            'kostnaðurOgTekjur',
            'afgreiðsla',
            'aðrarUpplýsingar'
        ];
        foreach ($summaryElements as $element) {
            $placeholderElement = null;
            if ($summaryDoc->getElementsByTagName($element)->item(0)) {
                $externalElement = $summaryDoc->getElementsByTagName($element)->item(0);
                $placeholderElement = $issue->ownerDocument->importNode($externalElement, true);
            } else {
                $placeholderElement = $issue->ownerDocument->createElement($element);
            }
            $issue->appendChild($placeholderElement);
        }

        $this->saveDomElement(
            $issue,
            "loggjafarthing/{$assemblyNumber}/thingmal",
            new Issue()
        );
    }

    private function processIssueCategory($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $categories = $xPath->query('//þingmál/efnisflokkar/yfirflokkur/efnisflokkur');
        $this->saveDomNodeList(
            $categories,
            "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/efnisflokkar",
            new IssueCategory()
        );
    }

    private function processDocuments($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $documentsNodeList = $xPath->query('//þingmál/þingskjöl/þingskjal');

        $this->saveDomNodeList(
            $documentsNodeList,
            "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/thingskjal",
            new Document()
        );
    }

    private function processVotes($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $votesNodeList = $xPath->query('//þingmál/atkvæðagreiðslur/atkvæðagreiðsla/slóð/xml');

        foreach ($votesNodeList as $voteNode) {
            $voteItemDocumentDom = $this->queryForDocument($voteNode->nodeValue);
            $this->saveDomElement(
                $voteItemDocumentDom->documentElement,
                "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/atkvaedagreidslur",
                new Vote()
            );

            $voteNumber = (int) $voteItemDocumentDom->documentElement->getAttribute('atkvæðagreiðslunúmer');

            foreach ($voteItemDocumentDom->getElementsByTagName('þingmaður') as $vote) {
                $this->saveDomElement(
                    $vote,
                    "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/atkvaedagreidslur/{$voteNumber}/atkvaedi",
                    new VoteItem()
                );
            }
        }
    }

    private function processProponents($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $documentsNodeList = $xPath->query('//þingmál/þingskjöl/þingskjal/slóð/xml');

        foreach ($documentsNodeList as $documentNodeElement) {
            $documentsDom = $this->queryForDocument($documentNodeElement->nodeValue);
            $documentsXPath = new DOMXPath($documentsDom);
            $documentId = $documentsXPath->query('//þingskjal/þingskjal')->item(0)->getAttribute('skjalsnúmer');
            $congressmenNodeList = $documentsXPath->query('//þingskjal/þingskjal/flutningsmenn/flutningsmaður');

            foreach ($congressmenNodeList as $congressman) {
                $this->saveDomElement(
                    $congressman,
                    "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/thingskjal/{$documentId}/flutningsmenn",
                    new Proponent()
                );
            }
        }
    }

    private function processSpeeches($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $speeches = $xPath->query('//þingmál/ræður/ræða');
        foreach ($speeches as $item) {
            $speechDocument = $this->buildSpeechDocument($item, $issueNumber);

            $this->saveDomElement(
                $speechDocument->documentElement,
                "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/raedur",
                new Speech()
            );
        }
    }

    private function processUndocumentedIssue($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $issue = $xPath->query('//bmál/mál')->item(0);
        $this->saveDomElement(
            $issue,
            "loggjafarthing/{$assemblyNumber}/bmal",
            new Issue()
        );
    }

    private function processUndocumentedSpeeches($assemblyNumber, $issueNumber, DOMXPath $xPath)
    {
        $speeches = $xPath->query('//bmál/ræður/ræða');
        foreach ($speeches as $item) {
            $speechDocument = $this->buildSpeechDocument($item, $issueNumber);

            $this->saveDomElement(
                $speechDocument->documentElement,
                "loggjafarthing/{$assemblyNumber}/thingmal/{$issueNumber}/raedur",
                new Speech()
            );
        }
    }

    /**
     * Speech is a complicated thing. The data is in two places, the list (xml) of speeches
     * has the speaker, begin and end date.
     * The actual speech xml has the text. These two documents have to be combined.
     *
     * @param \DOMElement $item
     * @return \DOMDocument
     */
    private function buildSpeechDocument(DOMElement $item, $issueId)
    {
        $speechDocument = new DOMDocument();
        $speechMetaElement = $speechDocument->importNode($item, true);
        $speechDocument->appendChild($speechMetaElement);
        $speechDocument->documentElement->setAttribute('þingmál', $issueId);

        if ($item->getElementsByTagName('xml')->item(0)) {
            $speechDom = $this->queryForDocument($item->getElementsByTagName('xml')->item(0)->nodeValue);
            $speechEl = $speechDom->getElementsByTagName('ræðutexti')->item(0);
            $speechBodyElement = $speechDocument->importNode($speechEl, true);
            $speechDocument->documentElement->appendChild($speechBodyElement);

            $issueEl = $speechDom->getElementsByTagName('mál')->item(0);

            if ($issueEl) {
                $issueElement = $speechDocument->importNode($issueEl, true);
                $speechDocument->documentElement->appendChild($issueElement);
            }
        }

        return $speechDocument;
    }
}
