<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class DocumentCommittee implements ExtractionInterface
{
    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
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
