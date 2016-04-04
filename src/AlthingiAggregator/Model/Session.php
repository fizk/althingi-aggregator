<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Model;

use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Session implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  \DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->getElementsByTagName('inn')->item(0)) {
            throw new ModelException('Missing [{inn}] value', $object);
        }

        if (!$object->getElementsByTagName('þing')->item(0)) {
            throw new ModelException('Missing [{þing}] value', $object);
        }

        if (!$object->getElementsByTagName('skammstöfun')->item(0)) {
            throw new ModelException('Missing [{skammstöfun}] value', $object);
        }

        if (!$object->getElementsByTagName('tegund')->item(0)) {
            throw new ModelException('Missing [{tegund}] value', $object);
        }

        if (!$object->getElementsByTagName('þingflokkur')->item(0)) {
            throw new ModelException('Missing [{þingflokkur}] value', $object);
        }

        if (!$object->getElementsByTagName('þingflokkur')->item(0)->hasAttribute('id')) {
            throw new ModelException('Missing [{þingflokkur.id}] value', $object);
        }

        if (!$object->getElementsByTagName('kjördæmi')->item(0)) {
            throw new ModelException('Missing [{kjördæmi}] value', $object);
        }

        if (!$object->getElementsByTagName('kjördæmi')->item(0)->hasAttribute('id')) {
            throw new ModelException('Missing [{kjördæmi.id}] value', $object);
        }

        if (!$object->getElementsByTagName('kjördæmanúmer')->item(0)) {
            throw new ModelException('Missing [{kjördæmanúmer}] value', $object);
        }

        if (!$object->getElementsByTagName('þingsalssæti')->item(0)) {
            throw new ModelException('Missing [{þingsalssæti}] value', $object);
        }

        $id = (int) $object->getElementsByTagName('þing')->item(0)->nodeValue;
        $abbr = trim($object->getElementsByTagName('skammstöfun')->item(0)->nodeValue);
        $type = trim($object->getElementsByTagName('tegund')->item(0)->nodeValue);
        $party = trim($object->getElementsByTagName('þingflokkur')->item(0)->nodeValue);
        $partyId = (int) $object->getElementsByTagName('þingflokkur')->item(0)->getAttribute('id');
        $constituency = trim($object->getElementsByTagName('kjördæmi')->item(0)->nodeValue);
        $constituencyId = (int) $object->getElementsByTagName('kjördæmi')->item(0)->getAttribute('id');
        $constituencyNo = trim($object->getElementsByTagName('kjördæmanúmer')->item(0)->nodeValue);
        $seat = (empty($object->getElementsByTagName('þingsalssæti')->item(0)->nodeValue))
            ? null
            : trim($object->getElementsByTagName('þingsalssæti')->item(0)->nodeValue);
        $division = (!$object->getElementsByTagName('deild')->item(0))
            ? null
            : trim($object->getElementsByTagName('deild')->item(0)->nodeValue);
        $from = date('Y-m-d', strtotime($object->getElementsByTagName('inn')->item(0)->nodeValue));
        $to = ($object->getElementsByTagName('út')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('út')->item(0)->nodeValue))
            : null;

        return [
            'id' => $id,
            'assembly_id' => $id,
            'abbr' => $abbr,
            'type' => $type,
            'party' => $party,
            'party_id' => $partyId,
            'constituency' => $constituency,
            'constituency_id' => $constituencyId,
            'constituency_no' => $constituencyNo,
            'seat' => $seat,
            'division' => $division,
            'from' => $from,
            'to' => $to,
        ];
    }
}
