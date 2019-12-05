<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Extractor;

class DocumentCommittee implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        $part = $object->getElementsByTagName('hluti')->length
            ? trim($object->getElementsByTagName('hluti')->item(0)->nodeValue)
            : null;
        $name = $object->getElementsByTagName('heiti')->item(0)
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null;

        return [
            'committee_id' => (int) $object->getAttribute('id'),
            'part' => $part,
            'name' => $name,
        ];
    }
}
