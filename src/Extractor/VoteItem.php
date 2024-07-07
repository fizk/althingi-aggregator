<?php

namespace App\Extractor;

use App\Extractor;
use DOMElement;

class VoteItem implements ExtractionInterface
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
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('atkvæði')->item(0)) {
            throw new Extractor\Exception('Missing [{atkvæði}] value', $this->object);
        }

        return [
            'congressman_id' => (int) $this->object->getAttribute('id'),
            'vote' => trim($this->object->getElementsByTagName('atkvæði')->item(0)->nodeValue)
        ];
    }
}
