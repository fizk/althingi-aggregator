<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Proponent implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        if (! $object->hasAttribute('röð')) {
            throw new Extractor\Exception('Missing [{röð}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));

        $minister = $object->getElementsByTagName('ráðherra')->length
            ? trim($object->getElementsByTagName('ráðherra')->item(0)->nodeValue)
            : null;

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'order' => (int) $object->getAttribute('röð'),
            'minister' => $minister
        ];
    }

    public function setIdentity(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
