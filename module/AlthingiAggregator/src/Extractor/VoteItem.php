<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Extractor\Exception as ModelException;

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
            throw new ModelException('Missing [{id}] value', $object);
        }

        if (! $object->getElementsByTagName('atkvæði')->item(0)) {
            throw new ModelException('Missing [{atkvæði}] value', $object);
        }

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'vote' => trim($object->getElementsByTagName('atkvæði')->item(0)->nodeValue)
        ];
    }
}
