<?php
namespace AlthingiAggregator\Extractor;

use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class CommitteeSitting implements ExtractionInterface
{
    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     */
    public function extract(\DOMElement $object)
    {
        $role = $object->getElementsByTagName('staða')->length === 1
            ? $object->getElementsByTagName('staða')->item(0)->nodeValue
            : null;
        $order = $object->getElementsByTagName('röð')->length === 1
            ? $object->getElementsByTagName('röð')->item(0)->nodeValue
            : 0;
        $from = $object->getElementsByTagName('inn')->length === 1
            ? $object->getElementsByTagName('inn')->item(0)->nodeValue
            : null;
        $to = $object->getElementsByTagName('út')->length === 1
            ? $object->getElementsByTagName('út')->item(0)->nodeValue
            : null ;
        $committee = $object->getElementsByTagName('nefnd')->item(0)->getAttribute('id');
        $assembly = $object->getElementsByTagName('þing')->item(0)->nodeValue;

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
            'order' => $order,
            'from' => $from,
            'to' => $to,
        ];
    }
}
