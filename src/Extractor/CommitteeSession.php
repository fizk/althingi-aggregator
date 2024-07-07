<?php

namespace App\Extractor;

use DOMElement;

class CommitteeSession implements ExtractionInterface
{
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        $role = $this->object->getElementsByTagName('staða')?->item(0)?->nodeValue;
        $order = $this->object->getElementsByTagName('röð')?->item(0)?->nodeValue;
        $from = $this->object->getElementsByTagName('inn')?->item(0)?->nodeValue;
        $to = $this->object->getElementsByTagName('út')?->item(0)?->nodeValue;
        $committee = $this->object->getElementsByTagName('nefnd')?->item(0)?->getAttribute('id');
        $assembly = $this->object->getElementsByTagName('þing')?->item(0)?->nodeValue;

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
            'assembly_id' => $assembly,
            'committee_id' => $committee,
            'role' => $role,
            'order' => $order ? $order : 0,
            'from' => $from,
            'to' => $to,
        ];
    }
}
