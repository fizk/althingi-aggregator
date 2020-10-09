<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class VoteItem implements ExtractionInterface
{
    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
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
