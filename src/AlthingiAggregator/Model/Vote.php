<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 10:34 AM
 */

namespace AlthingiAggregator\Model;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Vote implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->hasAttribute('málsnúmer')) {
            throw new ModelException('Missing [{málsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        if (!$object->hasAttribute('atkvæðagreiðslunúmer')) {
            throw new ModelException('Missing [{atkvæðagreiðslunúmer}] value', $object);
        }

        if (!$object->getElementsByTagName('tími')) {
            throw new ModelException('Missing [{tími}] value', $object);
        }

        if (!$object->getElementsByTagName('tegund')) {
            throw new ModelException('Missing [{tegund}] value', $object);
        }

        $xpath = new \DOMXPath($object->ownerDocument);

        //DOCUMENT
        $documentNodeList = $xpath->query('//atkvæðagreiðsla/þingskjal');
        $document = $documentNodeList->length > 0 && $documentNodeList->item(0)->hasAttribute('skjalsnúmer')
            ? $documentNodeList->item(0)->getAttribute('skjalsnúmer')
            : null;

        //OUTCOME
        $outcomeNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/niðurstaða');
        $outcome = $outcomeNodeList->length > 0
            ? trim($outcomeNodeList->item(0)->nodeValue)
            : null;

        //METHOD
        $methodNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/aðferð');
        $method = $methodNodeList->length > 0
            ? trim($methodNodeList->item(0)->nodeValue)
            : null;

        //YES
        $yesNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/já/fjöldi');
        $yes = $yesNodeList->length > 0
            ? $yesNodeList->item(0)->nodeValue
            : 0;
        //NO

        $noNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/nei/fjöldi');
        $no = $noNodeList->length > 0
            ? $noNodeList->item(0)->nodeValue
            : 0;

        //INACTION
        $inactionNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/greiðirekkiatkvæði/fjöldi');
        $inaction = $inactionNodeList->length > 0
            ? $inactionNodeList->item(0)->nodeValue
            : 0;

        $this->setIdentity((int) $object->getAttribute('atkvæðagreiðslunúmer'));

        return [
            'issue_id' => (int) $object->getAttribute('málsnúmer'),
            'assembly_id' => (int) $object->getAttribute('þingnúmer'),
            'vote_id' => (int) $object->getAttribute('atkvæðagreiðslunúmer'),
            'document_id' => (int) $document,
            'type' => trim($object->getElementsByTagName('tegund')->item(0)->nodeValue),
            'date' => date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('tími')->item(0)->nodeValue)),
            'outcome' => $outcome,
            'method' => $method,
            'yes' => (int) $yes,
            'no' => (int) $no,
            'inaction' => $inaction,
        ];
    }

    public function setIdentity($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
