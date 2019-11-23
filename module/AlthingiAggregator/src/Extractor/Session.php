<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Extractor;

class Session implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
    {
        if (! $object->getElementsByTagName('inn')->item(0)) {
            throw new Extractor\Exception('Missing [{inn}] value', $object);
        }

        if (! $object->getElementsByTagName('þing')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $object);
        }

        if (! $object->getElementsByTagName('skammstöfun')->item(0)) {
            throw new Extractor\Exception('Missing [{skammstöfun}] value', $object);
        }

        if (! $object->getElementsByTagName('tegund')->item(0)) {
            throw new Extractor\Exception('Missing [{tegund}] value', $object);
        }

        if (! $object->getElementsByTagName('þingflokkur')->item(0)) {
            throw new Extractor\Exception('Missing [{þingflokkur}] value', $object);
        }

        if (! $object->getElementsByTagName('þingflokkur')->item(0)->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{þingflokkur.id}] value', $object);
        }

        if (! $object->getElementsByTagName('kjördæmi')->item(0)) {
            throw new Extractor\Exception('Missing [{kjördæmi}] value', $object);
        }

        if (! $object->getElementsByTagName('kjördæmi')->item(0)->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{kjördæmi.id}] value', $object);
        }

        if (! $object->getElementsByTagName('kjördæmanúmer')->item(0)) {
            throw new Extractor\Exception('Missing [{kjördæmanúmer}] value', $object);
        }

        if (! $object->getElementsByTagName('þingsalssæti')->item(0)) {
            throw new Extractor\Exception('Missing [{þingsalssæti}] value', $object);
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
        $division = (! $object->getElementsByTagName('deild')->item(0))
            ? null
            : trim($object->getElementsByTagName('deild')->item(0)->nodeValue);
        $from = date('Y-m-d', strtotime($object->getElementsByTagName('inn')->item(0)->nodeValue));
        $to = ($object->getElementsByTagName('út')->item(0) &&
            ! empty($object->getElementsByTagName('út')->item(0)->nodeValue))
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
