<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class MinisterSitting implements ExtractionInterface
{
    /**
     * @throws Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        $assembly = $object->getElementsByTagName('þing')->length === 1
            ? (int) $object->getElementsByTagName('þing')->item(0)->nodeValue
            : null;
        if (! $assembly) {
            throw new Extractor\Exception('Missing [{þing}] value', $object);
        }

        $ministry = $object->getElementsByTagName('embætti')->length === 1
            ? (int) $object->getElementsByTagName('embætti')->item(0)->getAttribute('id')
            : null;
        if (! $assembly) {
            throw new Extractor\Exception('Missing [{embætti}] value', $object);
        }

        $party = $object->getElementsByTagName('þingflokkur')->length === 1
            ? (int) $object->getElementsByTagName('þingflokkur')->item(0)->getAttribute('id')
            : null;

        $from = $object->getElementsByTagName('inn')->length === 1
            ? $object->getElementsByTagName('inn')->item(0)->nodeValue
            : null;
        $to = $object->getElementsByTagName('út')->length === 1
            ? $object->getElementsByTagName('út')->item(0)->nodeValue
            : null ;

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
            'ministry_id' => $ministry,
            'party_id' => $party,
            'from' => $from,
            'to' => $to,
        ];
    }
}
