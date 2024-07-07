<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Category implements ExtractionInterface, IdentityInterface
{
    private string $id;
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

        $this->setIdentity($this->object->getAttribute('id'));

        return [
            'id' => (int) $this->getIdentity(),
            'title' => $this->object->getElementsByTagName('heiti')?->item(0)?->nodeValue,
            'description' => $this->object->getElementsByTagName('lýsing')?->item(0)?->nodeValue,
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
