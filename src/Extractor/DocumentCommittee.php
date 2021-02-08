<?php
namespace App\Extractor;

use App\Extractor;
use DOMElement;

class DocumentCommittee implements ExtractionInterface
{
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        $part = $this->object->getElementsByTagName('hluti')?->item(0)?->nodeValue;
        $name = $this->object->getElementsByTagName('heiti')?->item(0)?->nodeValue;

        return [
            'committee_id' => (int) $this->object->getAttribute('id'),
            'part' => $part ? trim($part) : null,
            'name' => $name ? trim($name) : null,
        ];
    }
}
