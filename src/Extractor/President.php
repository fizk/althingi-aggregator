<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class President implements ExtractionInterface
{
    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        if (! $object->getElementsByTagName('nafn')->item(0)) {
            throw new Extractor\Exception('Missing [{nafn}] value', $object);
        }

        if (! $object->getElementsByTagName('þing')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $object);
        }

        if (! $object->getElementsByTagName('inn')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $object);
        }

        $from = date(
            'Y-m-d',
            strtotime($object->getElementsByTagName('inn')->item(0)->nodeValue)
        );
        $to = ($object->getElementsByTagName('út')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('út')->item(0)->nodeValue))
            : null ;

        return [
            'assembly_id' => (int) $object->getElementsByTagName('þing')->item(0)->nodeValue,
            'congressman_id' => (int) $object->getAttribute('id'),
            'title' => $object->getElementsByTagName('embættisheiti')->item(0)
                ? $object->getElementsByTagName('embættisheiti')->item(0)->nodeValue
                : '',
            'attr' => $object->getElementsByTagName('skammstöfun')->item(0)
                ? $object->getElementsByTagName('skammstöfun')->item(0)->nodeValue
                : '',
            'from' => $from,
            'to' => $to
        ];
    }
}
