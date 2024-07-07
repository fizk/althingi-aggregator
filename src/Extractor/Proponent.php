<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Proponent implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        if (! $this->object->hasAttribute('röð')) {
            throw new Extractor\Exception('Missing [{röð}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('id'));

        $minister = $this->object->getElementsByTagName('ráðherra')?->item(0)?->nodeValue;

        return [
            'congressman_id' => (int) $this->object->getAttribute('id'),
            'order' => (int) $this->object->getAttribute('röð'),
            'minister' => $minister ? trim($minister) : null
        ];
    }

    public function setIdentity(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
