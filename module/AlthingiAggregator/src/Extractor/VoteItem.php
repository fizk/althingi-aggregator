<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Extractor;

class VoteItem implements ExtractionInterface
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

        if (! $object->getElementsByTagName('atkvæði')->item(0)) {
            throw new Extractor\Exception('Missing [{atkvæði}] value', $object);
        }

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'vote' => trim($object->getElementsByTagName('atkvæði')->item(0)->nodeValue)
        ];
    }
}
