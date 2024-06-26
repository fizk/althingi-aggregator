<?php

namespace App\Extractor;

use App\Extractor;
use DOMElement;

class Session implements ExtractionInterface
{
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->getElementsByTagName('inn')->item(0)) {
            throw new Extractor\Exception('Missing [{inn}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('þing')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('skammstöfun')->item(0)) {
            throw new Extractor\Exception('Missing [{skammstöfun}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('tegund')->item(0)) {
            throw new Extractor\Exception('Missing [{tegund}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('þingflokkur')->item(0)) {
            throw new Extractor\Exception('Missing [{þingflokkur}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('þingflokkur')->item(0)->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{þingflokkur.id}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('kjördæmi')->item(0)) {
            throw new Extractor\Exception('Missing [{kjördæmi}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('kjördæmi')->item(0)->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{kjördæmi.id}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('kjördæmanúmer')->item(0)) {
            throw new Extractor\Exception('Missing [{kjördæmanúmer}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('þingsalssæti')->item(0)) {
            throw new Extractor\Exception('Missing [{þingsalssæti}] value', $this->object);
        }

        $id = (int) $this->object->getElementsByTagName('þing')->item(0)->nodeValue;
        $abbr = trim($this->object->getElementsByTagName('skammstöfun')->item(0)->nodeValue);
        $type = trim($this->object->getElementsByTagName('tegund')->item(0)->nodeValue);
        $party = trim($this->object->getElementsByTagName('þingflokkur')->item(0)->nodeValue);
        $partyId = (int) $this->object->getElementsByTagName('þingflokkur')->item(0)->getAttribute('id');
        $constituency = trim($this->object->getElementsByTagName('kjördæmi')->item(0)->nodeValue);
        $constituencyId = (int) $this->object->getElementsByTagName('kjördæmi')->item(0)->getAttribute('id');
        $constituencyNo = trim($this->object->getElementsByTagName('kjördæmanúmer')->item(0)->nodeValue);
        $seat = (empty($this->object->getElementsByTagName('þingsalssæti')->item(0)->nodeValue))
            ? null
            : trim($this->object->getElementsByTagName('þingsalssæti')->item(0)->nodeValue);
        $division = (! $this->object->getElementsByTagName('deild')->item(0))
            ? null
            : trim($this->object->getElementsByTagName('deild')->item(0)->nodeValue);
        $from = date('Y-m-d', strtotime($this->object->getElementsByTagName('inn')->item(0)->nodeValue));
        $to = ($this->object->getElementsByTagName('út')->item(0) &&
            ! empty($this->object->getElementsByTagName('út')->item(0)->nodeValue))
            ? date('Y-m-d', strtotime($this->object->getElementsByTagName('út')->item(0)->nodeValue))
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
