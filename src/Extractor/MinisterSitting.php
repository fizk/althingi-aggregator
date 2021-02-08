<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class MinisterSitting implements ExtractionInterface
{
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws Extractor\Exception
     */
    public function extract(): array
    {
        $assembly = $this->object->getElementsByTagName('þing')?->item(0)?->nodeValue;

        if (! $assembly) {
            throw new Extractor\Exception('Missing [{þing}] value', $this->object);
        }

        if (! $assembly) {
            throw new Extractor\Exception('Missing [{embætti}] value', $this->object);
        }

        $ministry = $this->object->getElementsByTagName('embætti')?->item(0)?->getAttribute('id');
        $party = $this->object->getElementsByTagName('þingflokkur')?->item(0)?->getAttribute('id');
        $from = $this->object->getElementsByTagName('inn')?->item(0)?->nodeValue;
        $to = $this->object->getElementsByTagName('út')?->item(0)?->nodeValue;

        if ($from) {
            $result = [];
            $match = preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/', $from, $result);
            $from = $match !== false ? "{$result[3]}-{$result[2]}-{$result[1]}" : null;
        }

        if ($to) {
            $result = [];
            $match = preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/', $to, $result);
            $to = $match !== false ? "{$result[3]}-{$result[2]}-{$result[1]}" : null;
        }

        return [
            'assembly_id' => $assembly ? (int) $assembly : null,
            'ministry_id' => $ministry ? (int) $ministry : null,
            'party_id' => $party ? (int) $party : null,
            'from' => $from,
            'to' => $to,
        ];
    }
}
